<?php

namespace App\Http\Controllers;

use App\Models\Articles;
use App\Models\Stocks;
use App\Models\StockUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StocksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::check()) {
            $stocks = Stocks::where('client_id', "=", Auth::user()->id)->get();
            return view('stocks.stocks', compact('stocks'));
        } else {
            return view('auth.login');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $roles = [
            'quantite' => 'nullable',
            'code' => 'nullable|unique:stocks,code_stock,',
            'fichier' => 'nullable|mimes:xlsx,xls,csv|max:2048',
        ];
        $customMessages = [
            'fichier.mimes' => "Le fichier doit être un fichier de type : xlsx, xls, ou csv.",
            'fichier.max' => "La taille du fichier ne doit pas dépasser 2 Mo.",
        ];
        $request->validate($roles, $customMessages);

        // Vérifie si un fichier a été uploadé
        if ($request->hasFile('fichier')) {
            $file = $request->file('fichier');

            // Utiliser Maatwebsite\Excel pour lire le fichier
            $data = \Maatwebsite\Excel\Facades\Excel::toArray([], $file);

            // Vérifie si des données sont disponibles dans le fichier
            if (empty($data) || count($data[0]) === 0) {
                return back()->withErrors(["Le fichier est vide ou mal formaté."]);
            }

            $rows = $data[0];

            $errors = [];
            $successCount = 0;
            $invalidStocks = [];  // Tableau pour suivre les codes de stock invalides
            $updatedStocks = [];  // Initialisation de la variable pour suivre les stocks mis à jour

            // Récupérer tous les stocks existants pour ce client
            $existingStocks = Stocks::where('client_id', Auth::user()->id)
                ->pluck('code_stock')
                ->toArray(); // Tableau des codes de stock existants pour ce client

            // Récupérer tous les codes articles valides dans la table 'articles'
            $validArticleCodes = Articles::pluck('code_article')->toArray();

            foreach ($rows as $index => $row) {
                // Ignore les lignes vides ou mal formatées
                if (empty($row[0]) || empty($row[1])) {
                    continue;
                }

                // Récupère les colonnes du fichier
                $code_stock = $row[0];
                $quantite_initiale = $row[1];

                // Vérifier si le code_stock existe dans les articles
                if (!in_array($code_stock, $validArticleCodes)) {
                    // Si le code_stock n'existe pas dans les articles, ajouter à la liste des invalides
                    $invalidStocks[] = $code_stock;
                    continue; // Passer à la ligne suivante si ce code_stock est invalide
                }

                // Vérifier si le stock existe déjà en base pour le client
                $stock = Stocks::where('code_stock', $code_stock)
                    ->where('client_id', Auth::user()->id)
                    ->first();

                // Si le stock existe, on met à jour la quantité
                if ($stock) {
                    // Sauvegarde de la quantité avant la mise à jour
                    $quantite_avant = $stock->quantite_initiale;

                    // Mise à jour du stock
                    $stock->update([
                        'quantite_initiale' => $quantite_initiale,
                    ]);

                    // Enregistrement dans la table de suivi
                    StockUpdate::create([
                        'client_id' => Auth::user()->id,
                        'code_stock' => $code_stock,
                        'action' => 'updated',
                        'quantite_avant' => $quantite_avant,
                        'quantite_apres' => $quantite_initiale,
                    ]);

                    // Ajouter le code stock mis à jour au tableau
                    $updatedStocks[] = $code_stock;
                } else {
                    // Si le stock n'existe pas, on crée un nouveau stock
                    $stock = Stocks::create([
                        'code_stock' => $code_stock,
                        'quantite_initiale' => $quantite_initiale,
                        'client_id' => Auth::user()->id,
                    ]);

                    // Enregistrement dans la table de suivi
                    StockUpdate::create([
                        'client_id' => Auth::user()->id,
                        'code_stock' => $code_stock,
                        'action' => 'created',
                        'quantite_avant' => null,  // Pas de quantité avant pour la création
                        'quantite_apres' => $quantite_initiale,
                    ]);

                    // Ajouter le code stock mis à jour au tableau
                    $updatedStocks[] = $code_stock;
                }

                $successCount++;
            }

            // Maintenant, mettre les stocks existants non mis à jour à 0
            // Les stocks existants mais non mis à jour
            $stocksToUpdateToZero = array_diff($existingStocks, $updatedStocks);

            // Mettre à jour ces stocks à 0
            Stocks::where('client_id', Auth::user()->id)
                ->whereIn('code_stock', $stocksToUpdateToZero)
                ->update(['quantite_initiale' => 0]);

            // Retourne les résultats de l'importation
            if ($successCount > 0) {
                $message = $successCount . " stocks ont été importés ou mis à jour avec succès.";

                // Ajouter les codes invalides à la réponse si des erreurs existent
                if (count($invalidStocks) > 0) {
                    $message .= "<br>Les codes de stock suivants ne sont pas valides : " . implode(", ", $invalidStocks);
                }

                return back()->with('succes', $message);
            }

            return back()->withErrors($errors);
        } else {

            return back()->withErrors(["Importer un fichier de type : xlsx, xls, ou csv dont la taille du fichier ne doit pas dépasser 2 Mo."]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Stocks::findOrFail($id);

        $roles = [
            'quantite' => 'required',
            'code' => 'required|unique:stocks,code_stock,' . $user->id,
        ];
        $customMessages = [
            'quantite' => "Saisissez son nom",
            'code.unique' => "Le code est déjà utilisé. Veuillez essayer un autre!",
        ];
        $request->validate($roles, $customMessages);

        $user->quantite_initiale = $request->quantite;
        if ($user->code_stock !== $request->code) {
            $user->code_stock = $request->code;
        }

        if ($user->save()) {
            return back()->with('succes', "Les informations de " . $request->code . " ont été mises à jour avec succès.");
        } else {
            return back()->withErrors(["Impossible de mettre à jour les informations de " . $request->code . ". Veuillez réessayer!"]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Stocks::findOrFail($id)->delete();

        return back()->with('succes', "La suppression a été effectué");
    }
}

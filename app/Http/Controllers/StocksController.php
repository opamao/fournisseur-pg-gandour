<?php

namespace App\Http\Controllers;

use App\Exports\StocksExport;
use App\Models\Articles;
use App\Models\Clients;
use App\Models\Stocks;
use App\Models\StockUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class StocksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::check()) {
            $stocks = Stocks::where('client_id', "=", Auth::user()->id)
                ->get();

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
            'fichier' => 'nullable|mimes:xlsx,xls,csv|max:2048',
        ];
        $customMessages = [
            'fichier.mimes' => __("messages.fileMine"),
            'fichier.max' => __("messages.fileMax"),
        ];
        $request->validate($roles, $customMessages);

        // Vérifie si un fichier a été uploadé
        if ($request->hasFile('fichier')) {
            $file = $request->file('fichier');

            // Utiliser Maatwebsite\Excel pour lire le fichier
            $data = \Maatwebsite\Excel\Facades\Excel::toArray([], $file);

            // Vérifie si des données sont disponibles dans le fichier
            if (empty($data) || count($data[0]) === 0) {
                return back()->withErrors([__("messages.fileEmpty")]);
            }

            $rows = $data[0];

            // Supprimer la première ligne (les en-têtes ou la ligne à ignorer)
            array_shift($rows); // Cela supprime la première ligne du fichier (index 0)

            // Initialisation des variables
            $errors = [];
            $invalidStocks = [];  // Tableau pour suivre les codes de stock invalides
            $invalidQuantities = [];  // Tableau pour suivre les quantités invalides
            $validRows = []; // Tableau pour stocker les lignes valides avant l'insertion

            // Récupérer tous les codes articles valides dans la table 'articles'
            $validArticleCodes = Articles::pluck('code_article')->toArray();

            foreach ($rows as $index => $row) {
                // Vérifie si la ligne est vide ou mal formatée
                if (empty($row[0])) {
                    $errors[] = __("messages.theline") . ($index + 2) . __("messages.stockVide");
                    continue; // Passer à la ligne suivante si le code de stock est vide
                }

                // Récupère les colonnes du fichier
                $code_stock = $row[0];
                $quantite_initiale = $row[2];

                // Vérifier si le code_stock existe dans les articles
                if (!in_array($code_stock, $validArticleCodes)) {
                    // Si le code_stock n'existe pas dans les articles, ajouter à la liste des erreurs
                    // $invalidStocks[] = "La ligne " . ($index + 2) . " a un code de stock invalide : " . $code_stock;
                    $invalidStocks[] = __("messages.theline") . ($index + 2) . __("messages.StockInv");
                    continue; // Passer à la ligne suivante si ce code_stock est invalide
                }

                // Vérifier si la quantité est bien un nombre
                if (empty($quantite_initiale)) {
                    $quantite_initiale = 0; // Remplacer les quantités vides par 0
                } elseif (!is_numeric($quantite_initiale)) {
                    // Si la quantité n'est pas un nombre valide, ajouter à la liste des erreurs
                    $invalidQuantities[] = __("messages.theline") . ($index + 2) . __("messages.qteInv");
                    // $invalidQuantities[] = "La ligne " . ($index + 2) . " a une quantité invalide : " . $quantite_initiale;
                    continue; // Passer à la ligne suivante si la quantité est invalide
                } elseif ($quantite_initiale < 0) {
                    // Vérifier si la quantité est négative
                    $invalidQuantities[] = __("messages.theline") . ($index + 2) . __("messages.qteNeg");
                    continue; // Passer à la ligne suivante si la quantité est négative
                }

                // Ajouter la ligne valide au tableau pour insertion
                $validRows[] = [
                    'code_stock' => $code_stock,
                    'quantite_initiale' => $quantite_initiale
                ];
            }

            // Si des erreurs existent, retourner la liste des erreurs sans faire l'insertion
            if (count($invalidStocks) > 0 || count($invalidQuantities) > 0 || count($errors) > 0) {
                // Construction du message d'erreur
                $errorMessage = '';
                if (count($invalidStocks) > 0) {
                    $errorMessage .= implode(" ", $invalidStocks);
                    // $errorMessage .= "Les codes de stock suivants ne sont pas valides : <br>" . implode("<br>", $invalidStocks) . "<br>";
                }

                if (count($invalidQuantities) > 0) {
                    $errorMessage .= implode(" ", $invalidQuantities);
                    // $errorMessage .= "Les quantités suivantes ne sont pas valides (non numériques) : <br>" . implode("<br>", $invalidQuantities) . "<br>";
                }

                if (count($errors) > 0) {
                    $errorMessage .= implode(" ", $errors);
                }

                return response()->json(['errors' => $errorMessage], 422);
            }

            // Si toutes les vérifications sont réussies, on procède à l'insertion ou la mise à jour des stocks
            $existingStocks = Stocks::where('client_id', Auth::user()->id)
                ->pluck('code_stock')
                ->toArray(); // Tableau des codes de stock existants pour ce client

            $updatedStocks = []; // Tableau pour suivre les stocks mis à jour

            foreach ($validRows as $row) {
                $code_stock = $row['code_stock'];
                $quantite_initiale = $row['quantite_initiale'];

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
                    Stocks::create([
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
            }

            // Mettre les stocks existants non mis à jour à 0
            $stocksToUpdateToZero = array_diff($existingStocks, $updatedStocks);

            // Mettre à jour ces stocks à 0
            Stocks::where('client_id', Auth::user()->id)
                ->whereIn('code_stock', $stocksToUpdateToZero)
                ->update(['quantite_initiale' => 0]);

            // Retourne les résultats de l'importation
            $message = count($validRows) . " stocks" . __("messages.fileImport");

            return response()->json(['success' => $message]);
        } else {
            return response()->json(['errors' => __("messages.fileImportEx")], 422);
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
            // 'code' => 'required|unique:stocks,code_stock,' . $user->id,
        ];
        $customMessages = [
            'quantite' => __("messages.enterquantite"),
            // 'code.unique' => __("messages.enterCodeUse"),
        ];
        $request->validate($roles, $customMessages);

        StockUpdate::create([
            'client_id' => Auth::user()->id,
            'code_stock' => $request->code,
            'action' => 'updated',
            'quantite_avant' => $user->quantite_initiale,
            'quantite_apres' => $request->quantite,
        ]);

        $user->quantite_initiale = $request->quantite;
        // if ($user->code_stock !== $request->code) {
        //     $user->code_stock = $request->code;
        // }

        if ($user->save()) {
            return back()->with('succes', __("messages.update"));
        } else {
            return back()->withErrors([__("messages.impossible")]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Stocks::findOrFail($id)->update(['quantite_initiale' => 0]);

        return back()->with('succes', __("messages.stockzero"));
    }

    public function editPassword(Request $request)
    {
        $roles = [
            'code' => 'required',
            'codenew' => 'required',
            'codeconfirm' => 'required',
        ];
        $customMessages = [
            'code.required' => __("messages.passwordActuel"),
            'codenew.required' => __("messages.codenew"),
            'codeconfirm.required' => __("messages.codeconfirm"),
        ];
        $request->validate($roles, $customMessages);

        if ($request->codenew == $request->codeconfirm) {

            $user = Clients::where('username', Auth::user()->username)->first();

            if ($user && !Hash::check($request->codenew, $user->password_client)) {

                Clients::where('id', Auth::user()->id)
                    ->update([
                        'password_client' => Hash::make($request->codenew),
                    ]);

                return back()->with('succes', __("messages.updatePassword"));
            } else {
                return back()->withErrors([__("messages.updateActuel")]);
            }
        } else {
            return back()->withErrors([__("messages.updateConfirm")]);
        }
    }

    public function exportStock()
    {
        return Excel::download(new StocksExport, 'stock_' . Auth::user()->username . now() . '.xlsx');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Stocks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StocksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stocks = Stocks::where('client_id', "=", Auth::user()->id)->get();
        return view('stocks.stocks', compact('stocks'));
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

            foreach ($rows as $index => $row) {
                // Ignore les lignes vides ou mal formatées
                if (empty($row[0]) || empty($row[1]) || empty($row[2])) {
                    continue;
                }

                // Récupère les colonnes du fichier
                $code_stock = $row[0];
                $quantite_initiale = $row[1];

                // Création du client
                Stocks::create([
                    'code_stock' => $code_stock,
                    'quantite_initiale' => $quantite_initiale,
                    'client_id' => Auth::user()->id,
                ]);

                $successCount++;
            }

            // Retourne les résultats de l'importation
            if ($successCount > 0) {
                return back()->with('succes',  $successCount . " clients ont été importés avec succès.");
            }

            return back()->withErrors($errors);
        } else {
            $user = new Stocks();
            $user->code_stock = $request->code;
            $user->quantite_initiale = $request->quantite;
            $user->client_id = Auth::user()->id;

            if ($user->save()) {
                return back()->with('succes',  "Vous avez ajouter " . $request->code);
            } else {
                return back()->withErrors(["Impossible d'ajouter " . $request->code . ". Veuillez réessayer!!"]);
            }
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

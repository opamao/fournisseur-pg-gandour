<?php

namespace App\Http\Controllers;

use App\Models\Stocks;
use Illuminate\Http\Request;

class StocksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stocks = Stocks::all();
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
            'quantite' => 'required',
            'code' => 'required|unique:stocks,code_stock,',
        ];
        $customMessages = [
            'quantite' => "Saisissez la quantité",
            'code.unique' => "Le code est déjà utilisée. Veuillez essayer une autre!",
        ];
        $request->validate($roles, $customMessages);

        $user = new Stocks();
        $user->code_stock = $request->code;
        $user->quantite_initiale = $request->quantite;
        $user->client_id = 1;

        if ($user->save()) {
            return back()->with('succes',  "Vous avez ajouter " . $request->code);
        } else {
            return back()->withErrors(["Impossible d'ajouter " . $request->code . ". Veuillez réessayer!!"]);
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

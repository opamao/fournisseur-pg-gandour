<?php

namespace App\Http\Controllers;

use App\Models\Clients;
use App\Models\Stocks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class CustomAuthController extends Controller
{
    public function index()
    {
        return view("auth.login");
    }

    public function customLogin(Request $request)
    {
        $roles = [
            'email' => 'required',
            'password' => 'required',
        ];
        $customMessages = [
            'email.required' => "Veuillez saisir votre code",
            'password.required' => "Veuillez saisir cotre mot de passe",
        ];
        $request->validate($roles, $customMessages);

        $credentials = $request->only('email', 'password');

        $user = Clients::where('username', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password_client)) {
            if ($user->status_client == 0) {
                return back()->withInput()->withErrors(["Votre compte n'est pas accéssible."]);
            } else {
                // Lorque les paramètres sont valides, garde les informations dans la session
                Auth::login($user);

                return redirect()->intended('index')->withSuccess('Bon retour');
            }
        } else {
            // Les identifiants ne sont pas valides
            return back()->withInput()->withErrors(['E-mail ou mot de passe incorrect']);
        }
    }

    public function dashboard()
    {
        if (Auth::check()) {
            $nbreStock = Stocks::where('client_id', '=', Auth::user()->id)->count();
            $totalStock = Stocks::where('client_id', '=', Auth::user()->id)->sum('quantite_initiale');

            return view('dashboard.dashboard', compact('totalStock', 'nbreStock'));
        } else {
            return view('auth.login');
        }
    }

    public function signOut()
    {
        Session::flush();
        Auth::logout();

        return Redirect('/');
    }
}

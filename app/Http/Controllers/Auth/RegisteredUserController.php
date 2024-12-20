<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\welcomeMail;
use App\Models\User;
use App\Models\verifyToken;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validation des données
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'commune' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);
    
        // Création de l'utilisateur
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'commune' => $validated['commune'],
            'password' => bcrypt($validated['password']),
        ]);
    
        // Redirection vers la page de connexion avec un message de succès
        return redirect()->route('login')->with('success', 'Votre compte a été créé avec succès. Connectez-vous.');
    }
}

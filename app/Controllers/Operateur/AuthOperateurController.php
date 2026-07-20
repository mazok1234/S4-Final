<?php

namespace App\Controllers\Operateur;

use App\Controllers\BaseController;

class AuthOperateurController extends BaseController
{
    // Identifiants codés en dur (côté opérateur = "Admin" uniquement)
    private const ADMIN_USERNAME = 'Admin';
    private const ADMIN_PASSWORD = 'Admin'; // mot de passe par défaut — à changer en prod

    /**
     * Affiche la page de connexion opérateur
     */
    public function index(): string
    {
        // Redirige si déjà connecté en tant qu'opérateur
        if (session()->get('isOperator')) {
            return redirect()->to('/operator/dashboard');
        }

        return view('operator/login');
    }

    /**
     * Traite la soumission du formulaire de connexion opérateur
     */
    public function login()
    {
        $username = trim((string) $this->request->getPost('username'));
        $password = trim((string) $this->request->getPost('password'));

        // Validation des champs
        if (empty($username) || empty($password)) {
            return redirect()->back()->with('error', 'Veuillez remplir tous les champs.');
        }

        // Vérification des identifiants (uniquement "Admin")
        if ($username !== self::ADMIN_USERNAME || $password !== self::ADMIN_PASSWORD) {
            // Petit délai pour limiter les attaques par force brute
            sleep(1);
            return redirect()->back()->with('error', 'Identifiant ou mot de passe incorrect.');
        }

        // Création de la session opérateur
        session()->set([
            'isOperator'       => true,
            'operator_username' => $username,
        ]);

        return redirect()->to('/operator/dashboard')->with('success', 'Bienvenue, ' . $username . ' !');
    }

    /**
     * Déconnecte l'opérateur
     */
    public function logout()
    {
        session()->remove(['isOperator', 'operator_username']);
        return redirect()->to('/operator/login')->with('success', 'Vous avez été déconnecté.');
    }
}

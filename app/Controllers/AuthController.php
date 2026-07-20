<?php

namespace App\Controllers;

use App\Models\ClientModel;

class AuthController extends BaseController
{
    protected $clientModel;

    public function __construct(){
        $this->clientModel = new ClientModel();
    }
    /**
     * Affiche la page de connexion (Vue HTML)
     */
    public function index(): string
    {  

        // Redirige directement si le client est déjà connecté
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/client/test');
        }

        return view('front_office/login');
    }

    /**
     * Traite la soumission du formulaire de connexion
     */
    public function login()
    {
        $telephone = $this->request->getPost('telephone');

        // 1. Nettoyage et formatage simple de l'entrée
        $telephone = trim((string)$telephone);

        // 2. Validation basique de la présence de la donnée
        if (empty($telephone)) {
            return redirect()->back()->with('error', 'Veuillez saisir un numéro de téléphone.');
        }

       

        // 3. Récupération ou création automatique du client via le modèle
        // try {
            $client = $this->clientModel->getOrCreateByTelephone($telephone);
        // } catch (\Exception $e) {
        //     return redirect()->back()->with('error', 'Une erreur est survenue lors du traitement.');
        // }

        // 4. Mise en place de la session client
        $sessionData = [
            'client_id'  => $client['id'],
            'telephone'  => $client['telephone'],
            'isLoggedIn' => true,
        ];

        session()->set($sessionData);

        // 5. Redirection vers l'espace fonctionnel client
        return redirect()->to('/client/test')->with('success', 'Connexion réussie !');
        // $data = [
        //     'status'  => 'success',
        //     'message' => 'Connexion réussie',
        //     'client'  => [
        //         'id'        => 1,
        //         'telephone' => $telephone
        //     ]
        // ];

        // // Définit le header Content-Type: application/json et encode les données
        // return $this->response->setJSON($data);
    }

    /**
     * Déconnecte le client et détruit la session
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Vous avez été déconnecté.');
    }
    public function dashboard(){
        return view("front_office/home");
    }
}
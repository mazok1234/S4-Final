<?php

namespace App\Controllers;

use App\Models\ClientModel;

class ClientController extends BaseController
{
    protected $clientModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
    }

    /**
     * Vérification d'authentification
     */
    private function checkAuth()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }
        return null;
    }

    /**
     * Traitement de la demande de Dépôt
     */
    public function depot()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $clientId = (int) session()->get('client_id');
        $montant  = (float) $this->request->getPost('montant');

        try {
            if ($this->clientModel->depot($clientId, $montant)) {
                return redirect()->to('/client/historique')->with('success', 'Dépôt de ' . number_format($montant, 2) . ' Ar effectué avec succès.');
            }
            return redirect()->to('/client/historique')->with('error', 'Échec du traitement du dépôt.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('/client/historique')->with('error', $e->getMessage());
        }
    }

    /**
     * Traitement de la demande de Retrait
     */
    public function retrait()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $clientId = (int) session()->get('client_id');
        $montant  = (float) $this->request->getPost('montant');

        try {
            if ($this->clientModel->retrait($clientId, $montant)) {
                return redirect()->to('/client/historique')->with('success', 'Retrait de ' . number_format($montant, 2) . ' Ar effectué avec succès.');
            }
            return redirect()->to('/client/historique')->with('error', 'Échec du traitement du retrait.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('/client/historique')->with('error', $e->getMessage());
        }
    }

    /**
     * Traitement de la demande de Transfert
     */
    public function transfert()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $clientIdSource = (int) session()->get('client_id');
        $telephoneDest  = (string) $this->request->getPost('destinataire');
        $montant        = (float) $this->request->getPost('montant');

        try {
            if ($this->clientModel->transfert($clientIdSource, $telephoneDest, $montant)) {
                return redirect()->to('/client/historique')->with('success', "Transfert de " . number_format($montant, 2) . " Ar vers le {$telephoneDest} effectué avec succès.");
            }
            return redirect()->to('/client/historique')->with('error', 'Échec du traitement du transfert.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('/client/historique')->with('error', $e->getMessage());
        }
    }

    /**
     * Affichage du tableau de bord avec les formulaires et l'historique
     */
    public function historique()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $clientId  = (int) session()->get('client_id');
        $telephone = (string) session()->get('telephone');

        $data = $this->clientModel->getHistoriqueData($clientId, $telephone);

        return view('front_office/historique', $data);
    }
}
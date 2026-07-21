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
        } catch (\Exception $e) {
            log_message('error', 'Erreur dépôt: ' . $e->getMessage());
            return redirect()->to('/client/historique')->with('error', 'Échec du dépôt : ' . $e->getMessage());
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
        } catch (\Exception $e) {
            log_message('error', 'Erreur retrait: ' . $e->getMessage());
            return redirect()->to('/client/historique')->with('error', 'Échec du retrait : ' . $e->getMessage());
        }
    }

    /**
     * Traitement de la demande de Transfert
     */
    public function transfert()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $clientIdSource = (int) session()->get('client_id');
        $telephoneDest  = $this->request->getPost('destinataire'); // Ce sera un tableau PHP
        $montant        = (float) $this->request->getPost('montant');

        // S'assurer que c'est bien un tableau
        if (!is_array($telephoneDest)) {
            $telephoneDest = preg_split('/[\s,;]+/', (string)$telephoneDest, -1, PREG_SPLIT_NO_EMPTY);
        }

        // Nettoyer les entrées
        $telephonesDestClean = [];
        foreach ($telephoneDest as $tel) {
            $cleaned = trim((string)$tel);
            if ($cleaned !== '') {
                $telephonesDestClean[] = $cleaned;
            }
        }
        $telephonesDestClean = array_unique($telephonesDestClean);

        try {
            $nbDest = count($telephonesDestClean);

            if ($nbDest === 1) {
                // Transfert simple : supporte les destinataires d'un autre opérateur
                $dest = reset($telephonesDestClean);
                if ($this->clientModel->transfert($clientIdSource, $dest, $montant)) {
                    $label = "Transfert de " . number_format($montant, 2) . " Ar vers {$dest} effectué avec succès (frais calculés automatiquement selon l'opérateur).";
                    return redirect()->to('/client/historique')->with('success', $label);
                }
                return redirect()->to('/client/historique')->with('error', 'Échec du traitement du transfert.');
            } else {
                // Transfert multiple : même opérateur uniquement
                if ($this->clientModel->transfertMultiple($clientIdSource, $telephonesDestClean, $montant)) {
                    $montantIndiv = $montant / $nbDest;
                    $destList     = implode(', ', $telephonesDestClean);
                    $label = "Transfert multiple de " . number_format($montant, 2) . " Ar divisé vers {$nbDest} destinataires (" . number_format($montantIndiv, 2) . " Ar chacun à {$destList}).";
                    return redirect()->to('/client/historique')->with('success', $label);
                }
                return redirect()->to('/client/historique')->with('error', 'Échec du traitement du transfert.');
            }
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('/client/historique')->with('error', $e->getMessage());
        } catch (\Exception $e) {
            log_message('error', 'Erreur transfert: ' . $e->getMessage());
            return redirect()->to('/client/historique')->with('error', 'Échec du transfert : ' . $e->getMessage());
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
    public function epargne()
    {
        
    }
}
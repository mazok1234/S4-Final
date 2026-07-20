<?php

namespace App\Controllers\operateur;

use App\Controllers\BaseController;
use App\Models\Operateur\PrefixeModel;
use App\Models\Operateur\PrefixeAutreModel;
use App\Models\Operateur\BaremeFraisModel;
use App\Models\Operateur\ComissionModel;
use App\Models\Operateur\HistoriqueTransfertEtrangerModel;

class OperateurController extends BaseController
{
    public function index()
    {
      $prefixeModel      = new PrefixeModel();
        $prefixeAutreModel = new PrefixeAutreModel();

        $data = [
            'prefixes'          => $prefixeModel->findAll(),
            'autres_operateurs' => $prefixeAutreModel->findAll(),
        ];

        return view('operator/prefixes', $data);
    }

    public function store()
    {
        $prefixModel = new PrefixeModel();
        $prefixe = $this->request->getPost('prefixe');

        if (!empty($prefixe) && strlen($prefixe) === 3 && is_numeric($prefixe)) {
            try {
                $prefixModel->insert(['prefixe' => $prefixe]);
                return redirect()->to(site_url('operator/prefixes'))->with('success', 'Prefixe ajoute.');
            } catch (\Exception $e) {
                return redirect()->to(site_url('operator/prefixes'))->with('error', 'Ce prefixe existe deja.');
            }
        }
        return redirect()->to(site_url('operator/prefixes'))->with('error', 'Le prefixe doit contenir 3 chiffres.');
    }

    public function edit($id)
    {
        $prefixModel = new PrefixeModel();
        $data['prefixe'] = $prefixModel->find($id);

        if (!$data['prefixe']) {
            return redirect()->to(site_url('operator/prefixes'))->with('error', 'Prefixe introuvable.');
        }

        return view('operator/edit_prefixe', $data);
    }

    public function update($id)
    {
        $prefixModel = new PrefixeModel();
        $prefixe = $this->request->getPost('prefixe');

        if (!empty($prefixe) && strlen($prefixe) === 3 && is_numeric($prefixe)) {
            try {
                $prefixModel->update($id, ['prefixe' => $prefixe]);
                return redirect()->to(site_url('operator/prefixes'))->with('success', 'Prefixe modifie.');
            } catch (\Exception $e) {
                return redirect()->to(site_url('operator/prefixes'))->with('error', 'Ce prefixe existe deja.');
            }
        }
        return redirect()->to(site_url('operator/prefixes/edit/' . $id))->with('error', 'Le prefixe doit contenir 3 chiffres.');
    }

    public function delete($id)
    {
        $prefixModel = new PrefixeModel();
        $prefixModel->delete($id);
        return redirect()->to(site_url('operator/prefixes'))->with('success', 'Prefixe supprime.');
    }

    public function dashboard()
    {
        $db = \Config\Database::connect();
        $data['gains_par_type'] = $db->table('types_operation')
                                    ->select('types_operation.id, types_operation.nom as type_nom, COALESCE(SUM(transactions.frais_appliques), 0.0) as total_frais')
                                    ->join('transactions', 'transactions.id_type_operation = types_operation.id AND transactions.id_statut = 2', 'left')
                                    ->groupBy('types_operation.id')
                                    ->get()
                                    ->getResultArray();
        $gains = $db->table('transactions')
                    ->selectSum('frais_appliques')
                    ->where('id_statut', 2)
                    ->get()
                    ->getRowArray();
        $data['total_gains'] = $gains['frais_appliques'] ?? 0.0;

        $clientModel = new \App\Models\ClientModel();
        $data['clients'] = $clientModel->getClientsWithBalances();

        $data['transactions'] = $db->table('transactions')
                                   ->select('transactions.*, t_op.nom as type_nom, c_src.telephone as source_tel, c_dst.telephone as dest_tel, s_txn.code as statut_code')
                                   ->join('types_operation t_op', 't_op.id = transactions.id_type_operation')
                                   ->join('clients c_src', 'c_src.id = transactions.id_client_source')
                                   ->join('clients c_dst', 'c_dst.id = transactions.id_client_destination', 'left')
                                   ->join('statut_transaction s_txn', 's_txn.id = transactions.id_statut')
                                   ->orderBy('transactions.date_transaction', 'DESC')
                                   ->get()
                                   ->getResultArray();
        $historiqueEtrangerModel = new HistoriqueTransfertEtrangerModel();
        $data['montants_operateurs'] = $historiqueEtrangerModel->getMontantsAPayerParOperateur();
        return view('operator/dashboard', $data);
    }

    public function updateBareme()
    {
        $baremeModel = new BaremeFraisModel();
        $id = $this->request->getPost('id');
        $frais = $this->request->getPost('frais');

        if ($id && is_numeric($frais) && $frais >= 0) {
            $baremeModel->update($id, ['frais' => $frais]);
            return redirect()->to(site_url('operator/dashboard'))->with('success', 'Frais mis a jour.');
        }

        return redirect()->to(site_url('operator/dashboard'))->with('error', 'Donnees invalides.');
    }
    /**
     * NOUVEAU : Ajout d'un autre opérateur avec contrôle des doublons
     */
    public function store_autre()
    {
        $prefixe      = trim((string)$this->request->getPost('prefixe'));
        $nomOperateur = trim((string)$this->request->getPost('nom_operateur'));
        $pourcentage  = (float)$this->request->getPost('pourcentage');

        if (empty($prefixe) || empty($nomOperateur)) {
            return redirect()->back()->with('error', 'Le préfixe et le nom de l\'opérateur sont obligatoires.');
        }

        $prefixeAutreModel = new PrefixeAutreModel();

        // 1. Vérification doublon sur le préfixe externe
        $existant = $prefixeAutreModel->where('prefixe', $prefixe)->first();
        if ($existant) {
            return redirect()->back()->with('error', "Le préfixe {$prefixe} est déjà attribué à l'opérateur \"{$existant['nom_operateur']}\".");
        }

        // 2. Insertion de l'opérateur
        $idOperateur = $prefixeAutreModel->insert([
            'nom_operateur' => $nomOperateur,
            'prefixe'       => $prefixe,
        ]);

        // 3. Insertion de la commission initiale s'il y en a une
        if ($idOperateur) {
            $commissionModel = new CommissionModel();
            $commissionModel->insert([
                'id_operateur' => $idOperateur,
                'pourcentage'  => $pourcentage,
            ]);
        }

        return redirect()->back()->with('success', "L'opérateur {$nomOperateur} ({$prefixe}) a été ajouté avec succès.");
    }
    /**
     * NOUVEAU : Suppression d'un autre opérateur
     */
    public function delete_autre($id)
    {
        $prefixeAutreModel = new PrefixeAutreModel();
        $prefixeAutreModel->delete($id);

        return redirect()->back()->with('success', 'Opérateur externe supprimé.');
    }
}
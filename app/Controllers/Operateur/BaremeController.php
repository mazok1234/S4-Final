<?php

namespace App\Controllers\operateur;

use App\Controllers\BaseController;
use App\Models\Operateur\TypeOperationModel;
use App\Models\Operateur\BaremeFraisModel;

class BaremeController extends BaseController
{
    public function index()
    {
        $typeModel = new TypeOperationModel();
        $baremeModel = new BaremeFraisModel();
        
        $data['types'] = $typeModel->findAll();

        $db = \Config\Database::connect();
        $data['baremes'] = $db->table('baremes_frais')
                              ->select('baremes_frais.*, types_operation.nom as type_nom')
                              ->join('types_operation', 'types_operation.id = baremes_frais.id_type_operation')
                              ->orderBy('id_type_operation', 'ASC')
                              ->orderBy('montant_min', 'ASC')
                              ->get()
                              ->getResultArray();

        return view('operator/gestion_baremes', $data);
    }

    public function edit($id)
    {
        $baremeModel = new BaremeFraisModel();
        $data['bareme'] = $baremeModel->find($id);

        if (!$data['bareme']) {
            return redirect()->to(site_url('operator/gestion_baremes'))->with('error', 'Tranche introuvable.');
        }

        return view('operator/edit_bareme', $data);
    }


    public function store()
    {
        $baremeModel = new BaremeFraisModel();

        $data = [
            'id_type_operation' => $this->request->getPost('id_type_operation'),
            'montant_min'       => $this->request->getPost('montant_min'),
            'montant_max'       => $this->request->getPost('montant_max'),
            'frais'             => $this->request->getPost('frais')
        ];

        if ($data['montant_min'] >= $data['montant_max']) {
            return redirect()->to(site_url('operator/gestion_baremes'))->with('error', 'Le montant minimum doit etre inferieur au montant maximum.');
        }

        $baremeModel->insert($data);
        return redirect()->to(site_url('operator/gestion_baremes'))->with('success', 'Tranche ajoutee avec succes.');
    }

   
    public function update($id)
    {
        $baremeModel = new BaremeFraisModel();

        $data = [
            'montant_min' => $this->request->getPost('montant_min'),
            'montant_max' => $this->request->getPost('montant_max'),
            'frais'       => $this->request->getPost('frais')
        ];

        if ($id && $data['montant_min'] < $data['montant_max']) {
            $baremeModel->update($id, $data);
            return redirect()->to(site_url('operator/gestion_baremes'))->with('success', 'Tranche mise a jour.');
        }

        return redirect()->to(site_url('operator/gestion_baremes'))->with('error', 'Donnees invalides.');
    }

    public function delete($id)
    {
        $baremeModel = new BaremeFraisModel();
        $baremeModel->delete($id);
        return redirect()->to(site_url('operator/gestion_baremes'))->with('success', 'Tranche supprimee.');
    }
}
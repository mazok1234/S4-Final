<?php

namespace App\Controllers\operateur;

use App\Controllers\BaseController;
use App\Models\Operator\PrefixeModel;
use App\Models\Operator\BaremeFraisModel;

class OperateurController extends BaseController
{
    public function index()
    {
        $prefixModel = new PrefixeModel();
        $data['prefixes'] = $prefixModel->findAll();
        return view('operator/prefixes', $data);
    }

    public function store()
    {
        $prefixModel = new PrefixeModel();
        $prefixe = $this->request->getPost('prefixe');

        if (!empty($prefixe) && strlen($prefixe) === 3 && is_numeric($prefixe)) {
            try {
                $prefixModel->insert(['prefixe' => $prefixe]);
                return redirect()->to('/operator/prefixes')->with('success', 'Prefixe ajoute.');
            } catch (\Exception $e) {
                return redirect()->to('/operator/prefixes')->with('error', 'Ce prefixe existe deja.');
            }
        }
        return redirect()->to('/operator/prefixes')->with('error', 'Le prefixe doit contenir 3 chiffres.');
    }

    public function edit($id)
    {
        $prefixModel = new PrefixeModel();
        $data['prefixe'] = $prefixModel->find($id);

        if (!$data['prefixe']) {
            return redirect()->to('/operator/prefixes')->with('error', 'Prefixe introuvable.');
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
                return redirect()->to('/operator/prefixes')->with('success', 'Prefixe modifie.');
            } catch (\Exception $e) {
                return redirect()->to('/operator/prefixes')->with('error', 'Ce prefixe existe deja.');
            }
        }
        return redirect()->to('/operator/prefixes/edit/' . $id)->with('error', 'Le prefixe doit contenir 3 chiffres.');
    }

    public function delete($id)
    {
        $prefixModel = new PrefixeModel();
        $prefixModel->delete($id);
        return redirect()->to('/operator/prefixes')->with('success', 'Prefixe supprime.');
    }

    public function dashboard()
    {
        $db = \Config\Database::connect();

        $data['baremes'] = $db->table('baremes_frais')
                              ->select('baremes_frais.*, types_operation.nom as type_nom')
                              ->join('types_operation', 'types_operation.id = baremes_frais.id_type_operation')
                              ->get()
                              ->getResultArray();

        $data['clients'] = $db->table('clients')->get()->getResultArray();

        $gains = $db->table('transactions')->selectSum('frais_appliques')->get()->getRowArray();
        $data['total_gains'] = $gains['frais_appliques'] ?? 0.0;

        return view('operator/dashboard', $data);
    }

    public function updateBareme()
    {
        $baremeModel = new BaremeFraisModel();
        $id = $this->request->getPost('id');
        $frais = $this->request->getPost('frais');

        if ($id && is_numeric($frais) && $frais >= 0) {
            $baremeModel->update($id, ['frais' => $frais]);
            return redirect()->to('/operator/dashboard')->with('success', 'Frais mis a jour.');
        }

        return redirect()->to('/operator/dashboard')->with('error', 'Donnees invalides.');
    }
}
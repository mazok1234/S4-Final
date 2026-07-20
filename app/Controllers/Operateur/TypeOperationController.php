<?php

namespace App\Controllers\operateur;

use App\Controllers\BaseController;
use App\Models\Operateur\TypeOperationModel;

class TypeOperationController extends BaseController
{
    public function index()
    {
        $typeModel = new TypeOperationModel();

        return view('operator/types_operations', [
            'types' => $typeModel->orderBy('nom', 'ASC')->findAll(),
        ]);
    }

    public function store()
    {
        $typeModel = new TypeOperationModel();
        $nom = trim((string) $this->request->getPost('nom'));

        if ($nom === '') {
            return redirect()->to(site_url('operator/types_operation'))->with('error', 'Le nom du type est obligatoire.');
        }

        $existant = $typeModel->where('LOWER(nom)', strtolower($nom))->first();

        if ($existant) {
            return redirect()->to(site_url('operator/types_operation'))->with('error', 'Ce type d operation existe deja.');
        }

        $typeModel->insert(['nom' => $nom]);

        return redirect()->to(site_url('operator/types_operation'))->with('success', 'Type d operation ajoute.');
    }

    public function edit($id)
    {
        $typeModel = new TypeOperationModel();
        $type = $typeModel->find($id);

        if (!$type) {
            return redirect()->to(site_url('operator/types_operation'))->with('error', 'Type d operation introuvable.');
        }

        return view('operator/edit_type_operation', [
            'type' => $type,
        ]);
    }

    public function update($id)
    {
        $typeModel = new TypeOperationModel();
        $type = $typeModel->find($id);
        $nom = trim((string) $this->request->getPost('nom'));

        if (!$type) {
            return redirect()->to(site_url('operator/types_operation'))->with('error', 'Type d operation introuvable.');
        }

        if ($nom === '') {
            return redirect()->to(site_url('operator/types_operation/edit/' . $id))->with('error', 'Le nom du type est obligatoire.');
        }

        $existant = $typeModel
            ->where('LOWER(nom)', strtolower($nom))
            ->where('id !=', $id)
            ->first();

        if ($existant) {
            return redirect()->to(site_url('operator/types_operation/edit/' . $id))->with('error', 'Ce type d operation existe deja.');
        }

        $typeModel->update($id, ['nom' => $nom]);

        return redirect()->to(site_url('operator/types_operation'))->with('success', 'Type d operation mis a jour.');
    }

    public function delete($id)
    {
        $typeModel = new TypeOperationModel();
        $type = $typeModel->find($id);

        if (!$type) {
            return redirect()->to(site_url('operator/types_operation'))->with('error', 'Type d operation introuvable.');
        }

        $typeModel->delete($id);

        return redirect()->to(site_url('operator/types_operation'))->with('success', 'Type d operation supprime.');
    }
}
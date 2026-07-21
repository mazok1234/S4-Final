<?php

namespace App\Controllers\operateur;
use App\Models\Operateur\ClientrModel;
class EpargneController extends BaseController{
    public function store()
    {
        $epargneModel = new EpargneModel();
        $epargne = $this->request->getPost('valeur');

        if (!empty($prefixe) && strlen($prefixe) === 3 && is_numeric($prefixe)) {
            try {
                $epargneModel->insert(['valeur' => $valeur]);
                return redirect()->to(site_url('front_office/historique'));
            } catch (\Exception $e) {
                return redirect()->to(site_url('front_office/historique'));
            }
        }
        return redirect()->to(site_url('front_office/hitstorique'));
    }
}
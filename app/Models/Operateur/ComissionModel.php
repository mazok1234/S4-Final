<?php

namespace App\Models\Operateur;

use CodeIgniter\Model;

class ComissionModel extends Model
{
    protected $table            = 'comission';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['id_operateur', 'pourcentage', 'date'];
    protected $useTimestamps    = false;

    /**
     * Récupère la dernière commission définie pour un opérateur
     */
    public function getDerniereCommission(int $idOperateur): float
    {
        $row = $this->where('id_operateur', $idOperateur)
                    ->orderBy('date', 'DESC')
                    ->first();

        return $row ? (float) $row['pourcentage'] : 0.0;
    }
}
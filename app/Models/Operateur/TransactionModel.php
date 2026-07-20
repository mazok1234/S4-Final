<?php

namespace App\Models\Operateur;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table            = 'transactions';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'reference', 'id_client_source', 'id_client_destination', 
        'id_type_operation', 'id_statut', 'montant', 'frais_appliques'
    ];

    public function getGainsParType()
    {
        return $this->select('types_operation.nom as type_nom, SUM(transactions.frais_appliques) as total_frais')
                    ->join('types_operation', 'types_operation.id = transactions.id_type_operation')
                    ->where('transactions.id_statut', 2) // 2 = SUCCES dans statut_transaction
                    ->groupBy('transactions.id_type_operation')
                    ->findAll();
    }

    public function getGainTotalGlobal()
    {
        $result = $this->select('SUM(frais_appliques) as total')
                       ->where('id_statut', 2)
                       ->first();
                       
        return $result ? (float)$result['total'] : 0.0;
    }
}
<?php

namespace App\Models\Client;

use CodeIgniter\Model;

class EpargneModel extends Model
{
    protected $table            = 'compte_epargne';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['id_client','valeur'];
    protected $useAutoIncrement = true;
    
    // public function getValeurEpargne($id_client)
    // {
    //     $subQueryCommission = $this->db->table('compte_epargne')
    //         ->select()
    // }
}
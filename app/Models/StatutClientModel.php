<?php

namespace App\Models;

use CodeIgniter\Model;

class StatutClientModel extends Model
{
    // Nom de la table en BDD
    protected $table            = 'statut_client';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array'; // Ou 'object' / App\Entities\Client::class
    protected $useSoftDeletes   = false;

      protected $allowedFields    = ['date_modification','id_client','id_statut'];
    // Gestion automatique des timestamps (created_at, updated_at)
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'date_modification';
    protected $updatedField  = '';
}
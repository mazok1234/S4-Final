<?php

namespace App\Models;

use CodeIgniter\Model;

class PromotionModel extends Model
{
    // Nom de la table en BDD
    protected $table            = 'promotion_transfert';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array'; // Ou 'object' / App\Entities\Client::class
    protected $useSoftDeletes   = false;

      protected $allowedFields    = ['pourcentage','date'];
    // Gestion automatique des timestamps (created_at, updated_at)
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'date';
    protected $updatedField  = '';
        public function getDernierePromotion(): float
    {
        $row = $this->orderBy('date', 'DESC')
                    ->first();

        return $row ? (float) $row['pourcentage'] : 0.0;
    }
}
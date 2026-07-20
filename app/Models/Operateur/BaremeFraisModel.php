<?php

namespace App\Models\Operator;

use CodeIgniter\Model;

class BaremeFraisModel extends Model
{
    protected $table            = 'baremes_frais';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['id_type_operation', 'montant_min', 'montant_max', 'frais'];
    protected $useAutoIncrement = true;
}
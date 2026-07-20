<?php

namespace App\Models\Operateur;

use CodeIgniter\Model;

class PrefixeModel extends Model
{
    protected $table            = 'prefixes';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['prefixe'];
    protected $useAutoIncrement = true;
}
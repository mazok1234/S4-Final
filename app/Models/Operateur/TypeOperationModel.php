<?php

namespace App\Models\Operateur;

use CodeIgniter\Model;

class TypeOperationModel extends Model
{
    protected $table            = 'types_operation';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nom'];
    protected $useAutoIncrement = true;
}
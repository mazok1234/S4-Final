<?php

namespace App\Models\Operateur;

use CodeIgniter\Model;

class PrefixeAutreModel extends Model
{
    protected $table            = 'prefixe_autre';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nom_operateur', 'prefixe'];
    protected $useTimestamps    = false;

    /**
     * Vérifie si un numéro appartient à un autre opérateur
     */
    public function getOperateurParNumero(string $telephone): ?array
    {
        $telephone = trim($telephone);
        $prefixes  = $this->findAll();

        foreach ($prefixes as $p) {
            if (strpos($telephone, $p['prefixe']) === 0) {
                return $p;
            }
        }

        return null;
    }
}
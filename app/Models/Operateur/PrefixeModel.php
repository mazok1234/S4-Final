<?php

namespace App\Models\Operateur;

use CodeIgniter\Model;

class PrefixeModel extends Model
{
    protected $table            = 'prefixes';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['prefixe'];
    protected $useAutoIncrement = true;
    
    public function isValidTelephone(string $telephone): bool
    {
        // Nettoyage de la chaîne (enlève les espaces éventuels)
        $telephone = trim($telephone);

        // Récupère la liste de tous les préfixes sous forme de tableau simple : ['033', '037']
        $prefixes = array_column($this->findAll(), 'prefixe');

        foreach ($prefixes as $prefixe) {
            if (str_starts_with($telephone, $prefixe)) {
                return true;
            }
        }

        return false;
    }
}
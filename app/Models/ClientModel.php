<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\Operateur\PrefixeModel;
class ClientModel extends Model
{
    protected $table            = 'clients';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['telephone'];
    protected $useTimestamps    = false; // Votre schéma utilise DEFAULT CURRENT_TIMESTAMP

    public function findByTelephone($telephone)
    {
        return $this->where('telephone', $telephone)->first();
    }

    public function getOrCreateByTelephone($telephone)
    {
        $telephone = trim((string)$telephone);

        // 1. Recherche du client s'il existe déjà
        $client = $this->findByTelephone($telephone);

        if ($client) {
            return $client;
        }

        // 2. Vérification du préfixe via PrefixeModel
        $prefixeModel = new PrefixeModel();
        if (!$prefixeModel->isValidTelephone($telephone)) {
            // Lève une exception explicite qui sera attrapée par le contrôleur
            throw new \InvalidArgumentException("Le numéro de téléphone doit commencer par un préfixe valide (ex: 033, 037).");
        }

        // 3. Insertion du nouveau client
        $clientId = $this->skipValidation(true)->insert(['telephone' => $telephone]);

        if (!$clientId) {
            throw new \RuntimeException("Impossible d'enregistrer le client en base.");
        }

        // 4. Attribution du statut 'ACTIF' (id = 1 dans votre table statut)
        $statutClientModel = new StatutClientModel();
        $statutClientModel->skipValidation(true)->insert([
            'id_client' => $clientId,
            'id_statut' => 1,
        ]);

        return $this->find($clientId);
    }
}
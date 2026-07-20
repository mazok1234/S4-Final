<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    // Nom de la table en BDD
    protected $table            = 'clients';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // Champs autorisés à l'insertion/modification
    protected $allowedFields    = ['telephone'];

    // Gestion automatique des timestamps
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    // Règles de validation intégrées
    protected $validationRules = [
        'telephone' => 'required|min_length[10]|max_length[15]|is_unique[clients.telephone,id,{id}]',
    ];

    protected $validationMessages = [
        'telephone' => [
            'required'   => 'Le numéro de téléphone est obligatoire.',
            'min_length' => 'Le numéro de téléphone doit contenir au moins 10 chiffres.',
            'is_unique'  => 'Ce numéro de téléphone est déjà enregistré.',
        ],
    ];

    protected $skipValidation = false;
  
    public function findByTelephone($telephone)
    {
         return $this->where('telephone', $telephone)->first();
    } 

    public function getOrCreateByTelephone($telephone)
    {
        // 1. Correction ici : Ajout de $this->
        $client = $this->findByTelephone($telephone);

        if ($client) {
            return $client;
        }

        // 2. Insertion du nouveau client
        $dataClient = ['telephone' => $telephone];
        $clientId   = $this->insert($dataClient);

        // Si l'insertion échoue à cause des règles de validation
        if (!$clientId) {
            return false;
        }

        // 3. Attribution du statut par défaut dans la table de liaison
        $statutClientModel = new StatutClientModel();
        $dataStatut = [
            'id_client' => $clientId,
            'id_statut' => 1,
        ];
        
        $statutClientModel->insert($dataStatut);

        // 4. Retourne les infos du client venant d'être créé
        return $this->find($clientId);
    }
}
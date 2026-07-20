<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    // Nom de la table en BDD
    protected $table            = 'clients';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array'; // Ou 'object' / App\Entities\Client::class
    protected $useSoftDeletes   = false;

    // Champs autorisés à l'insertion/modification
    protected $allowedFields    = ['telephone'];

    // Gestion automatique des timestamps (created_at, updated_at)
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

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

    public function getClientsWithBalances()
    {
        $db = \Config\Database::connect();
        
        $sql = "SELECT c.id, c.telephone,
                COALESCE((
                    SELECT s.libelle 
                    FROM statut_client sc 
                    JOIN statut s ON s.id = sc.id_statut 
                    WHERE sc.id_client = c.id 
                    ORDER BY sc.date_modification DESC, sc.id DESC 
                    LIMIT 1
                ), 'ACTIF') AS statut_libelle,
                COALESCE((SELECT SUM(t.montant - t.frais_appliques) FROM transactions t WHERE t.id_client_source = c.id AND t.id_type_operation = 1 AND t.id_statut = 2), 0) +
                COALESCE((SELECT SUM(t.montant) FROM transactions t WHERE t.id_client_destination = c.id AND t.id_type_operation = 3 AND t.id_statut = 2), 0) -
                COALESCE((SELECT SUM(t.montant + t.frais_appliques) FROM transactions t WHERE t.id_client_source = c.id AND t.id_type_operation = 2 AND t.id_statut = 2), 0) -
                COALESCE((SELECT SUM(t.montant + t.frais_appliques) FROM transactions t WHERE t.id_client_source = c.id AND t.id_type_operation = 3 AND t.id_statut = 2), 0) AS solde
                FROM clients c";
                
        return $db->query($sql)->getResultArray();
    }
}
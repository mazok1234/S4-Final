<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\Operateur\PrefixeModel;

class ClientModel extends Model
{
    protected $table            = 'clients';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['telephone'];
    protected $useTimestamps    = false;

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

    /**
     * Calcule le solde net d'un client à partir de la table transactions
     */
    public function getSolde(int $clientId, ?string $date = null): float
    {
        $depots     = $this->getDepot($clientId, $date);
        $retraits   = $this->getRetrait($clientId, $date);
        $transferts = $this->getTransfert($clientId, $date);

        return $depots + $transferts['net'] - $retraits;
    }

    /**
     * Récupère les frais applicables selon le barème via Query Builder
     */
    public function getFrais(int $idTypeOperation, float $montant): float
    {
        $builder = $this->db->table('baremes_frais');

        $row = $builder->select('frais')
            ->where('id_type_operation', $idTypeOperation)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->get()
            ->getRow();

        return $row ? (float) $row->frais : 0.0;
    }

    /**
     * Génère une référence unique pour la transaction
     */
    private function generateReference(): string
    {
        return 'TXN-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), -6));
    }

    /**
     * Opération de Dépôt (id_type_operation = 1)
     */
    public function depot(int $clientId, float $montant): bool
    {
        if ($montant <= 0) {
            throw new \InvalidArgumentException("Le montant du dépôt doit être supérieur à 0.");
        }

        // Vérifier que le client existe en base
        $client = $this->find($clientId);
        if (!$client) {
            throw new \InvalidArgumentException("Client introuvable. Veuillez vous reconnecter.");
        }

        $this->db->transBegin();

        $this->db->table('transactions')->insert([
            'reference'             => $this->generateReference(),
            'id_client_source'      => $clientId,
            'id_client_destination' => null,
            'id_type_operation'     => 1, // Dépôt
            'id_statut'             => 2, // SUCCES
            'montant'               => $montant,
            'frais_appliques'       => 0.0,
        ]);

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return false;
        }

        $this->db->transCommit();
        return true;
    }

    /**
     * Opération de Retrait (id_type_operation = 2)
     */
    public function retrait(int $clientId, float $montant): bool
    {
        if ($montant <= 0) {
            throw new \InvalidArgumentException("Le montant du retrait doit être supérieur à 0.");
        }

        // Vérifier que le client existe en base
        $client = $this->find($clientId);
        if (!$client) {
            throw new \InvalidArgumentException("Client introuvable. Veuillez vous reconnecter.");
        }

        $frais = $this->getFrais(2, $montant);
        $totalAboed = $montant + $frais;

        if ($this->getSolde($clientId) < $totalAboed) {
            throw new \InvalidArgumentException("Solde insuffisant pour effectuer ce retrait (Frais applicables : {$frais} Ar).");
        }

        $this->db->transBegin();

        $this->db->table('transactions')->insert([
            'reference'             => $this->generateReference(),
            'id_client_source'      => $clientId,
            'id_client_destination' => null,
            'id_type_operation'     => 2, // Retrait
            'id_statut'             => 2, // SUCCES
            'montant'               => $montant,
            'frais_appliques'       => $frais,
        ]);

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return false;
        }

        $this->db->transCommit();
        return true;
    }


    public function transfert(int $clientIdSource, string $telephoneDest, float $montant): bool
    {
        if ($montant <= 0) {
            throw new \InvalidArgumentException("Le montant du transfert doit être supérieur à 0.");
        }
        $telephoneDest = trim($telephoneDest);

        // 1. Vérification s'il s'agit d'un autre opérateur
        $prefixeAutreModel = new \App\Models\Operateur\PrefixeAutreModel();
        $autreOperateur    = $prefixeAutreModel->getOperateurParNumero($telephoneDest);

        // Détermination automatique de l'opérateur
        $expediteur = $this->find($clientIdSource);
        $prefixeModel = new \App\Models\Operateur\PrefixeModel();
        $prefixes = array_column($prefixeModel->findAll(), 'prefixe');

        $getPrefix = function(string $telephone) use ($prefixes) {
            foreach ($prefixes as $pref) {
                if (str_starts_with($telephone, $pref)) return $pref;
            }
            return null;
        };

        $senderPrefix = $getPrefix($expediteur['telephone']);
        $destPrefix   = $getPrefix($telephoneDest);
        $isSameOperator = ($senderPrefix !== null && $destPrefix === $senderPrefix);

        $fraisTransfert = $this->getFrais(3, $montant);
        $totalDebite    = $montant + $fraisTransfert;

        if ($this->getSolde($clientIdSource) < $totalDebite) {
            throw new \InvalidArgumentException(
                "Solde insuffisant pour ce transfert (Débit total : {$totalDebite} Ar, dont {$fraisTransfert} Ar de frais)."
            );
        }

        // Frais automatiques selon l'opérateur
        if ($isSameOperator) {
            // Même opérateur : frais de retrait inclus automatiquement dans le montant envoyé
            $fraisRetrait  = $this->getFrais(2, $montant);
            $montantStored = $montant + $fraisRetrait;
            $fraisStored   = 0.0;
        } else {
            // Autre opérateur : pas de frais de retrait
            $montantStored = $montant;
            $fraisStored   = $fraisTransfert;
        }

        $this->db->transBegin();

        $reference = $this->generateReference();

        if ($autreOperateur) {
            // Transfert vers AUTRE OPÉRATEUR : destinataire = NULL dans transactions
            $this->db->table('transactions')->insert([
                'reference'             => $reference,
                'id_client_source'      => $clientIdSource,
                'id_client_destination' => null,
                'id_type_operation'     => 3, // Transfert
                'id_statut'             => 2, // SUCCES
                'montant'               => $montantStored,
                'frais_appliques'       => $fraisStored,
            ]);

            // Enregistrement dans l'historique des transferts étrangers
            $this->db->table('historique_transfert_etranger')->insert([
                'reference'           => $reference,
                'id_client_source'    => $clientIdSource,
                'id_operateur'        => $autreOperateur['id'],
                'numero_destinataire' => $telephoneDest,
                'montant'             => $montantStored,
            ]);

        }
        else{
            $destinataire = $this->where('telephone', trim($telephoneDest))->first();
            if (!$destinataire) {
                throw new \InvalidArgumentException("Numéro du destinataire introuvable.");
            }
    
            if ((int)$destinataire['id'] === $clientIdSource) {
                throw new \InvalidArgumentException("Vous ne pouvez pas effectuer un transfert vers vous-même.");
            }
    
            $this->db->table('transactions')->insert([
                'reference'             => $reference,
                'id_client_source'      => $clientIdSource,
                'id_client_destination' => $destinataire['id'],
                'id_type_operation'     => 3, // Transfert
                'id_statut'             => 2, // SUCCES
                'montant'               => $montantStored,
                'frais_appliques'       => $fraisStored,
            ]);
        }

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return false;
        }

        $this->db->transCommit();
        return true;
    }

   
    public function transfertMultiple(int $clientIdSource, array $telephonesDest, float $montantTotal): bool
    {
        if ($montantTotal <= 0) {
            throw new \InvalidArgumentException("Le montant total du transfert doit être supérieur à 0.");
        }

        $telephonesDestClean = [];
        foreach ($telephonesDest as $tel) {
            $cleaned = trim((string)$tel);
            if ($cleaned !== '') {
                $telephonesDestClean[] = $cleaned;
            }
        }
        $telephonesDestClean = array_unique($telephonesDestClean);

        $nbDestinataires = count($telephonesDestClean);
        if ($nbDestinataires === 0) {
            throw new \InvalidArgumentException("Veuillez saisir au moins un numéro de destinataire.");
        }

        // Valider l'expéditeur
        $expediteur = $this->find($clientIdSource);
        if (!$expediteur) {
            throw new \InvalidArgumentException("Compte expéditeur introuvable.");
        }

        // Récupérer la liste des préfixes
        $prefixeModel = new \App\Models\Operateur\PrefixeModel();
        $prefixes = array_column($prefixeModel->findAll(), 'prefixe');

        $getPrefix = function(string $telephone) use ($prefixes) {
            foreach ($prefixes as $pref) {
                if (str_starts_with($telephone, $pref)) {
                    return $pref;
                }
            }
            return null;
        };

        $senderPrefix = $getPrefix($expediteur['telephone']);

        // Règle : Même opérateur uniquement en transfert multiple
        if ($nbDestinataires > 1) {
            foreach ($telephonesDestClean as $tel) {
                $destPrefix = $getPrefix($tel);
                if ($destPrefix !== $senderPrefix) {
                    throw new \InvalidArgumentException(
                        "Le transfert multiple est réservé exclusivement aux numéros du même opérateur (" . ($senderPrefix ?: "inconnu") . ")."
                    );
                }
            }
        }

        // Division du montant
        $montantIndiv = $montantTotal / $nbDestinataires;

        if ($montantIndiv < 100) {
            throw new \InvalidArgumentException(
                "Le montant divisé par destinataire (" . number_format($montantIndiv, 2) . " Ar) est inférieur au montant minimum de transfert autorisé (100 Ar)."
            );
        }

        // Récupérer et valider chaque destinataire
        $destinataires = [];
        foreach ($telephonesDestClean as $tel) {
            $dest = $this->where('telephone', $tel)->first();
            if (!$dest) {
                throw new \InvalidArgumentException("Numéro de destinataire introuvable : {$tel}.");
            }
            if ((int)$dest['id'] === $clientIdSource) {
                throw new \InvalidArgumentException("Vous ne pouvez pas effectuer un transfert vers vous-même ({$tel}).");
            }
            $destinataires[] = $dest;
        }

        // Calculer le débit total et préparer les données d'insertion
        $transactionsData = [];
        $debitTotal = 0.0;

        foreach ($destinataires as $dest) {
            $destPrefix     = $getPrefix($dest['telephone']);
            $isSameOperator = ($destPrefix === $senderPrefix);
            $fraisTransfert = $this->getFrais(3, $montantIndiv);

            if ($isSameOperator) {
                // Même opérateur : frais de retrait inclus automatiquement
                $fraisRetrait  = $this->getFrais(2, $montantIndiv);
                $montantStored = $montantIndiv + $fraisRetrait;
                $fraisStored   = 0.0;
                $debitTotal   += ($montantIndiv + $fraisRetrait);
            } else {
                // Autre opérateur : pas de frais de retrait
                $montantStored = $montantIndiv;
                $fraisStored   = $fraisTransfert;
                $debitTotal   += ($montantIndiv + $fraisTransfert);
            }

            $transactionsData[] = [
                'id_client_destination' => $dest['id'],
                'montant'               => $montantStored,
                'frais_appliques'       => $fraisStored,
            ];
        }

        // Vérifier le solde
        $soldeActuel = $this->getSolde($clientIdSource);
        if ($soldeActuel < $debitTotal) {
            throw new \InvalidArgumentException(
                "Solde insuffisant pour ce transfert (Débit total requis : " . number_format($debitTotal, 2) . " Ar. Votre solde actuel est de " . number_format($soldeActuel, 2) . " Ar)."
            );
        }

        // Début de la transaction base de données
        $this->db->transBegin();

        foreach ($transactionsData as $data) {
            $this->db->table('transactions')->insert([
                'reference'             => $this->generateReference(),
                'id_client_source'      => $clientIdSource,
                'id_client_destination' => $data['id_client_destination'],
                'id_type_operation'     => 3, // Transfert
                'id_statut'             => 2, // SUCCES
                'montant'               => $data['montant'],
                'frais_appliques'       => $data['frais_appliques'],
            ]);
        }

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return false;
        }

        $this->db->transCommit();
        return true;
    }

    /**
     * Total des dépôts via Query Builder
     */
    public function getDepot(int $clientId, ?string $date = null): float
    {
        $builder = $this->db->table('transactions')
            ->select('COALESCE(SUM(montant), 0) as total')
            ->where('id_client_source', $clientId)
            ->where('id_type_operation', 1)
            ->where('id_statut', 2);

        if (!empty($date)) {
            $builder->where('date_transaction <=', $date);
        }

        return (float) $builder->get()->getRow()->total;
    }

    /**
     * Total des retraits (montant + frais) via Query Builder
     */
    public function getRetrait(int $clientId, ?string $date = null): float
    {
        $builder = $this->db->table('transactions')
            ->select('COALESCE(SUM(montant + frais_appliques), 0) as total')
            ->where('id_client_source', $clientId)
            ->where('id_type_operation', 2)
            ->where('id_statut', 2);

        if (!empty($date)) {
            $builder->where('date_transaction <=', $date);
        }

        return (float) $builder->get()->getRow()->total;
    }

    /**
     * Total des transferts (envois et réceptions) via Query Builder
     */
    public function getTransfert(int $clientId, ?string $date = null): array
    {
        // 1. Transferts envoyés (montant + frais)
        $builderEnvoi = $this->db->table('transactions')
            ->select('COALESCE(SUM(montant + frais_appliques), 0) as total')
            ->where('id_client_source', $clientId)
            ->where('id_type_operation', 3)
            ->where('id_statut', 2);

        if (!empty($date)) {
            $builderEnvoi->where('date_transaction <=', $date);
        }

        $totalEnvoi = (float) $builderEnvoi->get()->getRow()->total;

        // 2. Transferts reçus (montant brut)
        $builderRecu = $this->db->table('transactions')
            ->select('COALESCE(SUM(montant), 0) as total')
            ->where('id_client_destination', $clientId)
            ->where('id_type_operation', 3)
            ->where('id_statut', 2);

        if (!empty($date)) {
            $builderRecu->where('date_transaction <=', $date);
        }

        $totalRecu = (float) $builderRecu->get()->getRow()->total;

        return [
            'envoye' => $totalEnvoi,
            'recu'   => $totalRecu,
            'net'    => $totalRecu - $totalEnvoi
        ];
    }

    /**
     * Récupère la liste des transactions via Query Builder
     */
/**
     * Récupère la liste des transactions d'un client
     * Sépare visuellement les transferts en "Transfert envoyé" et "Transfert reçu"
     */
    public function getTransactions(int $clientId): array
    {
        return $this->db->table('transactions t')
            ->select("
                t.*, 
                st.code as statut,
                CASE 
                    WHEN t.id_type_operation = 3 AND t.id_client_source = {$clientId} THEN 'Transfert envoyé'
                    WHEN t.id_type_operation = 3 AND t.id_client_destination = {$clientId} THEN 'Transfert reçu'
                    ELSE top.nom 
                END as operation
            ")
            ->join('types_operation top', 'top.id = t.id_type_operation')
            ->join('statut_transaction st', 'st.id = t.id_statut')
            ->groupStart()
                ->where('t.id_client_source', $clientId)
                ->orWhere('t.id_client_destination', $clientId)
            ->groupEnd()
            ->orderBy('t.date_transaction', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Prépare l'ensemble des données nécessaires pour la vue de l'historique
     */
    public function getHistoriqueData(int $clientId, string $telephone): array
    {
        $prefixeModel = new \App\Models\Operateur\PrefixeModel();
        $prefixes = array_column($prefixeModel->findAll(), 'prefixe');

        return [
            'solde'        => $this->getSolde($clientId),
            'telephone'    => $telephone,
            'transactions' => $this->getTransactions($clientId),
            'prefixes'     => $prefixes,
        ];
    }
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
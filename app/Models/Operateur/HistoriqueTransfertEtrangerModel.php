<?php

namespace App\Models\Operateur;

use CodeIgniter\Model;

class HistoriqueTransfertEtrangerModel extends Model
{
    protected $table            = 'historique_transfert_etranger';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['reference', 'id_client_source', 'id_operateur', 'numero_destinataire', 'montant', 'date_transaction'];
    protected $useTimestamps    = false;

    /**
     * Calcule la somme due à chaque opérateur (Montant brut - Commission)
     */
  public function getMontantsAPayerParOperateur(): array
    {
        // Sous-requête pour récupérer la dernière commission par opérateur
        $subQueryCommission = $this->db->table('commission c1')
            ->select('c1.id_operateur, c1.pourcentage')
            ->where('c1.id = (
                SELECT c2.id 
                FROM commission c2 
                WHERE c2.id_operateur = c1.id_operateur 
                ORDER BY c2.date DESC, c2.id DESC 
                LIMIT 1
            )', null, false);

        // Requête principale
        return $this->db->table('prefixe_autre pa')
            ->select("
                pa.id as id_operateur,
                pa.nom_operateur,
                pa.prefixe,
                COALESCE(SUM(hte.montant), 0) as total_brut,
                COALESCE(comm.pourcentage, 0) as pourcentage_commission,
                COALESCE(SUM(hte.montant) * (1 - (COALESCE(comm.pourcentage, 0) / 100.0)), 0) as montant_a_envoyer
            ")
            ->join('historique_transfert_etranger hte', 'hte.id_operateur = pa.id', 'left')
            ->join("({$subQueryCommission->getCompiledSelect()}) comm", 'comm.id_operateur = pa.id', 'left')
            ->groupBy('pa.id, pa.nom_operateur, pa.prefixe, comm.pourcentage')
            ->get()
            ->getResultArray();
    }
}
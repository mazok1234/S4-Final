<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord Operateur</title>
</head>
<body>

    <div style="margin-bottom: 20px; font-family: sans-serif; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <a href="<?= site_url('operator/dashboard') ?>"><strong>Tableau de Bord / Gains</strong></a> | 
            <a href="<?= site_url('operator/prefixes') ?>">Gestion des Prefixes</a> | 
            <a href="<?= site_url('operator/gestion_baremes') ?>">Gestion des Baremes</a> | 
            <a href="<?= site_url('operator/types_operation') ?>">Gestion des Types d'Operation</a>
        </div>
        <?php if (session()->get('isOperator')): ?>
        <a href="<?= site_url('auth/logout') ?>" 
           style="background-color:#dc2626; color:#fff; padding:0.45rem 1rem; border-radius:6px; text-decoration:none; font-weight:600; font-size:0.9rem;">
            ⏻ Déconnecter
        </a>
        <?php endif; ?>
    </div>

    <hr>

    <div style="font-family: sans-serif;">
        <h2>Tableau de Bord de l'Operateur (Gains & Situation)</h2>
    </div>

    <!-- Messages Flash -->
    <?php if (session()->getFlashdata('success')): ?>
        <p style="color: green; font-weight: bold;"><?= session()->getFlashdata('success') ?></p>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <p style="color: red; font-weight: bold;"><?= session()->getFlashdata('error') ?></p>
    <?php endif; ?>

    <!-- Section 1 : Situation des Gains -->
    <div style="font-family: sans-serif;">
        <h3>1. Situation des Gains via les Frais</h3>
        <table border="1" cellpadding="8" cellspacing="0" style="width: 50%; text-align: left;">
            <thead>
                <tr style="background-color: #f3f4f6;">
                    <th>Type d'Operation</th>
                    <th>Total des Gains (Frais percus)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($gains_par_type)): ?>
                    <?php foreach ($gains_par_type as $g): ?>
                        <tr>
                            <td><strong><?= ucfirst($g['type_nom']) ?></strong></td>
                            <td><?= number_format($g['total_frais'], 2, ',', ' ') ?> Ar</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2" align="center">Aucun gain enregistre.</td>
                    </tr>
                <?php endif; ?>
                <tr style="background-color: #e5e7eb; font-weight: bold;">
                    <td>TOTAL GLOBAL DES GAINS</td>
                    <td><?= number_format($total_gains, 2, ',', ' ') ?> Ar</td>
                </tr>
            </tbody>
        </table>
    </div>

    <br>
    <hr>

    <!-- Section 2 : Situation des Comptes Clients -->
    <div style="font-family: sans-serif; margin-top: 20px;">
        <h3>2. Situation des Comptes des Clients (Soldes en temps reel)</h3>
        <table border="1" cellpadding="8" cellspacing="0" style="width: 70%; text-align: left;">
            <thead>
                <tr style="background-color: #f3f4f6;">
                    <th>ID</th>
                    <th>Numero de Telephone</th>
                    <th>Statut</th>
                    <th>Solde Actuel (Calculé)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($clients)): ?>
                    <?php foreach ($clients as $c): ?>
                        <tr>
                            <td><?= $c['id'] ?></td>
                            <td><strong><?= esc($c['telephone']) ?></strong></td>
                            <td>
                                <?php if ($c['statut_libelle'] === 'ACTIF'): ?>
                                    <span style="color: green; font-weight: bold;"><?= $c['statut_libelle'] ?></span>
                                <?php elseif ($c['statut_libelle'] === 'BLOQUE'): ?>
                                    <span style="color: red; font-weight: bold;"><?= $c['statut_libelle'] ?></span>
                                <?php else: ?>
                                    <span style="color: orange; font-weight: bold;"><?= $c['statut_libelle'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= number_format($c['solde'], 2, ',', ' ') ?> Ar</strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" align="center">Aucun client enregistre.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <br>
    <hr>

    <!-- NOUVEAU : Section 3 : Montants à payer aux autres opérateurs -->
    <div style="font-family: sans-serif; margin-top: 20px;">
        <h3>3. Montants a Payer aux Autres Operateurs</h3>
        <table border="1" cellpadding="8" cellspacing="0" style="width: 80%; text-align: left;">
            <thead>
                <tr style="background-color: #f3f4f6;">
                    <th>Operateur</th>
                    <th>Prefixe</th>
                    <th>Total Brut Transféré</th>
                    <th>Commission (%)</th>
                    <th>Montant Net a Envoyer</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($montants_operateurs)): ?>
                    <?php foreach ($montants_operateurs as $op): ?>
                        <tr>
                            <td><strong><?= esc($op['nom_operateur']) ?></strong></td>
                            <td><?= esc($op['prefixe']) ?></td>
                            <td><?= number_format($op['total_brut'], 2, ',', ' ') ?> Ar</td>
                            <td><?= number_format($op['pourcentage_commission'], 2, ',', ' ') ?> %</td>
                            <td style="color: #15803d; font-weight: bold;">
                                <?= number_format($op['montant_a_envoyer'], 2, ',', ' ') ?> Ar
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" align="center">Aucun autre opérateur configuré ou aucun transfert effectué.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <br>
    <hr>

</body>
</html>
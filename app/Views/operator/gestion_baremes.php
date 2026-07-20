<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Baremes de Frais</title>
</head>
<body>

    <h2>Gestion des Baremes de Frais</h2>
    <hr>

    <!-- Notifications -->
    <?php if (session()->getFlashdata('success')): ?>
        <p style="color: green; font-weight: bold;"><?= session()->getFlashdata('success') ?></p>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <p style="color: red; font-weight: bold;"><?= session()->getFlashdata('error') ?></p>
    <?php endif; ?>

    <!-- Formulaire d'Ajout Simple -->
    <div>
        <h3>Creer une tranche</h3>
        <form action="<?= site_url('operator/gestion_baremes/store') ?>" method="POST">
            <label>Type :</label>
            <select name="id_type_operation" required>
                <?php foreach ($types as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= ucfirst($t['nom']) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Min :</label>
            <input type="number" step="0.01" name="montant_min" required style="width:90px;">

            <label>Max :</label>
            <input type="number" step="0.01" name="montant_max" required style="width:90px;">

            <label>Frais :</label>
            <input type="number" step="0.01" name="frais" required style="width:70px;">

            <button type="submit">Ajouter</button>
        </form>
    </div>

    <br><hr><br>

    <div>
        <h3>Liste des tranches</h3>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Montant Minimum</th>
                    <th>Montant Maximum</th>
                    <th>Frais</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($baremes)): ?>
                    <?php foreach ($baremes as $b): ?>
                        <tr>
                            <td><strong><?= strtoupper($b['type_nom']) ?></strong></td>
                            <td><?= $b['montant_min'] ?> Ar</td>
                            <td><?= $b['montant_max'] ?> Ar</td>
                            <td><strong><?= $b['frais'] ?> Ar</strong></td>
                            <td>
                                <a href="<?= site_url('operator/gestion_baremes/edit/'.$b['id']) ?>">Modifier</a>
                                | 
                                <a href="<?= site_url('operator/gestion_baremes/delete/'.$b['id']) ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" align="center">Aucun bareme configure.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
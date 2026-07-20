<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Types d Operation</title>
</head>
<body>

    <!-- Barre de Navigation Commune -->
    <div style="margin-bottom: 20px; font-family: sans-serif;">
        <a href="<?= site_url('operator/dashboard') ?>">Tableau de Bord / Gains</a> | 
        <a href="<?= site_url('operator/prefixes') ?>">Gestion des Prefixes</a> | 
        <a href="<?= site_url('operator/gestion_baremes') ?>">Gestion des Baremes</a> | 
        <a href="<?= site_url('operator/types_operation') ?>"><strong>Gestion des Types d'Operation</strong></a>
    </div>

    <hr>

    <div>
        <h2>Gestion des types d operation</h2>
        <a href="<?= site_url('operator/gestion_baremes') ?>">Retour aux baremes</a>
    </div>

    <hr>

    <?php if (session()->getFlashdata('error')): ?>
        <div style="color: red; font-weight: bold; margin-bottom: 10px;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div style="color: green; font-weight: bold; margin-bottom: 10px;">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <div>
        <h3>Ajouter un type</h3>
        <form action="<?= site_url('operator/types_operation/store') ?>" method="POST">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required placeholder="Ex: depot">
            <button type="submit">Ajouter</button>
        </form>
    </div>

    <br>
    <hr>

    <div>
        <h3>Liste des types d operation</h3>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($types)): ?>
                    <?php foreach ($types as $type): ?>
                        <tr>
                            <td><?= $type['id'] ?></td>
                            <td><strong><?= esc($type['nom']) ?></strong></td>
                            <td>
                                <a href="<?= site_url('operator/types_operation/edit/' . $type['id']) ?>">Modifier</a>
                                |
                                <a href="<?= site_url('operator/types_operation/delete/' . $type['id']) ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" align="center">Aucun type d operation configure.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
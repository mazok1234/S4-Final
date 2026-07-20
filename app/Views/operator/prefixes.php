<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Configuration des Prefixes</title>
</head>
<body>

    <div>
        <h2>Gestion des prefixes de l'operateur</h2>
    </div>
    
    <hr>

    <!-- Section Messages Flash -->
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

    <!-- Formulaire d'Ajout -->
    <div>
        <h3>Ajouter un prefixe</h3>
        <form action="<?= site_url('operator/prefixes/store') ?>" method="POST">
            <label for="prefixe">Prefixe :</label>
            <input type="text" id="prefixe" name="prefixe" maxlength="3" required placeholder="Ex: 034">
            <button type="submit">Ajouter</button>
        </form>
    </div>

    <br>
    <hr>

    <!-- Liste des Prefixes -->
    <div>
        <h3>Prefixes autorises</h3>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Prefixe</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($prefixes)): ?>
                    <?php foreach ($prefixes as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><strong><?= $p['prefixe'] ?></strong></td>
                            <td>
                                <a href="<?= site_url('operator/prefixes/edit/'.$p['id']) ?>">Modifier</a>
                                | 
                                <a href="<?= site_url('operator/prefixes/delete/'.$p['id']) ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" align="center">Aucun prefixe configure.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
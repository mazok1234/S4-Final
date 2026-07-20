<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Configuration des Prefixes</title>
</head>
<body>

    <!-- Barre de Navigation Commune -->
    <div style="margin-bottom: 20px; font-family: sans-serif;">
        <a href="<?= site_url('operator/dashboard') ?>">Tableau de Bord / Gains</a> | 
        <a href="<?= site_url('operator/prefixes') ?>"><strong>Gestion des Prefixes</strong></a> | 
        <a href="<?= site_url('operator/gestion_baremes') ?>">Gestion des Baremes</a> | 
        <a href="<?= site_url('operator/types_operation') ?>">Gestion des Types d'Operation</a>
    </div>

    <hr>

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

    <!-- Formulaire d'Ajout Prefixe Interne -->
    <div>
        <h3>Ajouter un prefixe (Operateur principal)</h3>
        <form action="<?= site_url('operator/prefixes/store') ?>" method="POST">
            <?= csrf_field() ?>
            <label for="prefixe">Prefixe :</label>
            <input type="text" id="prefixe" name="prefixe" maxlength="5" required placeholder="Ex: 034">
            <button type="submit">Ajouter</button>
        </form>
    </div>

    <br>
    <hr>

    <!-- Liste des Prefixes Internes -->
    <div>
        <h3>Prefixes autorises (Interne)</h3>
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

    <br>
    <hr>

    <!-- ======================================================== -->
    <!-- NOUVEAU : GESTION DES AUTRES OPERATEURS                  -->
    <!-- ======================================================== -->

    <div>
        <h3>Ajouter un autre operateur (Externe)</h3>
        <form action="<?= site_url('operator/prefixes/store_autre') ?>" method="POST">
            <?= csrf_field() ?>
            <label for="prefixe_autre">Prefixe :</label>
            <input type="text" id="prefixe_autre" name="prefixe" maxlength="5" required placeholder="Ex: 032">
            
            &nbsp;&nbsp;
            <label for="nom_operateur">Nom Operateur :</label>
            <input type="text" id="nom_operateur" name="nom_operateur" required placeholder="Ex: Orange, Airtel">
            
            &nbsp;&nbsp;
            <label for="pourcentage">% Commission :</label>
            <input type="number" id="pourcentage" name="pourcentage" step="0.01" value="0" min="0" style="width: 70px;">
            
            &nbsp;&nbsp;
            <button type="submit">Ajouter l'operateur</button>
        </form>
    </div>

    <br>

    <div>
        <h3>Autres operateurs enregistres</h3>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom Operateur</th>
                    <th>Prefixe</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($autres_operateurs)): ?>
                    <?php foreach ($autres_operateurs as $ao): ?>
                        <tr>
                            <td><?= $ao['id'] ?></td>
                            <td><strong><?= esc($ao['nom_operateur']) ?></strong></td>
                            <td><?= esc($ao['prefixe']) ?></td>
                            <td>
                                <a href="<?= site_url('operator/prefixes/delete_autre/'.$ao['id']) ?>" onclick="return confirm('Supprimer cet operateur ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" align="center">Aucun autre operateur configure.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
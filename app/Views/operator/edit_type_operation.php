<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Type d Operation</title>
</head>
<body>

    <div>
        <h2>Modifier le type d operation</h2>
        <a href="<?= site_url('operator/types_operation') ?>">Annuler et retourner</a>
    </div>

    <hr>

    <?php if (session()->getFlashdata('error')): ?>
        <div style="color: red; font-weight: bold; margin-bottom: 10px;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('operator/types_operation/update/' . $type['id']) ?>" method="POST">
        <div>
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" value="<?= esc($type['nom']) ?>" required>
        </div>

        <br>

        <div>
            <a href="<?= site_url('operator/types_operation') ?>">Annuler</a>
            <button type="submit">Mettre a jour</button>
        </div>
    </form>

</body>
</html>
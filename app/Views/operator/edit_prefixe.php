<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le Prefixe</title>
</head>
<body>

    <div>
        <h2>Modifier le prefixe</h2>
        <a href="<?= site_url('operator/prefixes') ?>">Annuler et retourner</a>
    </div>

    <hr>

    <!-- Section Message Flash Erreur -->
    <?php if (session()->getFlashdata('error')): ?>
        <div style="color: red; font-weight: bold; margin-bottom: 10px;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- Formulaire de modification -->
    <form action="<?= site_url('operator/prefixes/update/'.$prefixe['id']) ?>" method="POST">
        <div>
            <label for="prefixe">Prefixe :</label>
            <input type="text" id="prefixe" name="prefixe" value="<?= esc($prefixe['prefixe']) ?>" maxlength="3" required>
        </div>
        
        <br>
        
        <div>
            <a href="<?= site_url('operator/prefixes') ?>">Annuler</a>
            <button type="submit">Mettre a jour</button>
        </div>
    </form>

</body>
</html>
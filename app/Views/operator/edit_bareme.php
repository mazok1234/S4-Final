<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la Tranche</title>
</head>
<body>

    <h2>Modifier la tranche de frais</h2>
    <a href="<?= site_url('operator/gestion_baremes') ?>">Annuler et retourner</a>
    <hr>

    <form action="<?= site_url('operator/gestion_baremes/update/'.$bareme['id']) ?>" method="POST">
        <div>
            <label>Montant Minimum :</label>
            <input type="number" step="0.01" name="montant_min" value="<?= $bareme['montant_min'] ?>" required>
        </div>
        <br>
        <div>
            <label>Montant Maximum :</label>
            <input type="number" step="0.01" name="montant_max" value="<?= $bareme['montant_max'] ?>" required>
        </div>
        <br>
        <div>
            <label>Frais (Ar) :</label>
            <input type="number" step="0.01" name="frais" value="<?= $bareme['frais'] ?>" required>
        </div>
        <br>
        <button type="submit">Mettre a jour</button>
    </form>

</body>
</html>
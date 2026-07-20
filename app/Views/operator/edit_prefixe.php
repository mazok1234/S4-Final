<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le Prefixe</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">Modifier le prefixe</div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger py-2"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>

                    <form action="<?= base_url('operator/prefixes/update/'.$prefixe['id']) ?>" method="POST">
                        <div class="mb-3">
                            <label for="prefixe" class="form-label">Prefixe</label>
                            <input type="text" class="form-control" id="prefixe" name="prefixe" value="<?= esc($prefixe['prefixe']) ?>" maxlength="3" required>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('operator/prefixes') ?>" class="btn btn-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">Mettre a jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
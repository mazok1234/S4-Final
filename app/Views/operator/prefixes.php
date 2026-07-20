<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Configuration des Prefixes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12 mb-4 d-flex justify-content-between align-items-center">
            <h2>Gestion des prefixes de l'operateur</h2>
            <a href="<?= base_url('operator/dashboard') ?>" class="btn btn-secondary">Retour Dashboard</a>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Ajouter un prefixe</div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger py-2"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success py-2"><?= session()->getFlashdata('success') ?></div>
                    <?php endif; ?>

                    <form action="<?= base_url('operator/prefixes/store') ?>" method="POST">
                        <div class="mb-3">
                            <label for="prefixe" class="form-label">Prefixe</label>
                            <input type="text" class="form-control" id="prefixe" name="prefixe" maxlength="3" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">Prefixes autorises</div>
                <div class="card-body">
                    <table class="table table-striped align-middle">
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
                                        <td><span class="badge bg-info text-dark fs-6"><?= $p['prefixe'] ?></span></td>
                                        <td>
                                            <a href="<?= base_url('operator/prefixes/edit/'.$p['id']) ?>" class="btn btn-warning btn-sm">Modifier</a>
                                            <a href="<?= base_url('operator/prefixes/delete/'.$p['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">Supprimer</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Aucun prefixe configure.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
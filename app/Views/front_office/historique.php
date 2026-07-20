<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Transactionnel</title>
    <style>
        :root {
            --primary: #2563eb;
            --success: #16a34a;
            --danger: #dc2626;
            --bg: #f8fafc;
            --card: #ffffff;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: system-ui, -apple-system, sans-serif; }
        body { background: var(--bg); padding: 2rem 1rem; }
        .container { max-width: 900px; margin: 0 auto; }
        
        .header-card {
            background: var(--card);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .solde-badge {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--success);
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        .alert-success { background: #dcfce7; color: var(--success); border: 1px solid #86efac; }
        .alert-error { background: #fee2e2; color: var(--danger); border: 1px solid #fca5a5; }

        .forms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .form-card {
            background: var(--card);
            padding: 1.25rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .form-card h3 {
            margin-bottom: 1rem;
            color: #1e293b;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 0.5rem;
        }

        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-size: 0.875rem; margin-bottom: 0.25rem; font-weight: 600; }
        .form-group input { width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; }

        .btn {
            width: 100%;
            padding: 0.6rem;
            border: none;
            border-radius: 6px;
            color: white;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-depot { background: var(--success); }
        .btn-retrait { background: #d97706; }
        .btn-transfert { background: var(--primary); }

        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--card);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        th, td { padding: 0.75rem 1rem; text-align: left; font-size: 0.875rem; }
        th { background: #f1f5f9; color: #475569; }
        tr:border-bottom { border-bottom: 1px solid #f1f5f9; }
    </style>
</head>
<body>

<div class="container">

    <!-- Header avec Solde -->
    <div class="header-card">
        <div>
            <h2>Compte Client : <?= esc($telephone) ?></h2>
            <p style="color: #64748b;">Gérez vos opérations en toute sécurité</p>
        </div>
        <div style="display:flex; align-items:center; gap:1rem;">
            <div class="solde-badge">
                <?= number_format($solde, 2, ',', ' ') ?> Ar
            </div>
            <?php if (session()->get('isLoggedIn')): ?>
            <a href="<?= site_url('auth/logout') ?>" 
               style="background-color:#dc2626; color:#fff; padding:0.45rem 1rem; border-radius:6px; text-decoration:none; font-weight:600; font-size:0.9rem; white-space:nowrap;">
                &#x23FB; Déconnecter
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Messages Réussite / Échec -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <!-- Grille des Formulaires -->
    <div class="forms-grid">
        
        <!-- Formulaire de Dépôt -->
        <div class="form-card">
            <h3>📥 Dépôt</h3>
            <form action="<?= base_url('depot') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Montant (Ar)</label>
                    <input type="number" name="montant" step="0.01" min="100" placeholder="Ex: 5000" required>
                </div>
                <button type="submit" class="btn btn-depot">Valider le Dépôt</button>
            </form>
        </div>

        <!-- Formulaire de Retrait -->
        <div class="form-card">
            <h3>📤 Retrait</h3>
            <form action="<?= base_url('retrait') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Montant (Ar)</label>
                    <input type="number" name="montant" step="0.01" min="100" placeholder="Ex: 2000" required>
                </div>
                <button type="submit" class="btn btn-retrait">Valider le Retrait</button>
            </form>
        </div>

        <!-- Formulaire de Transfert -->
        <div class="form-card">
            <h3>💸 Transfert</h3>
            <form action="<?= base_url('transfert') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Destinataire (Tél)</label>
                    <input type="tel" name="destinataire" placeholder="Ex: 0331234567" required>
                </div>
                <div class="form-group">
                    <label>Montant (Ar)</label>
                    <input type="number" name="montant" step="0.01" min="100" placeholder="Ex: 10000" required>
                </div>
                <button type="submit" class="btn btn-transfert">Valider le Transfert</button>
            </form>
        </div>

    </div>

    <!-- Historique des Transactions -->
    <h3 style="margin-bottom: 1rem; color: #1e293b;">Historique Récent</h3>
    <table>
        <thead>
            <tr>
                <th>Référence</th>
                <th>Opération</th>
                <th>Montant</th>
                <th>Frais</th>
                <th>Date</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($transactions)): ?>
                <tr><td colspan="6" style="text-align: center; color: #94a3b8;">Aucune transaction enregistrée.</td></tr>
            <?php else: ?>
                <?php foreach ($transactions as $txn): ?>
                    <tr>
                        <td><strong><?= esc($txn['reference']) ?></strong></td>
                        <td><?= strtoupper(esc($txn['operation'])) ?></td>
                        <td><?= number_format($txn['montant'], 2, ',', ' ') ?> Ar</td>
                        <td><?= number_format($txn['frais_appliques'], 2, ',', ' ') ?> Ar</td>
                        <td><?= esc($txn['date_transaction']) ?></td>
                        <td><span style="color: var(--success); font-weight: 600;"><?= esc($txn['statut']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</div>

</body>
</html>
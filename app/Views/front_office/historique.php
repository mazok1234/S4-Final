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
            <form id="transfert_form" action="<?= base_url('transfert') ?>" method="POST">
                <?= csrf_field() ?>
                
                <!-- Conteneur des destinataires -->
                <div class="form-group">
                    <label>Destinataire(s) (Tél)</label>
                    <div id="destinataires_container">
                        <div class="destinataire-row" style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem;">
                            <input type="tel" name="destinataire[]" class="destinataire-input" placeholder="Ex: 0331234567" required style="flex:1; padding:0.6rem; border:1px solid #cbd5e1; border-radius:6px;" oninput="updateApercu()">
                            <button type="button" class="btn-remove-dest" style="background:#dc2626; color:white; border:none; padding:0.6rem 0.8rem; border-radius:6px; font-weight:bold; cursor:pointer; display:none;" onclick="removeDest(this)">−</button>
                        </div>
                    </div>
                    <button type="button" id="btn_add_dest" style="background:#f1f5f9; color:#475569; border:1px dashed #cbd5e1; padding:0.5rem; border-radius:6px; font-weight:600; cursor:pointer; width:100%; margin-top:0.25rem; font-size:0.875rem;" onclick="addDest()">+ Ajouter un destinataire</button>
                </div>

                <div class="form-group">
                    <label>Montant total à diviser (Ar)</label>
                    <input type="number" id="transfert_montant" name="montant" step="0.01" min="100" placeholder="Ex: 10000" required oninput="updateApercu()">
                </div>
                
                <!-- Avertissement opérateur en cas de transfert multiple invalide -->
                <div id="operator_warning" style="display:none; font-size:0.8rem; color:#dc2626; background:#fee2e2; border:1px solid #fca5a5; border-radius:6px; padding:0.6rem 0.75rem; margin-bottom:0.75rem; line-height:1.6; font-weight:600;">
                    ⚠️ Le transfert multiple est réservé exclusivement aux numéros du même opérateur.
                </div>

                <!-- Aperçu dynamique -->
                <div id="transfert_apercu" style="display:none; font-size:0.8rem; color:#475569; background:#f1f5f9; border-radius:6px; padding:0.6rem 0.75rem; margin-bottom:0.75rem; line-height:1.6;">
                    <div><span id="apercu_label">Vous payez : </span><strong id="apercu_valeur">—</strong></div>
                    <div id="apercu_recoit" style="color:#16a34a; font-weight:600;"></div>
                </div>
                <button type="submit" id="btn_submit_transfert" class="btn btn-transfert">Valider le Transfert</button>
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

<script>
const validPrefixes = <?= json_encode($prefixes ?? []) ?>;
const senderPhone = "<?= esc($telephone) ?>";

function getPrefix(phone) {
    phone = phone.trim();
    for (var i = 0; i < validPrefixes.length; i++) {
        if (phone.indexOf(validPrefixes[i]) === 0) {
            return validPrefixes[i];
        }
    }
    return null;
}

function calculateFraisJS(montant) {
    if (montant >= 100 && montant <= 1000) return 50;
    if (montant > 1000 && montant <= 5000) return 50;
    if (montant > 5000 && montant <= 10000) return 100;
    if (montant > 10000 && montant <= 25000) return 200;
    if (montant > 25000 && montant <= 50000) return 400;
    if (montant > 50000 && montant <= 100000) return 800;
    if (montant > 100000 && montant <= 250000) return 1500;
    if (montant > 250000 && montant <= 500000) return 1500;
    if (montant > 500000 && montant <= 1000000) return 2500;
    if (montant > 1000000 && montant <= 2000000) return 3000;
    return 0;
}

function addDest() {
    var container = document.getElementById('destinataires_container');
    var row = document.createElement('div');
    row.className = 'destinataire-row';
    row.style.display = 'flex';
    row.style.alignItems = 'center';
    row.style.gap = '0.5rem';
    row.style.marginBottom = '0.5rem';
    
    row.innerHTML = '<input type="tel" name="destinataire[]" class="destinataire-input" placeholder="Ex: 0331234567" required style="flex:1; padding:0.6rem; border:1px solid #cbd5e1; border-radius:6px;" oninput="updateApercu()">' +
                    '<button type="button" class="btn-remove-dest" style="background:#dc2626; color:white; border:none; padding:0.6rem 0.8rem; border-radius:6px; font-weight:bold; cursor:pointer;" onclick="removeDest(this)">−</button>';
    
    container.appendChild(row);
    updateRemoveButtonsVisibility();
    updateApercu();
}

function removeDest(btn) {
    var container = document.getElementById('destinataires_container');
    var row = btn.parentNode;
    container.removeChild(row);
    updateRemoveButtonsVisibility();
    updateApercu();
}

function updateRemoveButtonsVisibility() {
    var container = document.getElementById('destinataires_container');
    var rows = container.getElementsByClassName('destinataire-row');
    for (var i = 0; i < rows.length; i++) {
        var removeBtn = rows[i].querySelector('.btn-remove-dest');
        if (rows.length > 1) {
            removeBtn.style.display = 'block';
        } else {
            removeBtn.style.display = 'none';
        }
    }
}

function updateApercu() {
    var montantInput = document.getElementById('transfert_montant');
    var apercuDiv    = document.getElementById('transfert_apercu');
    var apercuValeur = document.getElementById('apercu_valeur');
    var apercuLabel  = document.getElementById('apercu_label');
    var apercuRecoit = document.getElementById('apercu_recoit');
    var warningDiv   = document.getElementById('operator_warning');
    var submitBtn    = document.getElementById('btn_submit_transfert');

    // Collecter les destinataires
    var inputs = document.getElementsByClassName('destinataire-input');
    var destinataires = [];
    for (var i = 0; i < inputs.length; i++) {
        var val = inputs[i].value.trim();
        if (val !== '') destinataires.push(val);
    }
    destinataires = destinataires.filter(function(v, i, s) { return s.indexOf(v) === i; });

    var count = destinataires.length;
    var senderPrefix = getPrefix(senderPhone);

    // Validation opérateur pour transfert multiple
    var multiOperatorError = false;
    if (count > 1) {
        for (var i = 0; i < count; i++) {
            if (getPrefix(destinataires[i]) !== senderPrefix) {
                multiOperatorError = true;
                break;
            }
        }
    }

    if (multiOperatorError) {
        warningDiv.style.display = 'block';
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.5';
        submitBtn.style.cursor = 'not-allowed';
        apercuDiv.style.display = 'none';
        return;
    } else {
        warningDiv.style.display = 'none';
        submitBtn.disabled = false;
        submitBtn.style.opacity = '1';
        submitBtn.style.cursor = 'pointer';
    }

    var totalMontant = parseFloat(montantInput.value);
    if (isNaN(totalMontant) || totalMontant <= 0) {
        apercuDiv.style.display = 'none';
        return;
    }

    apercuDiv.style.display = 'block';

    var displayCount  = count || 1;
    var montantIndiv  = totalMontant / displayCount;
    var fraisTransfert, debitTotal;

    // Déterminer l'opérateur du premier destinataire (pour prévisualisation)
    var destPrefix    = destinataires.length > 0 ? getPrefix(destinataires[0]) : null;
    var isSameOperator = (destPrefix !== null && destPrefix === senderPrefix);

    if (displayCount > 1) {
        // Transfert multiple - forcément même opérateur (vérifié ci-dessus)
        fraisTransfert = calculateFraisJS(montantIndiv);
        var fraisRetrait = calculateFraisJS(montantIndiv); // même barème
        debitTotal = displayCount * (montantIndiv + fraisRetrait);
        apercuLabel.textContent  = 'Vous payez (total) : ';
        apercuValeur.textContent = debitTotal.toLocaleString('fr-FR', {minimumFractionDigits: 2}) + ' Ar';
        apercuRecoit.innerHTML   = 'Chaque destinataire (' + displayCount + ') reçoit : <strong>' +
            (montantIndiv + fraisRetrait).toLocaleString('fr-FR', {minimumFractionDigits: 2}) +
            ' Ar</strong> (frais de retrait inclus automatiquement — même opérateur)';
    } else {
        // Un seul destinataire
        fraisTransfert = calculateFraisJS(totalMontant);
        if (isSameOperator) {
            var fraisRetrait = calculateFraisJS(totalMontant);
            debitTotal = totalMontant + fraisRetrait;
            apercuLabel.textContent  = 'Vous payez : ';
            apercuValeur.textContent = debitTotal.toLocaleString('fr-FR', {minimumFractionDigits: 2}) + ' Ar';
            apercuRecoit.innerHTML   = 'Le destinataire reçoit : <strong>' +
                (totalMontant + fraisRetrait).toLocaleString('fr-FR', {minimumFractionDigits: 2}) +
                ' Ar</strong> <span style="color:#64748b; font-weight:normal;">(frais de retrait inclus automatiquement — même opérateur)</span>';
        } else if (destPrefix === null && destinataires.length === 0) {
            // Pas encore de destinataire saisi
            debitTotal = totalMontant + fraisTransfert;
            apercuLabel.textContent  = 'Vous payez : ';
            apercuValeur.textContent = debitTotal.toLocaleString('fr-FR', {minimumFractionDigits: 2}) + ' Ar';
            apercuRecoit.innerHTML   = 'Le destinataire reçoit : <strong>' +
                totalMontant.toLocaleString('fr-FR', {minimumFractionDigits: 2}) + ' Ar</strong>';
        } else {
            // Autre opérateur
            debitTotal = totalMontant + fraisTransfert;
            apercuLabel.textContent  = 'Vous payez : ';
            apercuValeur.textContent = debitTotal.toLocaleString('fr-FR', {minimumFractionDigits: 2}) + ' Ar';
            apercuRecoit.innerHTML   = 'Le destinataire reçoit : <strong>' +
                totalMontant.toLocaleString('fr-FR', {minimumFractionDigits: 2}) +
                ' Ar</strong> <span style="color:#64748b; font-weight:normal;">(frais de retrait : 0 Ar — autre opérateur)</span>';
        }
    }
}

document.getElementById('transfert_montant').addEventListener('input', updateApercu);
</script>

</body>
</html>
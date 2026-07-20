<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Client</title>
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --bg-color: #f3f4f6;
            --card-bg: #ffffff;
            --text-color: #1f2937;
            --error-color: #dc2626;
            --success-color: #16a34a;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--bg-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-card {
            background-color: var(--card-bg);
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-card h2 {
            color: var(--text-color);
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .login-card p.subtitle {
            color: #6b7280;
            font-size: 0.9rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        /* Notifications Flash Session */
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: var(--error-color);
            border: 1px solid #fca5a5;
        }

        .alert-success {
            background-color: #dcfce7;
            color: var(--success-color);
            border: 1px solid #86efac;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }

        .btn-submit {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-submit:hover {
            background-color: var(--primary-hover);
        }

        .btn-admin {
            width: 100%;
            padding: 0.75rem;
            background-color: #ffffff;
            color: #374151;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 0.75rem;
            transition: background-color 0.2s;
        }

        .btn-admin:hover {
            background-color: #f9fafb;
        }

        .admin-hint {
            font-size: 0.8rem;
            color: #9ca3af;
            text-align: center;
            margin-top: 0.4rem;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <h2>Connexion</h2>
        <p class="subtitle">Entrez votre numéro pour vous connecter ou créer un compte</p>

        <!-- Affichage du message d'erreur si présent en session -->
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <!-- Affichage du message de succès si présent en session (ex: déconnexion) -->
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('auth/login') ?>" method="POST">
            <?= csrf_field() ?> <!-- Protection CSRF obligatoire dans CI4 -->

            <div class="form-group">
                <label for="telephone">Numéro de téléphone</label>
                <input 
                    type="tel" 
                    id="telephone" 
                    name="telephone" 
                    placeholder="034 12 345 67" 
                    required
                >
            </div>

            <button type="submit" class="btn-submit">Valider</button>

            <!-- Accès opérateur -->
            <button type="button" class="btn-admin" onclick="document.getElementById('telephone').value='Admin'; this.form.submit();">
                &#9654; Accéder Admin
            </button>
            <p class="admin-hint"><strong></strong></p>
        </form>
    </div>

</body>
</html>
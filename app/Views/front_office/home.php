<ul>
    <li><strong>ID Client :</strong> <?= session('client_id') ?></li>
    <li><strong>Téléphone :</strong> <?= session('telephone') ?></li>
    <li><strong>Est connecté ? :</strong> <?= session('isLoggedIn') ? 'Oui' : 'Non' ?></li>
</ul>

<!-- Ou directement via la superglobale $_SESSION -->
<p>Numéro enregistré : <?= $_SESSION['telephone'] ?? 'Aucun' ?></p>
<div class="container">
    <nav class="navbar justify-content-center">
        <ul class="nav">
            <li class="nav-item">
                <a href="/logout" class="nav-link">Déconnexion</a>
            </li>
            <li class="nav-item">
                <a href="/users" class="nav-link">Liste des utilisateurs</a>
            </li>
        </ul>
    </nav>
    <h1 class="text-center">Home</h1>
    <p class="text-center">Bienvenue sur la page d'accueil,
        <?php echo $_SESSION['USER']->getName() ?>

    </p>
</div>


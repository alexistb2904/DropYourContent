<?php
const BY_SERVER = true;
include_once 'src/functions.php';

startSession();

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Crée, Partage, Communique et tout ça sur une seule application">
    <meta name="keywords" content="">
    <meta name="author" content="Alexis Thierry-Bellefond">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@alexistb2904">
    <meta name="twitter:title" content="DropYourContent - Accueil">
    <meta name="twitter:description" content="Crée, Partage, Communique et tout ça sur une seule application">
    <meta name="twitter:image" content="assets/images/logo_2K.webp">
    <!-- Balise de Langue -->
    <meta http-equiv="Content-Language" content="fr">
    <!-- Balise de Favicon (Logo) -->
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <!-- Balise de CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://kit.fontawesome.com/e1413d4c65.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&family=Roboto&display=swap" rel="stylesheet">
    <title>Accueil - DropYourContent</title>
</head>

<body id="home-page">
    <header>
        <?php include_once 'src/components/nav_bar.php' ?>
    </header>
    <main>
        <section class="home-text">
            <div class="home-text-div">
                <img src="assets/images/team.svg" alt="Two people in front of chat windows sitting">
                <h1>Partage, Communique, Réagi</h1>
                <?php if (isLogged()): ?>
                    <p>Bienvenue sur DropYourContent
                        <?php echo $_SESSION['user_name'] ?> !
                    </p>
                <?php else: ?>
                    <p>Notre application te permettra de faire un tas de nouvelles rencontre alors rejoins-nous maintenant !
                    </p>
                <?php endif; ?>
            </div>
            <div id="connection-button">
                <a class="log-in-button" onclick="showLogin(<?php echo isLogged() ?>)">
                    <button>Se Connecter</button>
                </a>
                <a class="sign-up-button" onclick="showSignUp(<?php echo isLogged() ?>)">
                    <button>S'inscrire</button>
                </a>
            </div>
        </section>
    </main>
    <footer>

    </footer>
    <script src="assets/js/login.js"></script>
</body>

</html>
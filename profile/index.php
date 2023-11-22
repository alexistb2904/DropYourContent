<?php
const BY_SERVER = true;
include_once '../src/functions.php';

startSession();

if (isset($_GET['user'])) {
    $userPage = $_GET['user'];
} else {
    $userPage = 'alexistb2904';
}

$integrityLogin = '../assets/js/login.js';
$integrityFeed = '../assets/js/feed.js';
$integrityStyle = '../assets/css/style.css';

$hashLogin = "sha256-" . base64_encode(hash_file('sha256', $integrityLogin, true));
$hashStyle = "sha256-" . base64_encode(hash_file('sha256', $integrityStyle, true));
$hashFeed = "sha256-" . base64_encode(hash_file('sha256', $integrityFeed, true));

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
    <meta name="twitter:image" content="../assets/images/logo_2K.webp">
    <!-- Balise de Langue -->
    <meta http-equiv="Content-Language" content="fr">
    <!-- Balise de Favicon (Logo) -->
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <!-- Balise de CSS -->
    <link rel="stylesheet" href="../assets/css/style.css" crossorigin="anonymous" integrity="<?php echo $hashStyle ?>">
    <script src="https://kit.fontawesome.com/e1413d4c65.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&family=Roboto&display=swap" rel="stylesheet">
    <title>Accueil - DropYourContent</title>
</head>

<body id="profile-page">
    <?php if (isLogged()) { ?>
        <nav>
            <aside>
                <div class="profile-side" style='background-image: url("../<?php echo getUserBackgroundPicture($_SESSION['user_name']) ?>")'>
                    <div class="profile-name">
                        <div class="profile-image" style='background-image: url("../<?php echo getUserProfilePicture($_SESSION['user_name']) ?>")'>
                        </div>
                        <ul>
                            <li>
                                <h2>
                                    <?php echo $_SESSION['user_name_full'] ?>
                                </h2>
                            </li>
                            <li>
                                <h3>
                                    <?php echo "@" . $_SESSION['user_name'] ?>
                                </h3>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="navigation-side">
                    <ul class="button-nav">
                        <li>
                            <a href="../accueil/">
                                <i class="fa-solid fa-house" style="color: #21A10C;"></i>Page Accueil
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <i class="fa-solid fa-user" style="color: #2353CF;"></i>Amis
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <i class="fa-solid fa-magnifying-glass" style="color: #E3621A;"></i>Recherche
                            </a>
                        </li>
                    </ul>
                    <ul>
                        <a href="">
                            <li><i class="fa-solid fa-gear" style="color: #606060;"></i> Paramètres</li>
                        </a>
                        <a onclick="showDisconnect()">
                            <li><i class="fa-solid fa-arrow-right-from-bracket" style="color: #606060;"></i> Déconnection</li>
                        </a>
                    </ul>
                </div>
            </aside>
        </nav>
        <main class="profile-page">
            <div class="profile-main" style='background-image: url("../<?php echo getUserBackgroundPicture($userPage) ?>")'>
                <div class="profile-name">
                    <span>
                        <div class="profile-image" style='background-image: url("../<?php echo getUserProfilePicture($userPage) ?>")'>
                        </div>
                        <ul>
                            <li>
                                <h2>
                                    <?php echo getUserByUsername($userPage)[0]['user_name_full'] ?>
                                </h2>
                            </li>
                            <li>
                                <h3>
                                    <?php echo "@" . getUserByUsername($userPage)[0]['user_name'] ?>
                                </h3>
                            </li>
                        </ul>
                    </span>
                    <button class="profile-button">Ajouter en Ami</button>
                    <!-- <button class="profile-button">Déjà en Ami</button> -->
                </div>
            </div>
            <?php if ($userPage == $_SESSION['user_name']) { ?>
                <form class="creator">
                    <label><span><i class="fa-solid fa-message"></i><textarea type="text" name="creator-message" id="creator-message" placeholder="Écrit quelque chose"
                                wrap="soft"></textarea></span><button type="submit" title="Envoyer le Post"><i class="fa-solid fa-paper-plane"></i></button></label>
                    <label><i class="fa-solid fa-camera"></i><span>Ajouter une photo</span><input type="file" name="creator-file" id="creator-file"
                            accept="image/png, image/jpeg, image/jpg, image/webp"><span class="error-message" id="error-form-message">Une Erreur est survenue veuillez
                            réesayer</span></label>
                </form>
            <?php } ?>
            <hr>
            <div class="main-feed">
                <?php
                $countOfPost = 0;
                foreach (getPosts($userPage) as $post) {
                    $countOfPost++ ?>
                    <div class="feed-card" id="<?php echo $post['id'] ?>">
                        <div class="feed-header">
                            <span class="feed-span">
                                <div class="feed-image" style="background-image :url(../<?php echo getUserProfilePicture($post['creator_user_name']) ?>)">
                                </div>
                                <div class="feed-name">
                                    <span>
                                        <?php echo getUserByUsername($post['creator_user_name'])[0]['user_name_full'] ?>
                                    </span><br>
                                    <span>@
                                        <?php echo getUserByUsername($post['creator_user_name'])[0]['user_name'] ?>
                                    </span>
                                </div>
                            </span>
                            <?php if ($post['creator_user_name'] == $_SESSION['user_name']) { ?>
                                <div class="feed-dots">
                                    <i class="fa-solid fa-ellipsis"></i>
                                    <div class="feed-dropdown">
                                        <span onclick="deletePost(<?php echo $post['id'] ?>)"><i class="fa-solid fa-trash"></i> Supprimer</span>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="feed-dots">
                                    <i class="fa-solid fa-ellipsis"></i>
                                    <div class="feed-dropdown">
                                        <span><i class="fa-regular fa-circle-user"></i> Aller sur le profil</span>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="feed-image">
                            <img src="../<?php echo $post['image'] ?>" alt="">
                        </div>
                        <div class="feed-content">
                            <?php echo $post['content'] ?>
                        </div>
                        <hr>
                        <div class="feed-information">
                            <span class="feed-span">
                                <div class="feed-likes">
                                    <?php if (isLiked($post['id'], $_SESSION['user_name'])) { ?>
                                        <i class="fa-solid fa-heart" style="color: red" onclick="likePost(event, <?php echo $post['id'] ?>)"></i>
                                    <?php } else { ?>
                                        <i class="fa-solid fa-heart" onclick="likePost(event, <?php echo $post['id'] ?>)"></i>
                                    <?php } ?>
                                    <span>
                                        <?php echo count(getLikesOfPost($post['id'])) ?>
                                    </span>
                                </div>
                                <!--
                                <div class="feed-comments">
                                    <i class="fa-solid fa-comment-dots"></i>
                                    210
                                </div> TODO: Add comments
                                -->
                            </span>
                            <div class="feed-date">
                                <span>
                                    Il y a
                                    <?php echo getCreationDatePost($post['id']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($countOfPost == 0) { ?>
                    <div class="feed-card">
                        Aucun post n'a été trouvé..
                    </div>
                <?php } ?>
            </div>
        </main>
    <?php } else { ?>
        <h1>Vous n'êtes pas connecté..<br>
            <p>Redirection dans 5 secondes</p>
        </h1>
        <script>
            let time = 5;
            function changeTime() {
                document.querySelector("p").innerHTML = "Redirection dans " + time + " secondes";
                time--;
                setTimeout(changeTime, 1000);
            }
            changeTime();
            setTimeout(() => {
                window.location.href = "../index.php";
            }, 5000);
        </script>
    <?php } ?>
    <script src="../assets/js/login.js" crossorigin="anonymous" integrity="<?php echo $hashLogin ?>"></script>
    <script src="../assets/js/feed.js" crossorigin="anonymous" integrity="<?php echo $hashFeed ?>"></script>
</body>

</html>
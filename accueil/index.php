<?php
const BY_SERVER = true;
include_once '../src/functions.php';

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
	<meta name="twitter:image" content="../assets/images/logo_2K.webp">
	<!-- Balise de Langue -->
	<meta http-equiv="Content-Language" content="fr">
	<!-- Balise de Favicon (Logo) -->
	<link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
	<!-- Balise de CSS -->
	<link rel="stylesheet" href="../assets/css/style.css">
	<script src="https://kit.fontawesome.com/e1413d4c65.js" crossorigin="anonymous"></script>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat&family=Roboto&display=swap" rel="stylesheet">
	<title>Accueil - DropYourContent</title>
</head>

<body id="base-page">
	<?php if (isLogged()) { ?>
		<nav>
			<aside>
				<div class="profile-side" style='background-image: url("../<?php echo getUserBackgroundPicture($_SESSION['user_name']) ?>")'>
					<div class="profile-name">
						<img src="../<?php echo getUserProfilePicture($_SESSION['user_name']) ?>" alt="">
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
							<a href="">
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
		<main>

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
	<script src="../assets/js/login.js"></script>
</body>

</html>
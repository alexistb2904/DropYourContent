function showLogin(isLogged = false) {
	// Création des variables éléments
	const connectionModal = document.createElement("div");
	connectionModal.className = "connection-modal";

	const modalContent = document.createElement("div");
	modalContent.className = "connection-modal-content";

	const modalClose = document.createElement("div");
	modalClose.className = "connection-modal-close";
	modalClose.innerHTML = '<i class="fa-solid fa-xmark"></i>';

	const header = document.createElement("div");
	header.innerHTML =
		'<p>Se Connecter</p><p class="error-message" id="connection-error">Nom d\'utilisateur ou mot de passe incorrect</p>';

	const loginForm = document.createElement("form");
	loginForm.id = "login-form";

	const labelUsername = document.createElement("label");
	labelUsername.innerHTML = 'Nom d\'utilisateur <input type="text" name="username" placeholder="Nom d\'utilisateur">';
	const usernameError = document.createElement("p");
	usernameError.className = "error-message";
	usernameError.id = "username-error";
	usernameError.innerHTML = "Nom d'utilisateur manquant";

	const labelPassword = document.createElement("label");
	labelPassword.id = "password-label";
	labelPassword.innerHTML =
		'<span>Mot de passe<i class="fa-regular fa-eye"></i></span> <input type="password" name="password" placeholder="Mot de passe">';
	const passwordError = document.createElement("p");
	passwordError.className = "error-message";
	passwordError.id = "password-error";

	const submitButton = document.createElement("button");
	submitButton.type = "submit";
	submitButton.innerHTML = "Se Connecter";

	const redirectMessage = document.createElement("p");
	redirectMessage.className = "redirect-connection";
	redirectMessage.id = "redirect-connection";
	redirectMessage.innerHTML = 'Pas encore inscrit ? <a href="">Inscris-toi</a>';

	const redirectDisconnect = document.createElement("p");
	redirectDisconnect.className = "redirect-connection";
	redirectDisconnect.innerHTML = 'Tu souhaite te déconnecter ? <a href="">Déconnecte-toi</a>';

	const loaderForFlex = document.createElement("div");
	const loader = document.createElement("div");
	loader.className = "loader-blob";

	const successMessage = document.createElement("p");
	successMessage.id = "success-message";
	successMessage.innerHTML =
		"Connection réussie !<br>Vous allez être redirigé vers la page d'accueil dans quelques secondes";

	const successImage = document.createElement("img");
	successImage.src = "../assets/images/thumbs_up.png";
	successImage.id = "success-image";
	successImage.alt = "Thumbs Up";

	const stopHandImage = document.createElement("img");
	stopHandImage.src = "../assets/images/stop_hand.png";
	stopHandImage.id = "success-image";
	stopHandImage.alt = "Stop Hand";

	// Création de la modal
	document.body.appendChild(connectionModal);
	connectionModal.appendChild(modalContent);
	modalContent.appendChild(modalClose);
	modalContent.appendChild(header);

	if (!isLogged) {
		modalContent.appendChild(loginForm);
		loginForm.appendChild(labelUsername);
		labelUsername.appendChild(usernameError);
		loginForm.appendChild(labelPassword);
		labelPassword.appendChild(passwordError);
		loginForm.appendChild(submitButton);
		modalContent.appendChild(redirectMessage);
	} else {
		header.innerHTML = "<p>Vous êtes déjà connecté</p>";
		modalContent.appendChild(stopHandImage);
		modalContent.appendChild(redirectDisconnect);
	}

	// Fermer la modal
	modalClose.addEventListener("click", function (e) {
		connectionModal.remove();
	});
	// Rediriger vers la page d'inscription
	redirectMessage.addEventListener("click", function (e) {
		e.preventDefault();
		connectionModal.remove();
		showSignUp();
	});

	// Rediriger vers la page de déconnection
	redirectDisconnect.addEventListener("click", function (e) {
		e.preventDefault();
		connectionModal.remove();
		showDisconnect();
	});

	// Afficher le mot de passe ou le cacher
	document.querySelector("#password-label i").addEventListener("click", function (e) {
		e.preventDefault();
		if (document.querySelector('#login-form input[name="password"]').type === "password") {
			document.querySelector('#login-form input[name="password"]').type = "text";
			document.querySelector("#password-label i").className = "fa-regular fa-eye-slash";
		} else {
			document.querySelector('#login-form input[name="password"]').type = "password";
			document.querySelector("#password-label i").className = "fa-regular fa-eye";
		}
	});

	loginForm.addEventListener("submit", function (e) {
		e.preventDefault(); // Empêcher le rechargement de la page
		// Récupérer les données du formulaire
		const formData = new FormData(loginForm);
		const username = formData.get("username");
		const password = formData.get("password");

		// Faire Apparaitre le loader
		loginForm.remove();
		header.remove();
		redirectMessage.remove();
		modalContent.appendChild(loader);
		modalContent.appendChild(loaderForFlex);
		function reappearForm() {
			// Faire réapparaitre le formulaire en cas d'erreur
			loader.remove();
			loaderForFlex.remove();
			modalContent.appendChild(header);
			modalContent.appendChild(loginForm);
			loginForm.appendChild(labelUsername);
			labelUsername.appendChild(usernameError);
			loginForm.appendChild(labelPassword);
			labelPassword.appendChild(passwordError);
			loginForm.appendChild(submitButton);
			modalContent.appendChild(redirectMessage);
		}
		fetch("../src/handle/login.php", {
			method: "POST",
			headers: {
				"Content-Type": "application/json",
			},
			body: JSON.stringify({ username: username, password: password }),
		})
			.then((response) => response.json())
			.then((json) => {
				if (json.error) {
					if (json.error === "badCombination") {
						reappearForm();
						document.querySelector("#connection-error").style.display = "block";
					} else if (json.error === "inexistantUser") {
						reappearForm();
						document.querySelector("#connection-error").style.display = "block";
						document.querySelector("#connection-error").innerHTML = "L'utilisateur n'existe pas";
					}
				} else {
					loader.remove();
					loaderForFlex.remove();
					modalContent.appendChild(successImage);
					modalContent.appendChild(successMessage);
					setTimeout(function () {
						connectionModal.remove();
						window.location.href = "../index.php";
					}, 3000);
				}
			})
			.catch((error) => console.log(error));
	});
}

function showSignUp(isLogged = false) {
	// Création des variables éléments
	const connectionModal = document.createElement("div");
	connectionModal.className = "connection-modal";

	const modalContent = document.createElement("div");
	modalContent.className = "connection-modal-content";

	const modalClose = document.createElement("div");
	modalClose.className = "connection-modal-close";
	modalClose.innerHTML = '<i class="fa-solid fa-xmark"></i>';

	const header = document.createElement("div");
	header.innerHTML =
		'<p>S\'inscrire</p><p class="error-message" id="connection-error">Erreur veuillez réessayer ultérieurement</p>';

	const signUpForm = document.createElement("form");
	signUpForm.id = "signUp-form";
	signUpForm.enctype = "multipart/form-data";

	const labelImage = document.createElement("label");
	labelImage.innerHTML = 'Photo de Profil (1024*1024 max) <input type="file" id="ProfilePictureInput" name="image">';
	const ImageError = document.createElement("p");
	ImageError.className = "error-message";
	ImageError.id = "image-error";

	const labelUsername = document.createElement("label");
	labelUsername.innerHTML = 'Nom d\'utilisateur <input type="text" name="username" placeholder="Nom d\'utilisateur">';
	const usernameError = document.createElement("p");
	usernameError.className = "error-message";
	usernameError.id = "username-error";

	const labelEmail = document.createElement("label");
	labelEmail.innerHTML = 'Email <input type="email" name="email" placeholder="Email">';
	const emailError = document.createElement("p");
	emailError.className = "error-message";
	emailError.id = "email-error";

	const labelPassword = document.createElement("label");
	labelPassword.id = "password-label";
	labelPassword.innerHTML =
		'<span>Mot de passe<i class="fa-regular fa-eye"></i></span> <input type="password" name="password" placeholder="Mot de passe">';
	const passwordError = document.createElement("p");
	passwordError.className = "error-message";
	passwordError.id = "password-error";

	const submitButton = document.createElement("button");
	submitButton.type = "submit";
	submitButton.innerHTML = "S'inscrire";

	const redirectMessage = document.createElement("p");
	redirectMessage.className = "redirect-connection";
	redirectMessage.innerHTML = 'Déjà un compte ? <a href="">Connecte-toi</a>';

	const redirectDisconnect = document.createElement("p");
	redirectDisconnect.className = "redirect-connection";
	redirectDisconnect.innerHTML = 'Tu souhaite te déconnecter ? <a href="">Déconnecte-toi</a>';

	const loaderForFlex = document.createElement("div");
	const loader = document.createElement("div");
	loader.className = "loader-blob";

	const successMessage = document.createElement("p");
	successMessage.id = "success-message";
	successMessage.innerHTML =
		"Inscription réussie !<br>Vous allez être redirigé vers la page de connexion dans quelques secondes";

	const successImage = document.createElement("img");
	successImage.src = "../assets/images/thumbs_up.png";
	successImage.id = "success-image";
	successImage.alt = "Thumbs Up";

	const stopHandImage = document.createElement("img");
	stopHandImage.src = "../assets/images/stop_hand.png";
	stopHandImage.id = "success-image";
	stopHandImage.alt = "Stop Hand";

	// Création de la modal
	document.body.appendChild(connectionModal);
	connectionModal.appendChild(modalContent);
	modalContent.appendChild(modalClose);
	modalContent.appendChild(header);
	if (!isLogged) {
		modalContent.appendChild(signUpForm);
		signUpForm.appendChild(labelImage);
		labelImage.appendChild(ImageError);
		signUpForm.appendChild(labelUsername);
		labelUsername.appendChild(usernameError);
		signUpForm.appendChild(labelEmail);
		labelEmail.appendChild(emailError);
		signUpForm.appendChild(labelPassword);
		labelPassword.appendChild(passwordError);
		signUpForm.appendChild(submitButton);
		modalContent.appendChild(redirectMessage);
	} else {
		header.innerHTML = "<p>Vous êtes déjà connecté</p>";
		modalContent.appendChild(stopHandImage);
		modalContent.appendChild(redirectDisconnect);
	}

	// Fermer la modal
	modalClose.addEventListener("click", function (e) {
		connectionModal.remove();
	});

	// Rediriger vers la page de connexion
	redirectMessage.addEventListener("click", function (e) {
		e.preventDefault();
		connectionModal.remove();
		showLogin();
	});

	// Rediriger vers la page de déconnection
	redirectDisconnect.addEventListener("click", function (e) {
		e.preventDefault();
		connectionModal.remove();
		showDisconnect();
	});

	// Afficher le mot de passe ou le cacher
	document.querySelector("#password-label i").addEventListener("click", function (e) {
		e.preventDefault();
		if (document.querySelector('#signUp-form input[name="password"]').type === "password") {
			document.querySelector('#signUp-form input[name="password"]').type = "text";
			document.querySelector("#password-label i").className = "fa-regular fa-eye-slash";
		} else {
			document.querySelector('#signUp-form input[name="password"]').type = "password";
			document.querySelector("#password-label i").className = "fa-regular fa-eye";
		}
	});

	signUpForm.addEventListener("submit", function (e) {
		e.preventDefault(); // Empêcher le rechargement de la page
		// Initialisation des variables
		const regexEmail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
		const regexPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*[;'"`\\]).{6,}$/;
		const ProfilePictureInput = document.getElementById("ProfilePictureInput");
		let usernameCheck = false;
		let emailCheck = false;
		let passwordCheck = false;
		let imageCheck = false;
		// Vérification des données

		if (ProfilePictureInput.files[0] !== undefined) {
			const reader = new FileReader();

			reader.onload = function (e) {
				const img = new Image();
				img.src = e.target.result;

				img.onload = function () {
					if (ProfilePictureInput.files[0].size > 5000000) {
						ImageError.style.display = "block";
						ImageError.innerHTML = "L'image ne doit pas dépasser 5Mo";
						imageCheck = false;
					} else if (
						ProfilePictureInput.files[0].type !== "image/png" &&
						ProfilePictureInput.files[0].type !== "image/jpeg"
					) {
						ImageError.style.display = "block";
						ImageError.innerHTML = "L'image doit être au format png ou jpg";
						imageCheck = false;
					} else if (img.width > 1024 || img.height > 1024) {
						ImageError.style.display = "block";
						ImageError.innerHTML = "L'image ne doit pas dépasser 1024*1024";
						imageCheck = false;
					} else {
						ImageError.style.display = "none";
						imageCheck = true;
					}
				};

				img.onerror = function () {
					ImageError.style.display = "block";
					ImageError.innerHTML = "Erreur lors du chargement de l'image";
					imageCheck = false;
				};
			};

			reader.readAsDataURL(ProfilePictureInput.files[0]);
		} else {
			ImageError.style.display = "none";
			imageCheck = true;
		}

		if (document.querySelector('#signUp-form input[name="username"]').value.length < 3) {
			usernameError.style.display = "block";
			usernameError.innerHTML = "Nom d'utilisateur trop court (3 caractères minimum)";
			usernameCheck = false;
		} else if (document.querySelector('#signUp-form input[name="username"]').value.length > 20) {
			usernameError.style.display = "block";
			usernameError.innerHTML = "Nom d'utilisateur trop long (20 caractères maximum)";
			usernameCheck = false;
		} else if (
			document.querySelector('#signUp-form input[name="username"]').value ==
			document.querySelector('#signUp-form input[name="email"]').value
		) {
			usernameError.style.display = "block";
			usernameError.innerHTML = "Le nom d'utilisateur ne peut être le même que l'email";
			usernameCheck = false;
		} else {
			usernameError.style.display = "none";
			usernameCheck = true; // Pas d'erreur pour le nom d'utilisateur
		}

		if (!regexEmail.test(document.querySelector('#signUp-form input[name="email"]').value)) {
			emailError.style.display = "block";
			emailError.innerHTML = "Email invalide";
			emailCheck = false;
		} else {
			emailError.style.display = "none";
			emailCheck = true; // Pas d'erreur pour l'email
		}

		if (!regexPassword.test(document.querySelector('#signUp-form input[name="password"]').value)) {
			passwordError.style.display = "block";
			passwordError.innerHTML =
				"Le Mot de passe doit contenir au moins 6 caractères, une majuscule, un chiffre et un caractère spécial (!@#$%^&*)";
			passwordCheck = false;
		} else {
			passwordError.style.display = "none";
			passwordCheck = true; // Pas d'erreur pour le mot de passe
		}

		if (usernameCheck && emailCheck && passwordCheck) {
			// Si il n'y a pas d'erreur

			// Récupérer les données du formulaire
			const formData = new FormData(signUpForm);
			const username = formData.get("username");
			const email = formData.get("email");
			const password = formData.get("password");
			const profilePicture = document.getElementById("ProfilePictureInput");
			if (profilePicture.files[0] != undefined) {
				formData.append("file", profilePicture.files[0]);
			}

			// Faire Apparaitre le loader
			signUpForm.remove();
			header.remove();
			redirectMessage.remove();
			modalContent.appendChild(loader);
			modalContent.appendChild(loaderForFlex);
			function reappearForm() {
				// Faire réapparaitre le formulaire en cas d'erreur
				loader.remove();
				loaderForFlex.remove();
				modalContent.appendChild(header);
				modalContent.appendChild(signUpForm);
				signUpForm.appendChild(labelUsername);
				labelUsername.appendChild(usernameError);
				signUpForm.appendChild(labelEmail);
				labelEmail.appendChild(emailError);
				signUpForm.appendChild(labelPassword);
				labelPassword.appendChild(passwordError);
				signUpForm.appendChild(submitButton);
				modalContent.appendChild(redirectMessage);
			}

			fetch("../src/handle/signup.php", {
				method: "POST",
				body: formData,
			})
				.then((response) => response.json())
				.then((json) => {
					if (json.error) {
						if (json.error === "usernameUsed") {
							reappearForm();
							usernameError.style.display = "block";
							usernameError.innerHTML = "Nom d'utilisateur déjà utilisé";
						} else if (json.error === "usernameTooShort") {
							reappearForm();
							usernameError.style.display = "block";
							usernameError.innerHTML = "Nom d'utilisateur trop court (3 caractères minimum)";
						} else if (json.error === "usernameTooLong") {
							reappearForm();
							usernameError.style.display = "block";
							usernameError.innerHTML = "Nom d'utilisateur trop long (20 caractères maximum)";
						} else if (json.error === "usernameSameAsEmail") {
							reappearForm();
							usernameError.style.display = "block";
							usernameError.innerHTML = "Le nom d'utilisateur ne peut être le même que l'email";
						} else if (json.error === "passwordError") {
							reappearForm();
							passwordError.style.display = "block";
							passwordError.innerHTML =
								"Le Mot de passe doit contenir au moins 6 caractères, une majuscule, un chiffre et un caractère spécial (!@#$%^&*)";
						} else if (json.error === "emailUsed") {
							reappearForm();
							emailError.style.display = "block";
							emailError.innerHTML = "Email déjà utilisé";
						} else if (json.error === "emailError") {
							reappearForm();
							emailError.style.display = "block";
							emailError.innerHTML = "Email Invalide";
						} else if (json.error === "imageErrorUpload") {
							reappearForm();
							ImageError.style.display = "block";
							ImageError.innerHTML = "Une erreur est survenue lors du chargement de l'image";
						} else if (json.error === "imageTooLarge") {
							reappearForm();
							ImageError.style.display = "block";
							ImageError.innerHTML = "L'image ne doit pas dépasser 1024*1024";
						} else if (json.error === "imageTooBig") {
							reappearForm();
							ImageError.style.display = "block";
							ImageError.innerHTML = "L'image ne doit pas dépasser 5Mo";
						}
					} else {
						loader.remove();
						loaderForFlex.remove();
						modalContent.appendChild(successImage);
						modalContent.appendChild(successMessage);
						setTimeout(function () {
							connectionModal.remove();
							showLogin();
						}, 3000);
					}
				})
				.catch((error) => console.log(error));
		}
	});
}

function showDisconnect() {
	// Création des variables éléments
	const connectionModal = document.createElement("div");
	connectionModal.className = "connection-modal";

	const modalContent = document.createElement("div");
	modalContent.className = "connection-modal-content";

	const modalClose = document.createElement("div");
	modalClose.className = "connection-modal-close";
	modalClose.innerHTML = '<i class="fa-solid fa-xmark"></i>';

	const header = document.createElement("div");
	header.innerHTML =
		'<p>Déconnection</p><p class="error-message" id="connection-error">Erreur veuillez réessayer ultérieurement</p>';

	const disconnectButton = document.createElement("button");
	disconnectButton.type = "submit";
	disconnectButton.id = "disconnectButton";
	disconnectButton.innerHTML = "Voulez-vous vraiment vous déconnecter ?";

	const loaderForFlex = document.createElement("div");
	const loader = document.createElement("div");
	loader.className = "loader-blob";

	const successMessage = document.createElement("p");
	successMessage.id = "success-message";
	successMessage.innerHTML =
		"Déconnection réussie !<br>Vous allez être redirigé vers la page d'accueil dans quelques secondes";

	const successImage = document.createElement("img");
	successImage.src = "../assets/images/thumbs_up.png";
	successImage.id = "success-image";
	successImage.alt = "Thumbs Up";

	const stopHandImage = document.createElement("img");
	stopHandImage.src = "../assets/images/stop_hand.png";
	stopHandImage.id = "success-image";
	stopHandImage.alt = "Stop Hand";

	// Création de la modal
	document.body.appendChild(connectionModal);
	connectionModal.appendChild(modalContent);
	modalContent.appendChild(modalClose);
	modalContent.appendChild(header);
	modalContent.appendChild(disconnectButton);

	// Fermer la modal
	modalClose.addEventListener("click", function (e) {
		connectionModal.remove();
	});

	disconnectButton.addEventListener("click", function (e) {
		e.preventDefault(); // Empêcher le rechargement de la page

		// Faire Apparaitre le loader
		disconnectButton.remove();
		header.remove();
		modalContent.appendChild(loader);
		modalContent.appendChild(loaderForFlex);
		function reappearForm() {
			// Faire réapparaitre le formulaire en cas d'erreur
			loader.remove();
			loaderForFlex.remove();
			modalContent.appendChild(header);
			modalContent.appendChild(disconnectButton);
		}

		fetch("../src/handle/disconnect.php", {
			method: "POST",
		})
			.then((response) => response.json())
			.then((json) => {
				if (json.error) {
					if (json.error === "disconnectError") {
						reappearForm();
						document.querySelector("#connection-error").style.display = "block";
						document.querySelector("#connection-error").innerHTML = "Erreur lors de la déconnection..";
					}
				} else {
					loader.remove();
					loaderForFlex.remove();
					modalContent.appendChild(successImage);
					modalContent.appendChild(successMessage);
					setTimeout(function () {
						connectionModal.remove();
						window.location.href = "../index.php";
					}, 3000);
				}
			})
			.catch((error) => console.log(error));
	});
}

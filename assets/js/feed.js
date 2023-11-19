const messageInput = document.querySelector('.creator label:nth-child(1) textarea');
const fileInput = document.querySelector('.creator label:nth-child(2) input');
const fileLabel = document.querySelector('.creator label:nth-child(2) span');
const fileLabelFull = document.querySelector('.creator label:nth-child(1)');
const creatorForm = document.querySelector('.creator');

fileInput.addEventListener('change', function (e) {
	let fileName = '';
	if (this.files && this.files.length > 1) {
		fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
	} else {
		fileName = e.target.value.split('\\').pop();
	}

	if (fileName) fileLabel.innerHTML = fileName;
	else fileLabel.innerHTML = 'Ajouter une photo';
});

creatorForm.addEventListener('submit', function (e) {
	e.preventDefault(); // Empêcher le rechargement de la page

	// Création des variables éléments
	const creationFormModal = document.createElement('div');
	creationFormModal.className = 'connection-modal';

	const modalContent = document.createElement('div');
	modalContent.className = 'connection-modal-content';

	const modalClose = document.createElement('div');
	modalClose.className = 'connection-modal-close';
	modalClose.innerHTML = '<i class="fa-solid fa-xmark"></i>';

	const loaderForFlex = document.createElement('div');
	const loader = document.createElement('div');
	loader.className = 'loader-blob';

	const successMessage = document.createElement('p');
	successMessage.id = 'success-message';
	successMessage.innerHTML = 'Création reussi !<br>Redirection en cours...';

	const successImage = document.createElement('img');
	successImage.src = '../assets/images/thumbs_up.png';
	successImage.id = 'success-image';
	successImage.alt = 'Thumbs Up';

	const stopHandImage = document.createElement('img');
	stopHandImage.src = '../assets/images/stop_hand.png';
	stopHandImage.id = 'success-image';
	stopHandImage.alt = 'Stop Hand';

	// Initialisation des variables
	const ErrorFormMessage = document.getElementById('error-form-message');

	let messageCheck = false;
	let imageCheck1 = false;
	let imageWidth1 = 0;
	// Vérification des données

	function ErrorAnimation($element) {
		if ($element.style.animation === 'animation: shake 0.3s ease forwards;') $element.style.animation = '';
		else $element.style.animation = 'animation: shake 0.3s ease forwards;';
	}

	function ImageCheck($image = undefined) {
		console.log('Test1');
		if ($image !== undefined) {
			const reader = new FileReader();

			reader.onload = function (e) {
				console.log('Test2');
				const img = new Image();
				img.src = e.target.result;

				img.onload = function () {
					console.log('Test3');
					if ($image.size > 5000000) {
						ErrorFormMessage.style.display = 'block';
						ErrorFormMessage.innerHTML = "L'image ne doit pas dépasser 5Mo JAVA";
						console.log("L'image ne doit pas dépasser 5Mo JAVA");
						imageCheck1 = false;
					} else if ($image.type !== 'image/png' && $image.type !== 'image/jpeg' && $image.type !== 'image/jpg' && $image.type !== 'image/webp') {
						ErrorFormMessage.style.display = 'block';
						ErrorFormMessage.innerHTML = "L'image doit être au format png, jpg ou webp";
						console.log("L'image doit être au format png, jpg ou webp");
						imageCheck1 = false;
					} else if (img.width > 4096 || img.height > 4096) {
						ErrorFormMessage.style.display = 'block';
						ErrorFormMessage.innerHTML = "L'image ne doit pas dépasser 4096*4096";
						console.log("L'image ne doit pas dépasser 4096*4096");
						imageCheck1 = false;
					} else {
						imageCheck1 = true;
						imageWidth1 = img.width;
					}
				};

				img.onerror = function () {
					ErrorFormMessage.style.display = 'block';
					ErrorFormMessage.innerHTML = "Erreur lors du chargement de l'image";
					console.log("Erreur lors du chargement de l'image");
					imageCheck1 = false;
				};
			};

			reader.readAsDataURL($image);
		} else {
			ErrorFormMessage.style.display = 'block';
			ErrorFormMessage.innerHTML = 'Une Image est requise';
			console.log('Une Image est requise');
			imageCheck1 = false;
		}
	}

	ImageCheck(fileInput.files[0]);

	if (messageInput.value.length > 255) {
		ErrorFormMessage.style.display = 'block';
		ErrorFormMessage.innerHTML = 'Message trop long (255 caractères minimum)';
		messageCheck = false;
		ErrorAnimation(fileLabelFull);
	} else if (messageInput.value.length < 5) {
		ErrorFormMessage.style.display = 'block';
		ErrorFormMessage.innerHTML = 'Message trop court (5 caractères minimum)';
		messageCheck = false;
		ErrorAnimation(fileLabelFull);
	} else {
		messageCheck = true;
	}
	setTimeout(function () {
		if (messageCheck && imageCheck1) {
			// Création de la modal
			document.body.appendChild(creationFormModal);
			creationFormModal.appendChild(modalContent);
			//modalContent.appendChild(modalClose);
			modalContent.appendChild(loader);
			// Fermer la modal
			modalClose.addEventListener('click', function (e) {
				creationFormModal.remove();
			});

			// Si il n'y a pas d'erreur
			ErrorFormMessage.style.display = 'none';
			// Récupérer les données du formulaire
			const formData = new FormData(creatorForm);
			const message = formData.get('creator-message');

			function ImageAppend($image = undefined) {
				if ($image != undefined) {
					formData.append('file', $image);
					formData.append('imageWidth', imageWidth1);
				}
			}

			ImageAppend(fileInput.files[0]);

			function disappearLoader() {
				creationFormModal.remove();
			}
			fetch('../src/handle/createPost.php', {
				method: 'POST',
				body: formData,
			})
				.then((response) => response.json())
				.then((json) => {
					if (json.error) {
						if (json.error === 'messageTooShort') {
							disappearLoader();
							ErrorAnimation(fileLabelFull);
							ErrorFormMessage.style.display = 'block';
							ErrorFormMessage.innerHTML = 'Message trop court (5 caractères minimum)';
						} else if (json.error === 'messageTooLong') {
							disappearLoader();
							ErrorAnimation(fileLabelFull);
							ErrorFormMessage.style.display = 'block';
							ErrorFormMessage.innerHTML = 'Message trop long (255 caractères minimum)';
						} else if (json.error === 'notLogged') {
							disappearLoader();
							ErrorAnimation(fileLabelFull);
							ErrorFormMessage.style.display = 'block';
							ErrorFormMessage.innerHTML = "Tu n'est pas connecté";
						} else if (json.error === 'imageErrorUpload') {
							disappearLoader();
							ErrorAnimation(fileLabelFull);
							ErrorFormMessage.style.display = 'block';
							ErrorFormMessage.innerHTML = "Une erreur est survenue lors du chargement de l'image";
						} else if (json.error === 'imageTooLarge') {
							disappearLoader();
							ErrorAnimation(fileLabelFull);
							ErrorFormMessage.style.display = 'block';
							ErrorFormMessage.innerHTML = "L'image ne doit pas dépasser 4096*4096";
						} else if (json.error === 'imageTooBig') {
							disappearLoader();
							ErrorAnimation(fileLabelFull);
							ErrorFormMessage.style.display = 'block';
							ErrorFormMessage.innerHTML = "L'image ne doit pas dépasser 5Mo";
						}
					} else {
						loader.remove();
						loaderForFlex.remove();
						modalContent.appendChild(successImage);
						modalContent.appendChild(successMessage);
						setTimeout(function () {
							creationFormModal.remove();
						}, 10000);
					}
				})
				.catch((error) => console.log(error));
		} else {
			ErrorFormMessage.style.display = 'block';
		}
	}, 500);
});

function likePost(event, $postId) {
	const likeForm = new FormData();
	const postID = $postId;
	likeForm.append('postId', $postId);
	fetch('../src/handle/likePost.php', {
		method: 'POST',
		body: likeForm,
	})
		.then((response) => response.json())
		.then((json) => {
			if (json.error) {
				if (json.error === 'notLogged') {
					console.log("Tu n'est pas connecté");
				} else if (json.error === 'postNotExist') {
					console.log("Le post n'existe pas");
				}
			} else if (json.success == 'likeDeleted') {
				console.log('Like supprimé');
				event.target.style.color = '';

				document.querySelector(`.feed-card[id='${postID}'] .feed-likes span`).innerHTML = +document.querySelector(`.feed-card[id='${postID}'] .feed-likes span`).innerHTML - 1;
				event.target.style.animation = '1s ease 0s 1 normal none running likeAnimation';
				setTimeout(function () {
					event.target.style.animation = '';
				}, 1000);
			} else {
				console.log('Like ajouté');
				event.target.style.color = 'red';
				document.querySelector(`.feed-card[id='${postID}'] .feed-likes span`).innerHTML = +document.querySelector(`.feed-card[id='${postID}'] .feed-likes span`).innerHTML + 1;
				event.target.style.animation = '1s ease 0s 1 normal none running likeAnimation';
				setTimeout(function () {
					event.target.style.animation = '';
				}, 1000);
			}
		})
		.catch((error) => console.log(error));
}

function deletePost($postId) {
	const deleteForm = new FormData();
	const postID = $postId;
	deleteForm.append('postId', $postId);
	fetch('../src/handle/deletePost.php', {
		method: 'POST',
		body: deleteForm,
	})
		.then((response) => response.json())
		.then((json) => {
			if (json.error) {
				if (json.error === 'notLogged') {
					alert("Tu n'est pas connecté");
				} else if (json.error === 'postNotExist') {
					alert("Le post n'existe pas");
				} else if (json.error === 'notOwner') {
					alert("Ce n'est pas ton post");
				}
			} else {
				alert('Post supprimé');
				document.querySelector(`.feed-card[id='${postID}']`).remove();
			}
		})
		.catch((error) => console.log(error));
}

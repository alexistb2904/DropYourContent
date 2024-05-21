# DropYourContent - Réseau Social - Pas Fini et ne le sera surement pas

DropYourContent est une application web de réseau social permettant aux utilisateurs de créer des publications, d'interagir avec du contenu, de personnaliser leurs profils, d'ajouter des amis, d'échanger des messages privés, de créer des comptes, de se connecter et de se déconnecter.

## Fonctionnalités

- **Créer un Post:** Les utilisateurs peuvent créer et publier du contenu.
- **Likes et Commentaires:** Les utilisateurs peuvent interagir avec les publications en les likant et en laissant des commentaires.
- **Personnalisation de Profil:** Possibilité de personnaliser les profils des utilisateurs.
- **Gestion des Amis TODO:** Ajouter, supprimer et gérer une liste d'amis.
- **Messagerie Privée TODO:** Échanger des messages privés avec d'autres utilisateurs.
- **Authentification:** Création de comptes, connexion et déconnexion des utilisateurs.

## Technologies Utilisées

- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP ( Version de PHP : 8.2.4 )
- **Base de Données:** MySQL
- **Messagerie en Temps Réel TODO:** WebSockets

## Installation 

- **Base de donnée :** Simplement copier les intructions suivante ou importer le fichier .sql
  ```
  SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
  START TRANSACTION;
  SET time_zone = "+00:00";
  
  CREATE TABLE `comments` (
    `id` int(11) NOT NULL,
    `post_id` int(11) NOT NULL,
    `creator_name` varchar(20) NOT NULL,
    `message` text NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
  
  CREATE TABLE `messages` (
    `id` int(11) NOT NULL,
    `message` text NOT NULL,
    `receiver` text NOT NULL,
    `sender` text NOT NULL,
    `creation_date` timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tables des Messages Privé';
  
  
  CREATE TABLE `posts` (
    `id` int(11) NOT NULL,
    `content` text NOT NULL,
    `image` text NOT NULL,
    `likes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
    `creator_user_name` text NOT NULL,
    `creation_date` timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tables des Post';
  CREATE TABLE `users` (
    `id` int(11) NOT NULL,
    `user_profile_picture` text NOT NULL,
    `user_background_picture` text NOT NULL,
    `user_name` text NOT NULL,
    `user_name_full` text NOT NULL,
    `user_password` text NOT NULL,
    `user_email` text NOT NULL,
    `user_connection_id` int(11) NOT NULL DEFAULT -1,
    `creation_date` timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
  
  ALTER TABLE `comments`
    ADD PRIMARY KEY (`id`);
  ALTER TABLE `messages`
    ADD PRIMARY KEY (`id`);
  ALTER TABLE `posts`
    ADD PRIMARY KEY (`id`);
  ALTER TABLE `users`
    ADD PRIMARY KEY (`id`);
  ALTER TABLE `comments`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  ALTER TABLE `messages`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  ALTER TABLE `posts`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
  ALTER TABLE `users`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
  COMMIT;
  ```

## Preview

![image](https://github.com/alexistb2904/DropYourContent/assets/59259007/5a53f9be-9a96-4e7a-832c-551d4b8867ca)
![image](https://github.com/alexistb2904/DropYourContent/assets/59259007/4e31181a-8786-4295-b453-bb42e173aed6)
![image](https://github.com/alexistb2904/DropYourContent/assets/59259007/0ca3a806-6873-4251-babe-489b0cb76ff5)


  

## Licence

Ce projet est sous licence MIT. Veuillez consulter le fichier `LICENSE` pour plus de détails.

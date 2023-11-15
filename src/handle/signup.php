<?php
header('Content-Type: application/json');
require("../functions.php");

function checkIfUsernameExists($username)
{
    if ($username) {
        $query = $GLOBALS['mysqlClientPDO']->prepare('SELECT * FROM users WHERE user_name = :user_name');
        $query->execute([
            'user_name' => $username
        ]);
        // Vérifier le nombre de lignes retournées
        $num_rows = $query->rowCount();

        if ($num_rows > 0) {
            // Il y a un utilisateur existant
            return true;
        } else {
            // Aucun utilisateur trouvé
            return false;
        }
    } else {
        return false;
    }

}

function checkIfEmailExists($email)
{
    if ($email) {
        $query = $GLOBALS['mysqlClientPDO']->prepare('SELECT * FROM users WHERE user_email = :user_email');
        $query->execute([
            'user_email' => $email
        ]);
        // Vérifier le nombre de lignes retournées
        $num_rows = $query->rowCount();

        if ($num_rows > 0) {
            // Il y a une email existante
            return true;
        } else {
            // Aucun email trouvé trouvé
            return false;
        }
    } else {
        return false;
    }
}

function checkRegex($regex, $string)
{
    if ($regex == "username") {
        return preg_match('/^[a-zA-Z0-9_-]+$/', $string) === 1;
    } elseif ($regex == "email") {
        return preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $string) === 1;
    } elseif ($regex == "password") {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*[;\'"`\\\\]).{6,}$/', $string) === 1;
    } else {
        return false;
    }
}

function UploadImage($image, $username)
{
    $imageSource = $image['tmp_name'];
    if (getimagesize($imageSource)[0] > 1024 && getimagesize($imageSource)[1] > 1024) {
        return "imageTooLarge";
    }
    if (filesize($imageSource) > 5000000) {
        return "imageTooBig";
    }

    $folder_path = '../../assets/images/profilesPicture/' . $username;
    if (!is_dir($folder_path)) {
        mkdir($folder_path, 0777, true);
    }
    // Obtenez l'extension du fichier
    $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
    $image_content = file_get_contents($imageSource);
    $file_name = 'profilePicture_' . $username;
    $file_path = $folder_path . '/' . $file_name;

    // Enregistrez l'image avec son extension d'origine
    file_put_contents($file_path . '.' . $extension, $image_content);
    // Chargez l'image en fonction de l'extension
    switch ($extension) {
        case 'png':
            $image = imagecreatefrompng($file_path . '.' . $extension);
            $temp = imagecreatetruecolor(imagesx($image), imagesy($image));
            imagecopy($temp, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
            imagedestroy($image);
            $image = $temp;
            break;
        case 'jpeg':
        case 'jpg':
            $image = imagecreatefromjpeg($file_path . '.' . $extension);
            $temp = imagecreatetruecolor(imagesx($image), imagesy($image));
            imagecopy($temp, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
            imagedestroy($image);
            $image = $temp;
            break;
        case 'gif':
            $image = imagecreatefromgif($file_path . '.' . $extension);
            $temp = imagecreatetruecolor(imagesx($image), imagesy($image));
            imagecopy($temp, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
            imagedestroy($image);
            $image = $temp;
            break;
        default:
            $image = false;
            break;
    }

    if ($image == true) {
        // Créez un chemin pour l'image WebP
        $webp_file_path = $folder_path . '/' . $file_name . '.webp';
        // Convertir l'image en WebP
        if (imagewebp($image, $webp_file_path, 80)) {
            // Libération de la mémoire
            imagedestroy($image);
            // Suppression du fichier original
            unlink($file_path . '.' . $extension);
            return true;
        } else {
            // Gestion des erreurs lors de la conversion
            return "webpConversionError: " . error_get_last()['message'];
        }
    } else {
        return "imageErrorUpload";
    }
}

function Register($username, $email, $password)
{
    $stmt = $GLOBALS['mysqlClientPDO']->prepare('INSERT INTO users (user_name, user_email, user_password, user_profile_picture) VALUES (:user_name, :user_email, :user_password, :user_profile_picture)');
    $stmt->execute([
        'user_name' => $username,
        'user_email' => $email,
        'user_password' => $password,
        'user_profile_picture' => 'assets/images/profilesPicture/' . $username . '/profilePicture_' . $username . '.webp'
    ]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = $_POST;
    $username = htmlspecialchars($data["username"], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($data["email"], ENT_QUOTES, 'UTF-8');
    $password = password_hash(htmlspecialchars($data["password"], ENT_QUOTES, 'UTF-8'), PASSWORD_DEFAULT);

    $existingUsername = checkIfUsernameExists($username);
    $existingEmail = checkIfEmailExists($email);


    if ($existingUsername) {
        echo json_encode(['error' => 'usernameUsed']);
    } elseif (strlen($username) < 3) {
        echo json_encode(['error' => 'usernameTooShort']);
    } elseif (strlen($username) > 20) {
        echo json_encode(['error' => 'usernameTooLong']);
    } elseif ($username == $email) {
        echo json_encode(['error' => 'usernameSameAsEmail']);
    } elseif (!checkRegex("password", $password)) {
        echo json_encode(['error' => 'passwordError']);
    } elseif ($existingEmail) {
        echo json_encode(['error' => 'emailUsed']);
    } elseif (!checkRegex("email", $email)) {
        echo json_encode(['error' => 'emailError']);
    } elseif (UploadImage($_FILES['image'], $username) != true) {
        echo json_encode(['error' => UploadImage($_FILES['image']['tmp_name'], $username)]);
    } else {
        Register($username, $email, $password);
        sleep(1);
        echo json_encode(['success' => true]);
    }
} else {
    echo json_encode(['error' => 'Méthode non autorisée']);
}
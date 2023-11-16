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
    } elseif ($regex == "name") {
        return preg_match("/^[\w\'\-,.\s][^0-9_!¡?÷?¿\/\\\\+=@#$%^&*(){}|~<>;:[\]]{2,}$/u", $string) === 1;
    } elseif ($regex == "email") {
        return preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $string) === 1;
    } elseif ($regex == "password") {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*[;\'"`\\\\]).{6,}$/', $string) === 1;
    } else {
        return false;
    }
}

function UploadImage($image, $username, $imageWidth1, $imageWidth2)
{
    $imageSourceConst = $image;
    if ($image == $_FILES['image']) {
        $imageSource = $image['tmp_name'];
        if (getimagesize($imageSource)[0] > 4096 && getimagesize($imageSource)[1] > 4096) {
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
    } else {
        $imageSource = $image['tmp_name'];
        if (getimagesize($imageSource)[0] > 4096 && getimagesize($imageSource)[1] > 4096) {
            return "BimageTooLarge";
        }
        if (filesize($imageSource) > 5000000) {
            return "BimageTooBig";
        }

        $folder_path = '../../assets/images/backgroundProfile/' . $username;
        if (!is_dir($folder_path)) {
            mkdir($folder_path, 0777, true);
        }
        // Obtenez l'extension du fichier
        $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
        $image_content = file_get_contents($imageSource);
        $file_name = 'background_' . $username;
        $file_path = $folder_path . '/' . $file_name;
    }

    // Enregistrez l'image avec son extension d'origine
    file_put_contents($file_path . '.' . $extension, $image_content);
    // Chargez l'image en fonction de l'extension
    switch ($extension) {
        case 'png':
            $image = imagecreatefrompng($file_path . '.' . $extension);
            imagepalettetotruecolor($image);
            break;
        case 'jpeg':
        case 'jpg':
            $image = imagecreatefromjpeg($file_path . '.' . $extension);
            imagepalettetotruecolor($image);
            break;
        case 'gif':
            $image = imagecreatefromgif($file_path . '.' . $extension);
            imagepalettetotruecolor($image);
            break;
        default:
            $image = false;
            break;
    }

    if ($image == true) {
        // Créez un chemin pour l'image WebP
        $webp_file_path = $folder_path . '/' . $file_name . '.webp';
        // Convertir l'image en WebP
        if ($imageSourceConst == $_FILES['image']) {
            if (getimagesize($imageSource)[0] != $imageWidth1) {
                $image = imagerotate($image, -90, 0);
            }
        } else {
            if (getimagesize($imageSource)[0] != $imageWidth2) {
                $image = imagerotate($image, -90, 0);
            }
        }
        imagewebp($image, $webp_file_path, 80);
        // Libération de la mémoire
        imagedestroy($image);
        // Suppression du fichier original
        unlink($file_path . '.' . $extension);
        return true;

    } else {
        if ($image == $_FILES['image']) {
            return "imageErrorUpload";
        } else {
            return "BimageErrorUpload";
        }
    }

}

function Register($username, $name, $email, $password)
{
    $stmt = $GLOBALS['mysqlClientPDO']->prepare('INSERT INTO users (user_name, user_name_full, user_email, user_password, user_profile_picture, user_background_picture) VALUES (:user_name, :user_name_full, :user_email, :user_password, :user_profile_picture, :user_background_picture)');
    $stmt->execute([
        'user_name' => $username,
        'user_name_full' => $name,
        'user_email' => $email,
        'user_password' => $password,
        'user_profile_picture' => 'assets/images/profilesPicture/' . $username . '/profilePicture_' . $username . '.webp',
        'user_background_picture' => 'assets/images/backgroundProfile/' . $username . '/background_' . $username . '.webp'
    ]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = $_POST;
    $username = htmlspecialchars($data["username"], ENT_QUOTES, 'UTF-8');
    $name = htmlspecialchars($data["name"], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($data["email"], ENT_QUOTES, 'UTF-8');
    $password = htmlspecialchars(password_hash($data["password"], PASSWORD_DEFAULT), ENT_QUOTES, 'UTF-8');
    $imageWidth1 = intval(htmlspecialchars($data["imageWidth1"], ENT_QUOTES, 'UTF-8'));
    $imageWidth2 = intval(htmlspecialchars($data["imageWidth2"], ENT_QUOTES, 'UTF-8'));

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
    } elseif (!checkRegex("name", $name)) {
        echo json_encode(['error' => 'nameInvalid']);
    } elseif (!checkRegex("password", $password)) {
        echo json_encode(['error' => 'passwordError']);
    } elseif ($existingEmail) {
        echo json_encode(['error' => 'emailUsed']);
    } elseif (!checkRegex("email", $email)) {
        echo json_encode(['error' => 'emailError']);
    } elseif (UploadImage($_FILES['image'], $username, $imageWidth1, $imageWidth2) != true) {
        echo json_encode(['error' => UploadImage($_FILES['image']['tmp_name'], $username, $imageWidth1, $imageWidth2)]);
    } elseif (UploadImage($_FILES['image2'], $username, $imageWidth1, $imageWidth2) != true) {
        echo json_encode(['error' => UploadImage($_FILES['image2']['tmp_name'], $username, $imageWidth1, $imageWidth2)]);
    } else {
        Register($username, $name, $email, $password);
        sleep(1);
        echo json_encode(['success' => true]);
    }
} else {
    echo json_encode(['error' => 'Méthode non autorisée']);
}
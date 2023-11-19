<?php
header('Content-Type: application/json');
require("../functions.php");
startSession();

function UploadImage($image, $username, $imageWidth1)
{
    $imageSourceConst = $image;
    $imageSource = $image['tmp_name'];
    if (getimagesize($imageSource)[0] > 4096 && getimagesize($imageSource)[1] > 4096) {
        return "imageTooLarge";
    }
    if (filesize($imageSource) > 5000000) {
        return "imageTooBig";
    }

    $folder_path = '../../assets/images/postCreated/' . $username;
    if (!is_dir($folder_path)) {
        mkdir($folder_path, 0777, true);
    }
    // Obtenez l'extension du fichier
    $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
    $image_content = file_get_contents($imageSource);

    $GLOBALS['file_count'] = 0;
    foreach (glob($folder_path . '/*') as $file) {
        if (is_file($file)) {
            $GLOBALS['file_count']++;
        }
    }

    $file_name = 'post_' . $username . '_' . $GLOBALS['file_count'];
    $file_path = $folder_path . '/' . $file_name;

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
        case 'webp':
            $image = imagecreatefromwebp($file_path . '.' . $extension);
            $imageWebp = true;
            break;
        default:
            $image = false;
            break;
    }

    if ($image == true || $imageWebp == true) {
        // Créez un chemin pour l'image WebP
        $webp_file_path = $folder_path . '/' . $file_name . '.webp';
        // Convertir l'image en WebP
        if (getimagesize($imageSource)[0] != $imageWidth1) {
            $image = imagerotate($image, -90, 0);
        }
        imagewebp($image, $webp_file_path, 80);
        // Libération de la mémoire
        imagedestroy($image);
        // Suppression du fichier original
        if (!$imageWebp) {
            unlink($file_path . '.' . $extension);
        }
        return true;

    } else {
        return "imageErrorUpload";
    }

}

function Register($username, $content)
{
    $stmt = $GLOBALS['mysqlClientPDO']->prepare('INSERT INTO posts (content, image, likes, creator_user_name) VALUES (:content, :image, :likes, :creator_user_name)');
    $stmt->execute([
        'creator_user_name' => $username,
        'content' => $content,
        'likes' => json_encode(array()),
        'image' => 'assets/images/postCreated/' . $username . '/post_' . $username . '_' . $GLOBALS['file_count'] . '.webp',
    ]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = $_POST;
    $username = $_SESSION['user_name'];
    $message = htmlspecialchars($data["creator-message"], ENT_QUOTES, 'UTF-8');
    $imageWidth1 = intval(htmlspecialchars($data["imageWidth"], ENT_QUOTES, 'UTF-8'));


    if (!isLogged()) {
        echo json_encode(['error' => 'notLogged']);
    } elseif (strlen($message) < 5) {
        echo json_encode(['error' => 'messageTooShort']);
    } elseif (strlen($message) > 255) {
        echo json_encode(['error' => 'messageTooLong']);
    } elseif (UploadImage($_FILES['file'], $username, $imageWidth1) != true) {
        echo json_encode(['error' => UploadImage($_FILES['file']['tmp_name'], $username, $imageWidth1)]);
    } else {
        Register($username, $message);
        sleep(1);
        echo json_encode(['success' => true]);
    }
} else {
    echo json_encode(['error' => 'Méthode non autorisée']);
}
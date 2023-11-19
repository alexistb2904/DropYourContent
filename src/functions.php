<?php
$mysqlHost = 'localhost';
$mysqlUser = 'root';
$mysqlPassword = '';
$mysqlName = 'dropyourcontent';
$mysqlPort = 3306;

try {
    $GLOBALS['mysqlClientPDO'] = new PDO(
        sprintf('mysql:host=%s;dbname=%s;port=%s', $mysqlHost, $mysqlName, 3306),
        $mysqlUser,
        $mysqlPassword
    );
    $GLOBALS['mysqlClientPDO']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $exception) {
    die('Erreur : ' . $exception->getMessage());
}

$GLOBALS['mysqlClient'] = mysqli_connect($mysqlHost, $mysqlUser, $mysqlPassword, $mysqlName);


function isLogged()
{
    if (isset($_SESSION['isLogged']) && ($_SESSION['isLogged'] === true) && isset($_SESSION['user_name']) && isset($_SESSION['user_name'])) {
        return true;
    } else {
        return false;
    }
}

function startSession()
{
    if (session_status() === PHP_SESSION_NONE) {
        return session_start();
    }
}


function NewMessage($message, $sender, $to): bool|int
{
    try {
        $query = $GLOBALS['mysqlClientPDO']->prepare('INSERT INTO messages (message, sender, receiver) VALUES (:message, :sender, :receiver)');
        $query->execute([
            'sender' => $sender,
            'receiver' => $to,
            'message' => $message
        ]);
        return $GLOBALS['mysqlClientPDO']->lastInsertId();
    } catch (Exception $exception) {
        return false;
    }
}

function getMessage($sender, $to): array
{
    $query = $GLOBALS['mysqlClientPDO']->prepare('SELECT * FROM messages WHERE sender = :sender AND receiver = :receiver OR sender = :receiver AND receiver = :sender ORDER BY timestamp ASC');
    $query->execute([
        'sender' => $sender,
        'receiver' => $to,
    ]);
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function getMessageById($id): array
{
    $query = $GLOBALS['mysqlClientPDO']->prepare('SELECT * FROM messages WHERE id = :id');
    $query->execute([
        'id' => $id
    ]);
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function getId($username): array
{
    if ($username) {
        $query = $GLOBALS['mysqlClientPDO']->prepare('SELECT user_connection_id FROM users WHERE user_name = :username');
        $query->execute([
            'username' => $username
        ]);
    }
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function getPosts($username = null): array
{
    if ($username == null) {
        $query = $GLOBALS['mysqlClientPDO']->prepare('SELECT * FROM posts ORDER BY creation_date DESC');
        $query->execute();
    } else {
        $query = $GLOBALS['mysqlClientPDO']->prepare('SELECT * FROM posts WHERE creator_user_name = :username ORDER BY creation_date DESC');
        $query->execute([
            'username' => $username
        ]);
    }
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function getUserByFullName($fullName): array
{
    if ($fullName) {
        $query = $GLOBALS['mysqlClientPDO']->prepare('SELECT * FROM users WHERE user_name_full = :fullName');
        $query->execute([
            'fullName' => $fullName
        ]);
    }
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function getUserByUsername($username): array
{
    if ($username) {
        $query = $GLOBALS['mysqlClientPDO']->prepare('SELECT * FROM users WHERE user_name = :username');
        $query->execute([
            'username' => $username
        ]);
    }
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function getUserProfilePicture($username): string
{
    if ($username) {
        $query = $GLOBALS['mysqlClientPDO']->prepare('SELECT user_profile_picture FROM users WHERE user_name = :username');
        $query->execute([
            'username' => $username
        ]);
    }
    return $query->fetchAll(PDO::FETCH_ASSOC)[0]['user_profile_picture'];
}

function getLikesOfPost($postId): array|string
{
    if ($postId) {
        $query = $GLOBALS['mysqlClientPDO']->prepare('SELECT likes FROM posts WHERE id = :postId');
        $query->execute([
            'postId' => $postId
        ]);
    }
    $result = json_decode($query->fetchAll(PDO::FETCH_ASSOC)[0]["likes"], true);
    if (!is_array($result)) {
        return json_decode($result, true);
    } else {
        return $result;
    }
}

function isLiked($postId, $username): bool
{
    getLikesOfPost($postId);
    foreach (getLikesOfPost($postId) as $like) {
        if ($like == $username) {
            return true;
        }
    }
    return false;
}

function isPostExist($postId): bool
{
    if ($postId) {
        $query = $GLOBALS['mysqlClientPDO']->prepare('SELECT * FROM posts WHERE id = :postId');
        $query->execute([
            'postId' => $postId
        ]);
    }
    if (count($query->fetchAll(PDO::FETCH_ASSOC)) > 0) {
        return true;
    }
    return false;
}

function getCreationDatePost($postId): string
{
    if ($postId) {
        $query = $GLOBALS['mysqlClientPDO']->prepare('SELECT creation_date FROM posts WHERE id = :postId');
        $query->execute([
            'postId' => $postId
        ]);
    }
    $timestampPost = $query->fetchAll(PDO::FETCH_ASSOC)[0]['creation_date'];
    $datePost = new DateTime($timestampPost);
    $dateNow = new DateTime();
    $interval = $datePost->diff($dateNow);
    if ($interval->y > 0) {
        return $interval->y . ' ans';
    } else if ($interval->m > 0) {
        return $interval->m . ' mois';
    } else if ($interval->d > 0) {
        return $interval->d . ' jours';
    } else if ($interval->h > 0) {
        return $interval->h . ' heures';
    } else if ($interval->i > 0) {
        return $interval->i . ' minutes';
    } else if ($interval->s > 0) {
        return $interval->s . ' secondes';
    }
    return '0 secondes';
}

function getUserBackgroundPicture($username): string
{
    if ($username) {
        $query = $GLOBALS['mysqlClientPDO']->prepare('SELECT user_background_picture FROM users WHERE user_name = :username');
        $query->execute([
            'username' => $username
        ]);
    }
    return $query->fetchAll(PDO::FETCH_ASSOC)[0]['user_background_picture'];
}
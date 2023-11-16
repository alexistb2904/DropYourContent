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
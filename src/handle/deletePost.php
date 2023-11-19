<?php
header('Content-Type: application/json');
require("../functions.php");
startSession();

function isOwning($username, $post_id)
{
    $stmt = $GLOBALS['mysqlClientPDO']->prepare('SELECT * FROM posts WHERE id = :id AND creator_user_name = :creator_user_name');
    $stmt->execute([
        'id' => $post_id,
        'creator_user_name' => $username
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        return true;
    } else {
        return false;
    }
}

function deletePost($post_id)
{
    $stmt = $GLOBALS['mysqlClientPDO']->prepare('DELETE FROM posts WHERE id = :id');
    $stmt->execute([
        'id' => $post_id
    ]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = $_POST;
    $username = $_SESSION['user_name'];
    $postId = $data['postId'];


    if (!isLogged()) {
        echo json_encode(['error' => 'notLogged']);
    } else if (!isPostExist($postId)) {
        echo json_encode(['error' => 'postNotExist']);
    } elseif (!isOwning($username, $postId)) {
        echo json_encode(['error' => 'notOwner']);
    } else {
        deletePost($postId);
        sleep(1);
        echo json_encode(['success' => true]);
    }
} else {
    echo json_encode(['error' => 'Méthode non autorisée']);
}
<?php
header('Content-Type: application/json');
require("../functions.php");
startSession();

function checkLikes($postId)
{
    $list = getLikesOfPost($postId);
    if (in_array($_SESSION['user_name'], $list)) {
        $key = array_search($_SESSION['user_name'], $list);
        unset($list[$key]);
    } else {
        array_push($list, $_SESSION['user_name']);
    }
    return $list;
}
function changeLikes($list, $postId)
{
    $list = json_encode($list);
    $query = $GLOBALS['mysqlClientPDO']->prepare('UPDATE posts SET likes = :likes WHERE id = :postId');
    $query->execute([
        'likes' => json_encode($list),
        'postId' => $postId
    ]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = $_POST;
    $postId = $data['postId'];

    if (!isLogged()) {
        echo json_encode(['error' => 'notLogged']);
    } elseif (!isPostExist($postId)) {
        echo json_encode(['error' => 'postNotExist']);
    } else {
        $list = checkLikes($postId);
        changeLikes($list, $postId);
        if (!in_array($_SESSION['user_name'], $list)) {
            echo json_encode(['success' => 'likeDeleted']);
        } else {
            echo json_encode(['success' => 'likeAdded']);
        }
    }
} else {
    echo json_encode(['error' => 'Méthode non autorisée']);
}
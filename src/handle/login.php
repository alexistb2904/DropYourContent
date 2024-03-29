<?php
header('Content-Type: application/json');
require("../functions.php");
// Lance la session si elle ne l'est pas déjà
startSession();

function checkExist($username)
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
    }
    return false;
}
function checkCombination($username, $password)
{
    if ($username && $password) {
        $userWithNameRequest = $GLOBALS['mysqlClientPDO']->prepare('SELECT * FROM users');
        $userWithNameRequest->execute();
        $users = $userWithNameRequest->fetchAll();
        $goodCombination = false;
        foreach ($users as $user) {
            if ((htmlspecialchars($user['user_name']) === htmlspecialchars($username)) && (password_verify(htmlspecialchars($password), htmlspecialchars($user['user_password'])))) {
                $loggedUser = ['logged' => true, 'user_name' => htmlspecialchars($user['user_name']), 'user_name_full' => htmlspecialchars($user['user_name_full']), 'user_email' => htmlspecialchars($user['user_email'])];
                $_SESSION['isLogged'] = $loggedUser['logged'];
                $_SESSION['user_name'] = $loggedUser['user_name'];
                $_SESSION['user_email'] = $loggedUser['user_email'];
                $_SESSION['user_name_full'] = $loggedUser['user_name_full'];
                $goodCombination = true;
                break;
            } else {
                $goodCombination = false;
            }
        }

        return $goodCombination;
    } else {
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = $_POST;
    $username = strtolower(htmlspecialchars($data["username"], ENT_QUOTES, 'UTF-8'));
    $password = htmlspecialchars($data["password"], ENT_QUOTES, 'UTF-8');

    if (!checkExist($username)) {
        echo json_encode(['error' => 'inexistantUser']);
    } else {
        if (checkCombination($username, $password)) {
            sleep(1);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'badCombination']);
        }
    }
} else {
    echo json_encode(['error' => 'Méthode non autorisée']);
}
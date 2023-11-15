<?php
// Lance la session si elle ne l'est pas déjà
session_start();

// Détruit la session, supprime les variables associées et redirige vers la page d'accueil
session_destroy();
unset($_SESSION['user_email']);
unset($_SESSION['user_name']);
$_SESSION['isLogged'] = false;

if (isset($_SESSION['user_email']) && isset($_SESSION['user_name'])) {
    echo json_encode(['error' => 'disconnectError']);
} else {
    sleep(1);
    echo json_encode(['success' => true]);
}

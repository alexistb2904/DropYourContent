<?php
$string = '["alexistb2904"]';

// Décoder la chaîne JSON en un tableau PHP
$array = json_decode($string, true);

// Vérifier si le décodage a été effectué avec succès
if ($array !== null && is_array($array)) {
    var_dump($array); // Affiche le contenu du tableau PHP
} else {
    echo "Erreur lors du décodage de la chaîne JSON en tableau.";
}
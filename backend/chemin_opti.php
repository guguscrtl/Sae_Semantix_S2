<?php
header('Content-Type: application/json');  // Important pour renvoyer du JSON

$file_chemin_opti = fopen('../out_java.txt', 'r');
$result = [];  // Créer un tableau pour stocker les résultats

if ($file_chemin_opti) {
    // Lire et ignorer la première ligne
    fgets($file_chemin_opti);

    // Continuer avec le reste du fichier
    while (($line = fgets($file_chemin_opti)) !== false) {
        // Séparer la ligne en utilisant l'espace comme séparateur
        $parts = explode(' ', $line);

        // Assurez-vous qu'il y a au moins deux mots avant de les assigner
        if (count($parts) >= 2) {
            $mot1 = trim($parts[0]);  // Utilisez trim pour enlever les espaces blancs
            $mot2 = trim($parts[1]);
            $score = trim($parts[2]);

            // Ajouter les mots au tableau de résultats
            $result[] = ["mot1" => $mot1, "mot2" => $mot2, "score" => $score];
        }
    }
    fclose($file_chemin_opti);

    // Renvoyer les données en format JSON
    echo json_encode($result);
} else {
    echo json_encode(["error" => "Impossible d'ouvrir le fichier."]);
}
?>

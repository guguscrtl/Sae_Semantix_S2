<?php $file = $_GET['file'];
$word = $_GET['word'];

$cheminFichier = $file;

// Ouvre le fichier en mode lecture
$file = fopen($cheminFichier, 'r');

if ($file) {
    $scores = array(); // Liste pour stocker tous les scores avec $mot1 = $word

    while (($line = fgets($file)) !== false) {
        // Divise la ligne en mots et distance
        $elements = explode(' ', $line);

        if (count($elements) === 3) {
            $mot1 = $elements[0];
            $mot2 = $elements[1];
            $distance = $elements[2];

            // Vérifie si $mot1 est égal à $word
            if ($mot1 === $word) {
                // Ajoute le score à la liste des scores
                $scores[] = $distance;
            }
        }
    }

    // Ferme le fichier
    fclose($file);

    // Trouve le score maximum dans la liste des scores
    if (isset($scores) && $scores != null)
    $maxScore = $scores[0];
    else echo 0;

    // Convertit la valeur de $maxScore en JavaScript et l'affecte à une variable JavaScript
    if (isset($maxScore)) {
        echo $maxScore;
    }
}

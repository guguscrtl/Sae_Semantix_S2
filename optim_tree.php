<?php

$id = $_GET['gameId'];

// Votre commande, avec redirection de la sortie d'erreur vers la sortie standard
$command = '.\jdk-21\bin\java -jar ./Partie-Java/Semantix-JAVA/out/artifacts/Semantix_JAVA_jar/Semantix-JAVA.jar optim ./save/'.$id.'.txt ./save_java/'.$id.'.txt 2>&1';

// Exécuter la commande
$output = shell_exec($command);

// Récupérer le contenu du fichier Java généré
$javaFile = './save_java/'.$id.'.txt';
$fileContent = file_get_contents($javaFile);

// Initialiser les tableaux de nœuds et d'arêtes
$nodes = [];
$edges = [];

// Parcourir chaque ligne du fichier Java
foreach (explode("\n", $fileContent) as $line) {
    // Séparer les mots et le score à partir de chaque ligne
    $data = explode(' ', $line);
    
    // Si la ligne n'est pas vide et contient au moins deux mots et un score
    if (!empty($data) && count($data) == 3) {
        // Extraire les mots et le score
        $word1 = $data[0];
        $word2 = $data[1];
        $score = $data[2];

        // Vérifier si le mot1 existe déjà dans les nœuds, sinon l'ajouter
        $node1Index = array_search($word1, array_column($nodes, 'label'));
        if ($node1Index === false) {
            $node1Index = count($nodes);
            $nodes[] = ['id' => $node1Index, 'label' => $word1];
        }

        // Vérifier si le mot2 existe déjà dans les nœuds, sinon l'ajouter
        $node2Index = array_search($word2, array_column($nodes, 'label'));
        if ($node2Index === false) {
            $node2Index = count($nodes);
            $nodes[] = ['id' => $node2Index, 'label' => $word2];
        }

        // Ajouter l'arête avec le score
        $edges[] = ['id' => count($edges), 'from' => $node1Index, 'to' => $node2Index, 'label' => (string)$score];
    }
}

// Combine nodes et edges dans un seul tableau
$data = [
    'command' => $command,
    'retour' => $output,
    'nodes' => $nodes,
    'edges' => $edges
];

// Encodez les données au format JSON et envoyez-les
header('Content-Type: application/json');
echo json_encode($data);
?>
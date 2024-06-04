<?php

$depart = $_GET['depart'];
$arrive = $_GET['arrive'];

$command = "..\jdk-21\bin\java -jar ../Partie-Java/Semantix-JAVA/out/artifacts/Semantix_JAVA_jar/Semantix-JAVA.jar score ../out_java.txt $depart $arrive 2>&1";
// Exécuter la commande et récupérer la sortie
$output = shell_exec($command);


// Convertir la sortie en entier (int)
$result = intval(trim($output));

// Vérifier si la conversion a réussi
if ($result !== 0 || $output === "0") {
    // La conversion en entier a réussi
    echo $result;
} else {
    // La conversion a échoué ou la sortie n'était pas un entier valide
    echo 5;
}
?>

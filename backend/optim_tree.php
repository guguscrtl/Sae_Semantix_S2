<?php

// Votre commande, avec redirection de la sortie d'erreur vers la sortie standard
$command = '..\jdk-21\bin\java -jar ../Partie-Java/Semantix-JAVA/out/artifacts/Semantix_JAVA_jar/Semantix-JAVA.jar optim ../partie.txt ../out_java.txt 2>&1';

// Exécuter la commande
$output = shell_exec($command);


?>


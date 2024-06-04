<?php

$id = $_GET['gameId'] ?? '';

if (empty($id)) {
    http_response_code(400);
    echo json_encode(['error' => 'gameId is required']);
    exit;
}

$filePath = './save/' . $id . '.txt';

if (!file_exists($filePath)) {
    http_response_code(404);
    echo json_encode(['error' => 'File not found']);
    exit;
}

$fileContent = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$compteur = 0;
$depart = "";
$arrive = "";

foreach ($fileContent as $line) {
    $data = explode(' ', $line);
    if (count($data) < 1) {
        continue; // Ignore lines that do not have enough data
    }
    if ($compteur == 1) {
        $depart = $data[0];
    }
    if ($compteur == 2) {
        $arrive = $data[0];
        break;
    }
    $compteur++;
}

if (empty($depart) || empty($arrive)) {
    http_response_code(500);
    echo json_encode(['error' => 'Insufficient data in file']);
    exit;
}

$command = ".\\jdk-21\\bin\\java -jar ./Partie-Java/Semantix-JAVA/out/artifacts/Semantix_JAVA_jar/Semantix-JAVA.jar score ./save_java/" . $id . ".txt $depart $arrive 2>&1";

// Debugging: log the command
file_put_contents('debug.log', "Executing command: $command\n", FILE_APPEND);

// Exécuter la commande et récupérer la sortie
$output = shell_exec($command);

// Debugging: log the output
file_put_contents('debug.log', "Command output: $output\n", FILE_APPEND);

if ($output === null) {
    http_response_code(500);
    echo json_encode(['error' => 'Command execution failed']);
    exit;
}

// Convertir la sortie en entier (int)
$result = trim($output);

$data = [
    'score' => $result,
    'out' => $output
];

// Encodez les données au format JSON et envoyez-les
header('Content-Type: application/json');
echo json_encode($data);

?>

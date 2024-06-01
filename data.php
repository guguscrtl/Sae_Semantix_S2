<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
$data = ["message" => "Bonjour du caca PHP!"];
echo json_encode($data);
?>
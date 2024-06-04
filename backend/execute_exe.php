<?php
$exe = $_GET['exe'];
$param = $_GET['param'];

$command = "" . $exe . " " . $param;

$output = shell_exec($command);

echo "<script>console.log('test : ' + $command);</script>";
?>

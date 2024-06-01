<?php
$exe = $_GET['exe'];
$param_s = $_GET['param_s'];
$param_f = $_GET['param_f'];
$param_w = $_GET['param_w'];

$command = "" . $exe . " " . $param_s . " " . $param_f . " " .$param_w;

$output = shell_exec($command);

echo "<script>console.log('test : ' + $command);</script>";
?>

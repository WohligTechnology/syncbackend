<?php 
header('Content-type: application/javascript');
header("Access-Control-Allow-Origin: *");

echo json_encode($message);
?>
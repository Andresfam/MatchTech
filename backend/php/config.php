<?php
$host = "sql3.freesqldatabase.com";  
$user = "sql3808768";                
$pass = "xbGCRFjiZG";      
$db   = "sql3808768";                
$port = 3306;                        

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die(json_encode([
        "status" => "error",
        "message" => "Error de conexión a la base de datos"
    ]));
}

$conn->set_charset("utf8");
?>
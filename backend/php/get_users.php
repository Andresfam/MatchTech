<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include "config.php";

$sql = "
    SELECT id_usuario, usuario, nombre, apellido, correo, rol
    FROM usuarios
    WHERE rol != 'admin'
    ORDER BY id_usuario DESC
";

$result = $conn->query($sql);
$users = [];

while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);
?>

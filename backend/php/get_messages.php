<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include "config.php";

$id_chat = $_GET["id_chat"] ?? 0;

$stmt = $conn->prepare("
    SELECT id_mensaje, rol, contenido, fecha
    FROM mensajes
    WHERE id_chat = ? AND eliminado = 0
    ORDER BY id_mensaje ASC
");
$stmt->bind_param("i", $id_chat);
$stmt->execute();

$result = $stmt->get_result();
$messages = [];

while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
?>

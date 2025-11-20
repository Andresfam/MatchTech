<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include "config.php";

$id_usuario = $_GET["id_usuario"] ?? 0;

$stmt = $conn->prepare("
    SELECT id_chat, titulo, fecha_creado, fecha_actualizado
    FROM chats
    WHERE id_usuario = ? AND eliminado = 0
    ORDER BY fecha_actualizado DESC
");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();

$result = $stmt->get_result();
$chats = [];

while ($row = $result->fetch_assoc()) {
    $chats[] = $row;
}

echo json_encode($chats);
?>

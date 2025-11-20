<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include "config.php";

$data = json_decode(file_get_contents("php://input"), true);
$id_usuario = $data["id_usuario"] ?? 0;

$stmt = $conn->prepare("SELECT id_chat FROM chats WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

$chats = [];
while ($row = $res->fetch_assoc()) {
    $chats[] = $row["id_chat"];
}

foreach ($chats as $chatId) {
    $conn->query("UPDATE mensajes SET eliminado = 1 WHERE id_chat = $chatId");
}

$conn->query("UPDATE chats SET eliminado = 1 WHERE id_usuario = $id_usuario");

$conn->query("DELETE FROM usuarios WHERE id_usuario = $id_usuario");

echo json_encode([
    "status" => "success",
    "message" => "Usuario, chats y mensajes eliminados correctamente."
]);

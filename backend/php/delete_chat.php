<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include "config.php";

$data = json_decode(file_get_contents("php://input"), true);
$id_chat = $data["id_chat"] ?? 0;

$conn->query("UPDATE mensajes SET eliminado = 1 WHERE id_chat = $id_chat");

$conn->query("UPDATE chats SET eliminado = 1 WHERE id_chat = $id_chat");

echo json_encode([
    "status" => "success",
    "message" => "Chat eliminado correctamente."
]);

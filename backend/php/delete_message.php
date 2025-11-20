<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include "config.php";

$data = json_decode(file_get_contents("php://input"), true);
$id_mensaje = $data["id_mensaje"] ?? 0;

$conn->query("UPDATE mensajes SET eliminado = 1 WHERE id_mensaje = $id_mensaje");

echo json_encode([
    "status" => "success",
    "message" => "Mensaje eliminado."
]);

<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include "config.php";

$data = json_decode(file_get_contents("php://input"), true);
$id = $data["id_usuario"] ?? 0;

$stmt = $conn->prepare("SELECT rol FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$newRole = ($user["rol"] === "baneado") ? "usuario" : "baneado";

$stmt2 = $conn->prepare("UPDATE usuarios SET rol = ? WHERE id_usuario = ?");
$stmt2->bind_param("si", $newRole, $id);
$stmt2->execute();

echo json_encode([
    "status" => "success",
    "new_rol" => $newRole
]);

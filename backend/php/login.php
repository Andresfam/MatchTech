<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

include "config.php";

$data = json_decode(file_get_contents("php://input"), true);

$login      = $data["login"] ?? "";
$contrasena = $data["contrasena"] ?? "";

if ($login === "" || $contrasena === "") {
    echo json_encode(["status" => "error", "message" => "Faltan datos"]);
    exit;
}

$sql = $conn->prepare("
    SELECT id_usuario, uid, usuario, nombre, apellido, correo, contrasena, rol
    FROM usuarios
    WHERE (usuario = ? OR correo = ?)
");
$sql->bind_param("ss", $login, $login);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Usuario no encontrado"]);
    exit;
}

$user = $result->fetch_assoc();


if (!password_verify($contrasena, $user["contrasena"])) {
    echo json_encode(["status" => "error", "message" => "Contraseña incorrecta"]);
    exit;
}


if ($user["rol"] === "baneado") {
    unset($user["contrasena"]);
    echo json_encode([
        "status" => "banned",
        "message" => "El usuario está baneado.",
        "user" => $user,
        "rol" => "baneado"
    ]);
    exit;
}

unset($user["contrasena"]);

echo json_encode([
    "status" => "success",
    "user" => $user,
    "rol" => $user["rol"]
]);

?>

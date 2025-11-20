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

$usuario = $data["usuario"] ?? "";
$correo  = $data["correo"] ?? "";
$pass1   = $data["pass1"] ?? "";
$pass2   = $data["pass2"] ?? "";


if ($usuario === "" || $correo === "" || $pass1 === "" || $pass2 === "") {
    echo json_encode(["status" => "error", "message" => "Faltan datos"]);
    exit();
}

if ($pass1 !== $pass2) {
    echo json_encode(["status" => "error", "message" => "Las contraseñas no coinciden"]);
    exit();
}


$stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE usuario = ? AND correo = ?");
$stmt->bind_param("ss", $usuario, $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Usuario y correo no coinciden"]);
    exit();
}


$nueva = password_hash($pass1, PASSWORD_DEFAULT);
$stmt2 = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE usuario = ? AND correo = ?");
$stmt2->bind_param("sss", $nueva, $usuario, $correo);

if ($stmt2->execute()) {
    echo json_encode(["status" => "success", "message" => "Contraseña actualizada"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error al actualizar contraseña"]);
}

?>

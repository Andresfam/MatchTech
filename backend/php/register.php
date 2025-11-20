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

$usuario    = $data["usuario"] ?? "";
$nombre     = $data["nombre"] ?? "";
$apellido   = $data["apellido"] ?? "";
$correo     = $data["correo"] ?? "";
$contrasena = $data["contrasena"] ?? "";


if ($usuario === "" || $nombre === "" || $apellido === "" || $correo === "" || $contrasena === "") {
    echo json_encode(["status" => "error", "message" => "Faltan datos"]);
    exit;
}


$stmtUser = $conn->prepare("SELECT id_usuario FROM usuarios WHERE usuario = ?");
$stmtUser->bind_param("s", $usuario);
$stmtUser->execute();
$stmtUser->store_result();
$usuarioExiste = $stmtUser->num_rows > 0;


$stmtCorreo = $conn->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
$stmtCorreo->bind_param("s", $correo);
$stmtCorreo->execute();
$stmtCorreo->store_result();
$correoExiste = $stmtCorreo->num_rows > 0;


if ($usuarioExiste && $correoExiste) {
    echo json_encode([
        "status" => "error",
        "message" => "El usuario y el correo ya están registrados."
    ]);
    exit;
}

if ($usuarioExiste) {
    echo json_encode([
        "status" => "error",
        "message" => "El usuario ya está registrado."
    ]);
    exit;
}

if ($correoExiste) {
    echo json_encode([
        "status" => "error",
        "message" => "El correo ya está registrado."
    ]);
    exit;
}


$stmtToken = $conn->prepare("SELECT token FROM admin_tokens LIMIT 1");
$stmtToken->execute();
$resultToken = $stmtToken->get_result()->fetch_assoc();


$isAdmin = password_verify($contrasena, $resultToken["token"]);


$rol = $isAdmin ? "admin" : "usuario";



$hashed = password_hash($contrasena, PASSWORD_DEFAULT);


$stmt = $conn->prepare("
    INSERT INTO usuarios (usuario, nombre, apellido, correo, contrasena, rol)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("ssssss", $usuario, $nombre, $apellido, $correo, $hashed, $rol);

if ($stmt->execute()) {

    $id_usuario = $stmt->insert_id;

            echo json_encode([
            "status" => "success",
            "usuario" => [
                "id_usuario" => $id_usuario,
                "usuario" => $usuario,
                "nombre" => $nombre,
                "apellido" => $apellido,
                "correo" => $correo,
                "rol" => $rol  
            ]
        ]);


} else {
    echo json_encode(["status" => "error", "message" => "Error al registrar"]);
}
?>

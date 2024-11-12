<?php
// userData.php

include '../includes/dbConnection.php'; // Asegúrate de que esta ruta es correcta
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../templates/login.html");
    exit;
}

$user_id = $_SESSION['user_id'];

// Inicializar las variables con valores predeterminados
$nombre = "Usuario";
$descripcion = "Sin descripción.";

// Consultar el nombre y la descripción del usuario
$sql = "SELECT nombre, descripcion FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($nombre, $descripcion);
    $stmt->fetch();
    $stmt->close();
} else {
    // Manejar el error de la consulta si no se puede preparar
    die("Error en la consulta: " . $conn->error);
}

// Directorio donde se guardan las imágenes del usuario
$baseDir = "../imagesUsers/userID/$user_id";

// Ruta de la imagen de perfil
$foto_perfil = "$baseDir/0.jpg";
if (!file_exists($foto_perfil)) {
    $foto_perfil = "../assets/images/default_profile.png"; // Imagen por defecto
}

// Chequear si las imágenes adicionales (1 a 4) existen y guardarlas en un array
$imagenes_adicionales = [];
for ($i = 1; $i <= 4; $i++) {
    $imagePath = "$baseDir/$i.jpg";
    if (file_exists($imagePath)) {
        $imagenes_adicionales[] = $imagePath;
    }
}

// Cerrar la conexión a la base de datos
$conn->close();
?>

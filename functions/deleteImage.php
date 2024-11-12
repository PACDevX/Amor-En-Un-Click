<?php
// deleteImage.php

include '../includes/dbConnection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../templates/login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$imagePath = $_GET['imagePath'] ?? '';

// Verificar que el archivo exista y eliminarlo
if ($imagePath && file_exists($imagePath)) {
    unlink($imagePath); // Eliminar la imagen

    // Eliminar la ruta de la base de datos
    $stmt = $conn->prepare("DELETE FROM imagenes_perfil WHERE id_usuario = ? AND ruta_imagen = ?");
    $stmt->bind_param("is", $user_id, $imagePath);
    $stmt->execute();
    $stmt->close();

    // Redirigir con un mensaje de éxito
    header("Location: profileView.php?message=deleted");
    exit;
} else {
    // Redirigir con un mensaje de error si no se pudo eliminar
    header("Location: profileView.php?message=error");
    exit;
}

// Cerrar la conexión
$conn->close();
?>

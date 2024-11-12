<?php
// profile.php

// Incluir el archivo de conexión a la base de datos
include '../includes/dbConnection.php';

// Iniciar la sesión
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit;
}

// Obtener el ID del usuario desde la sesión
$user_id = $_SESSION['user_id'];

// Consultar el nombre, descripción y foto de perfil del usuario
$sql = "SELECT nombre, descripcion, foto_perfil FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($nombre, $descripcion, $foto_perfil);
$stmt->fetch();
$stmt->close();

// Usar una imagen por defecto si no hay foto de perfil
$foto_perfil = $foto_perfil ? $foto_perfil : "../uploads/default_profile.png";

// Cerrar la conexión a la base de datos
$conn->close();

// Incluir la vista de perfil
include 'profile_view.php';
?>

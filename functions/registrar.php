<?php
// registrar.php

// Incluir el archivo de conexión a la base de datos
include '../includes/dbConnection.php';

// Comprobar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger los datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    // Verificar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        header("Location: ../registro.html?error=password_mismatch");
        exit;
    }

    // Comprobar si el correo ya existe
    $sql_check_email = "SELECT id FROM usuarios WHERE email = ?";
    $stmt_check_email = $conn->prepare($sql_check_email);
    $stmt_check_email->bind_param("s", $email);
    $stmt_check_email->execute();
    $stmt_check_email->store_result();

    if ($stmt_check_email->num_rows > 0) {
        // Si el correo ya existe, redirigir con un mensaje de error
        header("Location: ../registro.html?error=email_exists");
        exit;
    }

    // Hashear la contraseña para almacenarla de manera segura
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Preparar la consulta SQL para insertar el nuevo usuario
    $sql = "INSERT INTO usuarios (nombre, apellido, email, contraseña) VALUES (?, ?, ?, ?)";

    // Inicializar la sentencia
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // Vincular parámetros
        $stmt->bind_param("ssss", $nombre, $apellido, $email, $hashed_password);
        
        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Redirigir con un mensaje de éxito usando un parámetro en la URL
            header("Location: ../index.html?message=registered");
            exit;
        } else {
            // Error en la ejecución de la consulta
            header("Location: ../registro.html?error=insert_failed");
            exit;
        }
        
        // Cerrar la declaración
        $stmt->close();
    } else {
        // Error en la preparación de la consulta
        header("Location: ../registro.html?error=insert_failed");
        exit;
    }

    // Cerrar la conexión a la base de datos
    $conn->close();
}
?>

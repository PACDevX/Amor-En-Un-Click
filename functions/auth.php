<?php
// auth.php

// Iniciar la sesión
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../templates/login.html"); // Ajusta la ruta a `login.html` si es necesario
    exit;
}
?>

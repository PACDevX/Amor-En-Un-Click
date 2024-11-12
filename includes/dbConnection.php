<?php
// db_connection.php

// Configuración de la base de datos
$host = 'localhost'; // Cambia esto si tu servidor de base de datos está en otra dirección
$user = 'root'; // Nombre de usuario
$password = ''; // Contraseña (en este caso está vacía)
$db_name = 'amor_en_un_click'; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($host, $user, $password, $db_name);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>

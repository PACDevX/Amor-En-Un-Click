<?php
include '../includes/dbConnection.php';
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Prohibido si el usuario no está autenticado
    exit;
}

$current_user_id = $_SESSION['user_id'];
$target_user_id = $_POST['user_to_like'];
$action = $_POST['action']; // 'like' o 'dislike'

// Validar la acción
if ($action !== 'like' && $action !== 'dislike') {
    http_response_code(400); // Solicitud incorrecta
    exit;
}

// Verificar si ya existe una relación entre los usuarios
$query = "SELECT * FROM likes WHERE id_usuario_quien_likes = ? AND id_usuario_recibe_likes = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $current_user_id, $target_user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Ya existe una relación; actualizar el tipo de acción
    $update_query = "UPDATE likes SET tipo = ? WHERE id_usuario_quien_likes = ? AND id_usuario_recibe_likes = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sii", $action, $current_user_id, $target_user_id);
    $update_stmt->execute();
} else {
    // Insertar un nuevo like/dislike
    $insert_query = "INSERT INTO likes (id_usuario_quien_likes, id_usuario_recibe_likes, tipo) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("iis", $current_user_id, $target_user_id, $action);
    $insert_stmt->execute();
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>

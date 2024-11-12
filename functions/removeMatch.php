<?php
session_start();
include '../includes/dbConnection.php';

$current_user_id = $_SESSION['user_id'];
$user_id_to_remove = json_decode(file_get_contents('php://input'), true)['user_id'];

if (!$user_id_to_remove) {
    echo json_encode(["status" => "error", "message" => "ID de usuario no válido"]);
    exit;
}

// Eliminar el like del usuario actual al otro usuario
$sql = "DELETE FROM likes WHERE id_usuario_quien_likes = ? AND id_usuario_recibe_likes = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $current_user_id, $user_id_to_remove);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "No se pudo deshacer el match"]);
}

$stmt->close();
$conn->close();
?>

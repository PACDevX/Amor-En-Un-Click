<?php
// sendMessage.php
session_start();
include '../includes/dbConnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = $_SESSION['user_id'];
    $data = json_decode(file_get_contents("php://input"), true); // Decodificar JSON del cuerpo de la solicitud
    $receiver_id = $data['receiver_id'];
    $message = trim($data['message']);

    if (!empty($message)) {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
            if ($stmt->execute()) {
                echo json_encode(["status" => "success"]);
            } else {
                echo json_encode(["status" => "error", "message" => "No se pudo enviar el mensaje"]);
            }
            $stmt->close();
        } else {
            echo json_encode(["status" => "error", "message" => "Error al preparar la consulta"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "El mensaje no puede estar vacío"]);
    }

    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Solicitud no válida"]);
}
?>

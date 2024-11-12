<?php
// loadMessages.php
session_start();
include '../includes/dbConnection.php';

$current_user_id = $_SESSION['user_id'];
$receiver_id = $_GET['receiver_id'];

$sql = "
    SELECT sender_id, receiver_id, message, sent_at
    FROM messages
    WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
    ORDER BY sent_at ASC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $current_user_id, $receiver_id, $receiver_id, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($messages);

?>

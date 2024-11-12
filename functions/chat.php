<?php
// chat.php
session_start();
include '../includes/dbConnection.php';

$current_user_id = $_SESSION['user_id'];
$chat_user_id = $_GET['user_id'];

// Obtener el nombre y la foto de perfil del usuario con el que se está chateando
$sql = "SELECT nombre, apellido FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $chat_user_id);
$stmt->execute();
$stmt->bind_result($chat_user_nombre, $chat_user_apellido);
$stmt->fetch();
$stmt->close();

// Ruta de la foto de perfil del usuario con el que se está chateando
$fotoPerfilPath = "../imagesUsers/userID/$chat_user_id/0.jpg";
if (!file_exists($fotoPerfilPath)) {
    $fotoPerfilPath = "../assets/images/default_profile.jpg"; // Imagen por defecto
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat con <?php echo htmlspecialchars($chat_user_nombre . ' ' . $chat_user_apellido); ?></title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/chat.css">
</head>

<body>
    <?php include '../includes/headerForFunctions.php'; ?>
    <main>
        <br>
        <div class="chat-container">
            <!-- Sección del encabezado del chat con el evento onclick -->
            <div class="chat-header" onclick="viewUserProfile()">
                <img src="<?php echo htmlspecialchars($fotoPerfilPath); ?>" alt="Foto de perfil" class="chat-profile-pic">
                <span class="chat-user-name"><?php echo htmlspecialchars($chat_user_nombre . ' ' . $chat_user_apellido); ?></span>
            </div>

            <!-- Sección de mensajes -->
            <div class="chat-messages" id="chatMessages">
                <!-- Los mensajes se cargarán aquí mediante JavaScript -->
            </div>

            <!-- Sección de entrada de texto -->
            <div class="chat-input">
                <input type="text" id="messageInput" placeholder="Escribe tu mensaje...">
                <button onclick="sendMessage()">Enviar</button>
            </div>
        </div>
    </main>

    <script>
// Función para redirigir al perfil del usuario
function viewUserProfile() {
    const userId = <?php echo json_encode($chat_user_id); ?>;
    window.location.href = `../functions/viewUserProfile.php?user_id=${userId}`;
}


        // Funciones JavaScript para cargar y enviar mensajes (ya proporcionadas)
        document.addEventListener("DOMContentLoaded", function () {
            loadMessages(); // Cargar mensajes antiguos al cargar la página
        });

        function loadMessages() {
            const chatMessages = document.getElementById('chatMessages');
            const receiverId = <?php echo json_encode($chat_user_id); ?>;

            fetch(`loadMessages.php?receiver_id=${receiverId}`)
                .then(response => response.json())
                .then(data => {
                    chatMessages.innerHTML = ''; // Limpiar mensajes antiguos
                    data.forEach(message => {
                        const messageElement = document.createElement('div');
                        messageElement.className = `message ${message.sender_id == <?php echo json_encode($current_user_id); ?> ? 'sent' : 'received'}`;

                        // Crear un elemento de texto para el mensaje
                        const messageText = document.createElement('div');
                        messageText.textContent = message.message;

                        // Crear un elemento para la hora
                        const messageTime = document.createElement('div');
                        messageTime.className = 'message-time';
                        const date = new Date(message.sent_at);
                        messageTime.textContent = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }); // Formato de hora

                        // Añadir ambos elementos al mensaje
                        messageElement.appendChild(messageText);
                        messageElement.appendChild(messageTime);

                        chatMessages.appendChild(messageElement);
                    });

                    // Desplazar hacia abajo para ver los mensajes más recientes
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                })
                .catch(error => console.error('Error al cargar los mensajes:', error));
        }


        function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            const receiverId = <?php echo json_encode($chat_user_id); ?>;

            if (message !== '') {
                fetch('sendMessage.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ receiver_id: receiverId, message: message })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            messageInput.value = ''; // Limpiar el campo de entrada
                            loadMessages(); // Recargar los mensajes
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => console.error('Error al enviar el mensaje:', error));
            }
        }

        // Actualizar mensajes en tiempo real cada 5 segundos
        setInterval(loadMessages, 5000);
    </script>
</body>

</html>
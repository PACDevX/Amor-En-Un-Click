<?php
// viewUserProfile.php
session_start();
include '../includes/dbConnection.php';

$current_user_id = $_SESSION['user_id']; // Obtener el ID del usuario actual de la sesión
$view_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : $current_user_id; // Usar el `user_id` de la URL o el usuario actual

// Inicializar variables por si la consulta no devuelve resultados
$nombre = "Usuario desconocido";
$descripcion = "Sin descripción";

// Consulta para obtener la información del usuario
$sql = "SELECT nombre, descripcion FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $view_user_id);
$stmt->execute();
$stmt->bind_result($nombre, $descripcion);
$stmt->fetch();
$stmt->close();

// Directorio base donde se guardan las imágenes de los usuarios
$baseDir = "../imagesUsers/userID/" . $view_user_id;
$fotoPerfilPath = "$baseDir/0.jpg";

// Verificar si la foto de perfil existe, de lo contrario usar la imagen por defecto
if (!file_exists($fotoPerfilPath)) {
    $fotoPerfilPath = "../assets/images/default_profile.jpg";
}

// Verificar las imágenes adicionales
$imagenes_adicionales = array();
for ($i = 1; $i <= 4; $i++) {
    $additionalImagePath = "$baseDir/$i.jpg";
    if (file_exists($additionalImagePath)) {
        $imagenes_adicionales[] = $additionalImagePath;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($nombre); ?></title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/profileView.css">
    <style>
        /* Estilo para el popup */
        .popup-success {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 14px;
            opacity: 1;
            transition: opacity 0.5s ease;
            z-index: 1000;
            display: none; /* Oculto por defecto */
        }
    </style>
</head>

<body>
<?php include '../includes/headerForFunctions.php'; ?>
    <div class="card-container">
        <div class="profile-card">
            <div class="profile-picture">
                <img src="<?php echo htmlspecialchars($fotoPerfilPath) . '?t=' . time(); ?>" alt="Foto de perfil">
            </div>

            <h2><?php echo htmlspecialchars($nombre); ?></h2>
            <p><?php echo htmlspecialchars($descripcion); ?></p>

            <div class="image-container">
                <img id="currentImage"
                    src="<?php echo htmlspecialchars($imagenes_adicionales[0] ?? '../assets/images/default_additional.jpg'); ?>"
                    alt="Sin foto adicional">
                <div class="navigation-buttons">
                    <button onclick="showPreviousImage()">&#9664; Anterior</button>
                    <button onclick="showNextImage()">Siguiente &#9654;</button>
                </div>
            </div>

            <!-- Botón para deshacer el match -->
            <?php if ($view_user_id !== $current_user_id): ?>
                <button class="delete-button" onclick="deshacerMatch(<?php echo $view_user_id; ?>)">Deshacer Match</button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Contenedor del popup -->
    <div class="popup-success" id="successPopup">Match deshecho con éxito.</div>

    <script>
        const images = <?php echo json_encode($imagenes_adicionales); ?>;
        let currentIndex = 0;

        function showPreviousImage() {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            document.getElementById('currentImage').src = images[currentIndex];
        }

        function showNextImage() {
            currentIndex = (currentIndex + 1) % images.length;
            document.getElementById('currentImage').src = images[currentIndex];
        }

        function deshacerMatch() {
            const userId = <?php echo json_encode($view_user_id); ?>; // Obtener el user_id de la URL

            fetch('removeMatch.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ user_id: userId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const popup = document.getElementById('successPopup');
                    popup.style.display = 'block'; // Mostrar el popup
                    setTimeout(() => {
                        popup.style.opacity = '0'; // Desaparecer el popup
                        setTimeout(() => popup.remove(), 200); // Eliminar el popup del DOM
                        window.location.href = 'matches.php'; // Redirigir a matches.php
                    }, 700);
                } else {
                    alert(data.message || 'Ocurrió un error al deshacer el match.');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>

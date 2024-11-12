<?php
session_start();
include './includes/dbConnection.php';

$current_user_id = $_SESSION['user_id'];

// Obtener todos los usuarios que no tengan relación con el usuario actual
$query = "
    SELECT id, nombre, descripcion 
    FROM usuarios 
    WHERE id NOT IN (
        SELECT id_usuario_recibe_likes 
        FROM likes 
        WHERE id_usuario_quien_likes = ?
    ) AND id != ? 
    ORDER BY RAND()
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $current_user_id, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

// Guardar los usuarios con al menos la imagen 0 en un array
$user_list = [];
while ($row = $result->fetch_assoc()) {
    $user_id = $row['id'];
    $image_base_path = "./imagesUsers/userID/$user_id/0.jpg";

    // Verificar si la imagen de perfil 0 existe
    if (file_exists($image_base_path)) {
        $user_list[] = $row;
    }
}

if (!empty($user_list)) {
    // Seleccionar el primer usuario de la lista
    $current_user = $user_list[0];
    $user_id = $current_user['id'];
    $image_base_path = "./imagesUsers/userID/$user_id";

    // Crear un array de las imágenes adicionales que existan
    $images = [];
    for ($i = 1; $i <= 4; $i++) { // Cambié el bucle para comenzar en 1
        $image_path = "$image_base_path/$i.jpg";
        if (file_exists($image_path)) {
            $images[] = $image_path;
        }
    }

    $profile_image_path = "$image_base_path/0.jpg"; // Imagen de perfil
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amor en un Click - Página Principal</title>
    <link rel="stylesheet" href="./assets/css/styles.css">
</head>

<body>
    <?php include './includes/header.php'; ?>

    <main>
        <section class="card-container">


            <div class="profile-card">
                <!-- Mostrar la imagen de perfil encima del resto -->
                <?php if (!empty($user_list)): ?>
                    <div class="profile-picture">
                        <img src="<?php echo htmlspecialchars($profile_image_path); ?>" alt="Foto de perfil principal">
                    </div>
                <?php endif; ?>

                <?php if (empty($user_list)): ?>
                    <p>Vaya, parece que por hoy no te queda a nadie por conocer...</p>
                <?php else: ?>
                    <div class="image-container">
                        <img id="profileImage" src="<?php echo htmlspecialchars($images[0]); ?>" alt="Foto adicional">
                        <div id="emojiOverlay" class="emoji-overlay"></div> <!-- Emoji overlay -->
                    </div>

                    <h2><?php echo htmlspecialchars($current_user['nombre']); ?></h2>
                    <p><?php echo htmlspecialchars($current_user['descripcion']); ?></p>

                    <!-- Botones de navegación para las imágenes -->
                    <div class="navigation-buttons">
                        <button onclick="showPreviousImage()">&#9664; </button>
                        <button onclick="showNextImage()"> &#9654;</button>
                    </div>

                    <br>
                    <!-- Botones de Like y Dislike -->
                    <button class="like" onclick="handleLike(<?php echo $user_id; ?>)">Like</button>
                    <button class="dislike" onclick="handleDislike(<?php echo $user_id; ?>)">Dislike</button>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script>
        // Pasar el array de imágenes desde PHP a JavaScript
        const images = <?php echo json_encode($images); ?>;
    </script>
    <script src="./assets/js/main.js"></script>

</body>

</html>
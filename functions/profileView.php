<?php
// profileView.php
include 'userData.php'; // Asegúrate de que este archivo contiene la lógica necesaria para obtener $nombre, $descripcion, y $foto_perfil

// Directorio base donde se guardan las imágenes de los usuarios
$baseDir = "../imagesUsers/userID/" . $_SESSION['user_id'];
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/profileView.css"> <!-- Nueva línea para incluir CSS -->
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
                    <button class="delete-button" onclick="deleteCurrentImage()">Eliminar &#10060;</button>
                </div>
            </div>

            <br><br>
            <form id="updateProfileForm" action="updateProfile.php" method="post" enctype="multipart/form-data">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>

                <br><br>
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion"><?php echo htmlspecialchars($descripcion); ?></textarea>

                <br><br>
                <label for="foto_perfil">Foto de perfil:</label>
                <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*">

                <br><br>
                <label>Fotos adicionales:</label>
                <input type="file" id="fotos_adicionales" name="fotos_adicionales[]" accept="image/*" multiple
                    onchange="checkFileLimit()">

                <button type="submit">Guardar cambios</button>
            </form>

            <!-- Mensajes flotantes -->
            <div class="floating-message message-success" id="successMessage">Datos almacenados correctamente</div>
            <div class="floating-message message-success" id="deletedMessage">Foto eliminada exitosamente</div>
            <div class="floating-message message-error" id="errorMessage">Error al guardar los datos</div>
        </div>
    </div>

    <script>
        const images = <?php echo json_encode($imagenes_adicionales); ?>; // Esto inyecta el JSON en la variable
        const imageCount = images.length;
    </script>
    <script src="../assets/js/profileView.js"></script> <!-- Nueva línea para incluir JS -->
</body>
</html>

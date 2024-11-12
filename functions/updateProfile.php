<?php
// updateProfile.php

include '../includes/dbConnection.php'; // Asegúrate de que la ruta sea correcta
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../templates/login.html");
    exit;
}

$user_id = $_SESSION['user_id'];

// Obtener los datos del formulario
$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];

// Función para redimensionar imágenes
function resizeImage($file, $targetWidth, $targetHeight) {
    $imageInfo = getimagesize($file);
    $mime = $imageInfo['mime'];

    // Crear imagen a partir del tipo MIME
    switch ($mime) {
        case 'image/jpeg':
            $src = imagecreatefromjpeg($file);
            break;
        case 'image/png':
            $src = imagecreatefrompng($file);
            break;
        case 'image/gif':
            $src = imagecreatefromgif($file);
            break;
        default:
            return false; // Tipo de imagen no compatible
    }

    if (!$src) {
        return false; // Si no se pudo crear la imagen, retornar false
    }

    $width = $imageInfo[0];
    $height = $imageInfo[1];
    $dst = imagecreatetruecolor($targetWidth, $targetHeight);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

    // Guardar la imagen redimensionada en el mismo archivo
    imagejpeg($dst, $file); // Cambia a imagepng o imagegif si es necesario
    imagedestroy($src);
    imagedestroy($dst);

    return true;
}

// Directorio base donde se guardarán las imágenes del usuario
$baseDir = "../imagesUsers/userID/$user_id";

// Crear la carpeta del usuario si no existe
if (!is_dir($baseDir)) {
    mkdir($baseDir, 0777, true);
}

// Procesar la imagen de perfil
if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
    $fotoPerfilPath = "$baseDir/0.jpg";

    // Eliminar la imagen anterior si existe
    if (file_exists($fotoPerfilPath)) {
        unlink($fotoPerfilPath); // Elimina la imagen anterior
    }

    // Mover la nueva imagen a la carpeta
    if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $fotoPerfilPath)) {
        // Redimensionar la imagen de perfil
        if (!resizeImage($fotoPerfilPath, 300, 375)) {
            echo "Error: Formato de imagen no compatible.";
            exit;
        }
    } else {
        echo "Error al mover la imagen de perfil.";
    }
}

// Procesar imágenes adicionales (hasta 4)
if (isset($_FILES['fotos_adicionales'])) {
    $uploadedPhotos = count($_FILES['fotos_adicionales']['name']);
    for ($i = 0; $i < min(4, $uploadedPhotos); $i++) {
        if ($_FILES['fotos_adicionales']['error'][$i] == 0) {
            $fotoAdicionalPath = "$baseDir/" . ($i + 1) . ".jpg";
            move_uploaded_file($_FILES['fotos_adicionales']['tmp_name'][$i], $fotoAdicionalPath);
            // Redimensionar la imagen adicional
            if (!resizeImage($fotoAdicionalPath, 400, 500)) {
                echo "Error: Formato de imagen no compatible.";
                exit;
            }
        }
    }
}

// Actualizar el nombre y la descripción en la base de datos
$stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, descripcion = ? WHERE id = ?");
$stmt->bind_param("ssi", $nombre, $descripcion, $user_id);

if ($stmt->execute()) {
    // Redirigir con un mensaje de éxito
    header("Location: profileView.php?message=success");
    exit;
} else {
    // Redirigir con un mensaje de error si algo falla
    header("Location: profileView.php?message=error");
    exit;
}

// Cerrar la declaración y la conexión
$stmt->close();
$conn->close();
?>

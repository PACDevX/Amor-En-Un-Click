<?php
// matches.php
session_start();
include '../includes/dbConnection.php';

$current_user_id = $_SESSION['user_id'];

// Consulta para obtener los matches mutuos
$sql = "
    SELECT u.id, u.nombre, u.apellido, u.email
    FROM usuarios u
    INNER JOIN likes l1 ON u.id = l1.id_usuario_recibe_likes
    INNER JOIN likes l2 ON u.id = l2.id_usuario_quien_likes
    WHERE l1.id_usuario_quien_likes = ? AND l2.id_usuario_recibe_likes = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $current_user_id, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

$matches = [];
while ($row = $result->fetch_assoc()) {
    // Verificar la existencia de la foto de perfil
    $user_id = $row['id'];
    $fotoPerfilPath = "../imagesUsers/userID/$user_id/0.jpg";
    if (!file_exists($fotoPerfilPath)) {
        $fotoPerfilPath = "../assets/images/default_profile.jpg"; // Imagen por defecto
    }
    $row['foto_perfil'] = $fotoPerfilPath;
    $matches[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descubre tus Matches</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        /* Estilos para el contenedor de matches */
        .matches-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            padding: 20px;
            max-width: 1000px;
            margin: 0 auto; /* Centrar horizontalmente */
        }

        .match-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 15px;
            cursor: pointer;
            transition: transform 0.2s ease;
            text-align: center;
        }

        .match-card:hover {
            transform: translateY(-5px);
        }

        .match-card img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .match-card p {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>
    <?php include '../includes/headerForFunctions.php'; ?>
    <main>
        <h2>Descubre tus Matches</h2>
        <div class="matches-container">
            <?php if (empty($matches)): ?>
                <p>No tienes ningún match por el momento.</p>
            <?php else: ?>
                <?php foreach ($matches as $match): ?>
                    <div class="match-card" onclick="window.location.href='chat.php?user_id=<?php echo $match['id']; ?>'">
                        <img src="<?php echo htmlspecialchars($match['foto_perfil']); ?>" alt="Foto de perfil">
                        <p><?php echo htmlspecialchars($match['nombre'] . ' ' . $match['apellido']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

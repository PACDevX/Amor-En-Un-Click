<?php
// recuperarContraseña.php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    echo "<script>
            alert('La función de recuperación estará disponible una vez se publique la web.');
            window.location.href = '../index.html';
          </script>";
}
?>

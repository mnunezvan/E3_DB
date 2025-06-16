<?php
session_start();
require_once 'utils.php';

$usuario    = $_POST['usuario']    ?? '';
$contrasena = $_POST['contrasena'] ?? '';

try {
    $db = conectarBD();
    $sql = "
      SELECT P.username, P.contrasena
        FROM persona P
        JOIN usuario U ON P.correo = U.correo
       WHERE P.username = :usuario
    ";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();

    $fila = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Para depuración local, mostramos el mensaje de error real:
    echo '<h2>Error en la consulta SQL:</h2>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    exit();
}


if (! $fila) {
    header('Location: index.php?error=Usuario no existe');
    exit();
}
if ($fila['contrasena'] !== $contrasena) {
    header('Location: index.php?error=Clave errónea');
    exit();
}

$_SESSION['usuario'] = $fila['username'];
header('Location: main.php');
exit();
?>

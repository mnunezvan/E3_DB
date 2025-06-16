<?php
session_start();
require_once 'utils.php';

$usuario = $_POST['nombre_usuario'] ?? '';
$clave = $_POST['clave'] ?? '';
$repetir_clave = $_POST['repetir_clave'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$run = $_POST['run'] ?? '';
$dv = $_POST['dv'] ?? '';
$nombre = $_POST['nombre_real'] ?? '';
$email = $_POST['email'] ?? '';

// Guardar datos para reusarlos
$_SESSION['form_data'] = $_POST;

if ($clave !== $repetir_clave) {
    $_SESSION['error'] = 'Las contraseñas no coinciden';
    header('Location: registro.php');
    exit();
}

try {
    $db = conectarBD();

    $stmt = $db->prepare("SELECT correo FROM persona WHERE correo = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->fetch()) {
        $_SESSION['error'] = 'El correo ya existe';
        header('Location: registro.php');
        exit();
    }

    $db->beginTransaction();

    $stmt = $db->prepare("
        INSERT INTO persona
           (correo, nombre, contrasena, username,
            telefono_contacto, run, dv)
        VALUES
           (:email, :nombre, :contrasena, :username,
            :telefono, :run, :dv)
    ");
    $stmt->bindParam(':username', $usuario);
    $stmt->bindParam(':contrasena', $clave);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':run', $run);
    $stmt->bindParam(':dv', $dv);
    $stmt->execute();

    $stmt = $db->prepare("
        INSERT INTO usuario
           (correo, puntos)
        VALUES
           (:email, 0)
    ");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    $db->commit();

    unset($_SESSION['form_data']);
    $_SESSION['success'] = 'Usuario registrado correctamente';
    header('Location: registro.php');
    exit();

} catch (Exception $e) {
    // Rollback si estamos en transacción
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    // Guardar mensaje exacto de error para depuración
    $_SESSION['error'] = 'Error: ' . $e->getMessage();
    header('Location: registro.php');
    exit();
}
?>

<?php
session_start();
require_once 'utils.php';

if (! isset($_SESSION['correo'])) {
    header('Location: index.php?error=Debes iniciar sesión');
    exit();
}

$nombre       = $_POST['nombre']        ?? '';
$descripcion  = $_POST['descripcion']   ?? '';
$fecha_inicio = $_POST['fecha_inicio']  ?? '';
$fecha_fin    = $_POST['fecha_fin']     ?? '';
$ciudad       = $_POST['ciudad']        ?? '';
$organizador  = $_POST['organizador']   ?? '';

try {
    $db = conectarBD();
    $db->beginTransaction();
    $stmt = $db->prepare("
        SELECT correo 
          FROM persona 
         WHERE username = :organizador
    ");
    $stmt->bindParam(':organizador', $organizador);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (! $row) {
        throw new Exception("Organizador no válido");
    }
    $correo_org = $row['correo'];

    $insert = "
        INSERT INTO agenda
            (correo_usuario, nombre, descripcion,
             fecha_inicio, fecha_fin, ciudad_destino)
        VALUES
            (:correo, :nombre, :descripcion,
             :fecha_inicio, :fecha_fin, :ciudad)
        RETURNING id
    ";
    $stmt = $db->prepare($insert);
    $stmt->bindParam(':correo',        $correo_org);
    $stmt->bindParam(':nombre',        $nombre);
    $stmt->bindParam(':descripcion',   $descripcion);
    $stmt->bindParam(':fecha_inicio',  $fecha_inicio);
    $stmt->bindParam(':fecha_fin',     $fecha_fin);
    $stmt->bindParam(':ciudad',        $ciudad);
    $stmt->execute();

    // (Opcional) lees el nuevo id si lo necesitas:
    $agenda_id = $stmt->fetchColumn();

    // 6) Commit: al insertar en agenda, el TRIGGER que configuraremos
    //    invocará automáticamente al SP1 para sumar puntos.
    $db->commit();

    // 7) Redirigir con mensaje de éxito
    header('Location: crear_viaje.php?mensaje=Viaje creado correctamente');
    exit();

} catch (Exception $e) {
    // Si algo falla, deshacemos la transacción
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    header('Location: crear_viaje.php?mensaje=Error al crear el viaje');
    exit();
}
?>

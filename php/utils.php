<?php
function conectarBD() {
    $host = 'localhost'; // Cambiar al servidor bdd1.ing.puc.cl si se quiere usar el servidor remoto
    $dbname = 'mnunezvan'; // Nombre de usuario
    $usuario = 'mnunezvan'; // Nombre de usuario
    $clave = '23645997'; // Número de alumno

    try {
        $db = new PDO("pgsql:host=$host;dbname=$dbname", $usuario, $clave);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
        exit();
    }
}
?>

<?php
session_start();
$error   = $_GET['error']   ?? null;
$mensaje = $_GET['mensaje'] ?? null;
require_once 'utils.php';
$db = conectarBD();
$res = $db->query("
  SELECT 
    r.id,
    r.monto,
    CASE
      WHEN av.id IS NOT NULL  THEN 'Avión'
      WHEN bu.id IS NOT NULL  THEN 'Bus'
      WHEN tr.id IS NOT NULL  THEN 'Tren'
      WHEN pa.id IS NOT NULL  THEN 'Panorama'
      WHEN ho.id IS NOT NULL  THEN 'Hospedaje'
      ELSE 'Desconocido'
    END AS tipo
  FROM reserva r
    LEFT JOIN avion     av ON av.id = r.id
    LEFT JOIN bus       bu ON bu.id = r.id
    LEFT JOIN tren      tr ON tr.id = r.id
    LEFT JOIN panorama  pa ON pa.id = r.id
    LEFT JOIN hospedaje ho ON ho.id = r.id
  WHERE r.agenda_id IS NULL
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Viaje</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Crear nuevo viaje</h1>
        <form action="procesar_crear_viaje.php" method="POST" class="formulario">
            <label for="nombre">Nombre del viaje:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="4" required></textarea>

            <label for="fecha_inicio">Fecha de inicio:</label>
            <input type="date" id="fecha_inicio" name="fecha_inicio" required>

            <label for="fecha_fin">Fecha de término:</label>
            <input type="date" id="fecha_fin" name="fecha_fin" required>

            <label for="ciudad">Ciudad destino:</label>
            <input type="text" id="ciudad" name="ciudad" required>

            <label for="organizador">Organizador (usuario):</label>
            <input type="text" id="organizador" name="organizador" required>

            <label for="reservas">Reservas disponibles:</label>
            <select name="reservas[]" multiple size="6">
            <?php foreach($reservas as $res): ?>
                <option value="<?= $res['id'] ?>">
                <?= "{$res['tipo']} — ID {$res['id']} — \$ {$res['monto']}" ?>
                </option>
            <?php endforeach ?>
            </select>



            <label for="participantes">Participantes (usernames separados por coma):</label>
            <textarea id="participantes" name="participantes" rows="2"
                placeholder="usuario1,usuario2,…"></textarea>

            <button type="submit">Crear viaje</button>
        </form>

        <?php if ($mensaje): ?>
            <p class="success"><?= htmlspecialchars($mensaje) ?></p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <p><a href="main.php">Volver al inicio</a></p>
    </div>
</body>
</html>

<?php
require_once __DIR__ . '/conexion.php';

// ==============================================================================
// PASO 1: Leemos el ID de la URL y verificamos que la incidencia existe
// ==============================================================================
$id = (int) ($_GET['id'] ?? 0);

if ($id === 0) {
    // Si no viene ningún ID en la URL, volvemos al listado
    header("Location: index.php");
    exit;
}

// Buscamos la incidencia en la base de datos
$stmt = $pdo->prepare("SELECT * FROM incidencias WHERE id = :id");
$stmt->execute([':id' => $id]);
$incidencia = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no existe ninguna incidencia con ese ID, volvemos al listado
if (!$incidencia) {
    header("Location: index.php");
    exit;
}

// Cargamos los técnicos para el desplegable de asignación
$stmtTecnicos = $pdo->query("SELECT id, nombre FROM tecnicos");
$tecnicos = $stmtTecnicos->fetchAll(PDO::FETCH_ASSOC);

$mensaje = '';
$tipo_mensaje = ''; // 'exito' o 'error'

// ==============================================================================
// PASO 2a: Cambio de estado simple (Pendiente / En Proceso / Resuelta)
// ==============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'cambiar_estado') {
    $nuevo_estado = $_POST['estado'];

    $stmtUpdate = $pdo->prepare("UPDATE incidencias SET estado = :estado WHERE id = :id");
    $stmtUpdate->execute([':estado' => $nuevo_estado, ':id' => $id]);

    // Recargamos los datos actualizados
    $stmt->execute([':id' => $id]);
    $incidencia = $stmt->fetch(PDO::FETCH_ASSOC);

    $mensaje = 'Estado actualizado correctamente.';
    $tipo_mensaje = 'exito';
}

// ==============================================================================
// PASO 2b: Asignación avanzada con TRANSACCIÓN
// Cuando asignamos un técnico, necesitamos hacer DOS cosas a la vez:
//   1. Actualizar la incidencia: tecnico_id + estado = 'En Proceso'
//   2. Incrementar tareas_activas del técnico en +1
// Si una falla, deshacemos todo con rollBack() para no dejar datos corruptos.
// ==============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'asignar_tecnico') {
    $id_tecnico = (int) $_POST['id_tecnico'];

    try {
        // Iniciamos la transacción (a partir de aquí, nada se guarda hasta hacer commit)
        $pdo->beginTransaction();

        // Consulta 1: Actualizamos la incidencia (estado = 'En Proceso' + técnico asignado)
        $stmt1 = $pdo->prepare("UPDATE incidencias SET tecnico_id = :tecnico, estado = 'En Proceso' WHERE id = :id");
        $stmt1->execute([':tecnico' => $id_tecnico, ':id' => $id]);

        // Consulta 2: Sumamos +1 a las tareas activas del técnico elegido
        $stmt2 = $pdo->prepare("UPDATE tecnicos SET tareas_activas = tareas_activas + 1 WHERE id = :tecnico");
        $stmt2->execute([':tecnico' => $id_tecnico]);

        // Si las dos consultas salieron bien, confirmamos los cambios
        $pdo->commit();

        // Recargamos los datos actualizados
        $stmtReload = $pdo->prepare("SELECT * FROM incidencias WHERE id = :id");
        $stmtReload->execute([':id' => $id]);
        $incidencia = $stmtReload->fetch(PDO::FETCH_ASSOC);

        $mensaje = 'Técnico asignado correctamente. La incidencia está ahora En Proceso.';
        $tipo_mensaje = 'exito';

    } catch (Exception $e) {
        // Si algo falló, deshacemos TODO (como si no hubiera pasado nada)
        $pdo->rollBack();

        $mensaje = 'Error al asignar el técnico. No se guardó ningún cambio.';
        $tipo_mensaje = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Incidencia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="caja">
    <div class="cabecera">
        <h1>Editar Incidencia #<?= (int) $incidencia['id'] ?></h1>
        <a href="index.php" class="boton boton-gris">← Volver</a>
    </div>

    <!-- Mostramos el mensaje de éxito o error si hay alguno -->
    <?php if (!empty($mensaje)): ?>
        <div class="<?= $tipo_mensaje === 'exito' ? 'mensaje-exito' : 'mensaje-error' ?>">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <p><strong>Título:</strong> <?= htmlspecialchars($incidencia['titulo']) ?></p>
    <p>
        <strong>Estado actual:</strong>
        <span class="etiqueta <?= htmlspecialchars($incidencia['estado']) ?>">
            <?= htmlspecialchars($incidencia['estado']) ?>
        </span>
    </p>

    <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">

    <!-- ------------------------------------------------------------------
         FORMULARIO 1: Cambiar el estado manualmente
         ------------------------------------------------------------------ -->
    <h2>Cambiar Estado</h2>
    <form action="editar_incidencia.php?id=<?= (int) $incidencia['id'] ?>" method="POST">
        <input type="hidden" name="accion" value="cambiar_estado">

        <div class="campo">
            <label for="estado">Nuevo estado:</label>
            <select id="estado" name="estado">
                <option value="Pendiente"  <?= ($incidencia['estado'] === 'Pendiente')  ? 'selected' : '' ?>>Pendiente</option>
                <option value="En Proceso" <?= ($incidencia['estado'] === 'En Proceso') ? 'selected' : '' ?>>En Proceso</option>
                <option value="Resuelta"   <?= ($incidencia['estado'] === 'Resuelta')   ? 'selected' : '' ?>>Resuelta</option>
            </select>
        </div>

        <button type="submit" class="boton">Guardar Estado</button>
    </form>

    <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">

    <!-- ------------------------------------------------------------------
         FORMULARIO 2: Asignación avanzada con transacción
         Asigna un técnico Y actualiza su contador de tareas en un solo bloque
         ------------------------------------------------------------------ -->
    <h2>Asignar Técnico</h2>
    <p style="font-size: 13px; color: #666;">
        Al asignar un técnico, el estado cambia automáticamente a <strong>En Proceso</strong>
        y se incrementa el contador de tareas del técnico.
    </p>

    <form action="editar_incidencia.php?id=<?= (int) $incidencia['id'] ?>" method="POST">
        <input type="hidden" name="accion" value="asignar_tecnico">

        <div class="campo">
            <label for="id_tecnico">Técnico:</label>
            <select id="id_tecnico" name="id_tecnico">
                <?php foreach ($tecnicos as $tecnico): ?>
                    <option value="<?= (int) $tecnico['id'] ?>" <?= ($incidencia['tecnico_id'] == $tecnico['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tecnico['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="boton boton-verde">Asignar y poner En Proceso</button>
    </form>
</div>

</body>
</html>

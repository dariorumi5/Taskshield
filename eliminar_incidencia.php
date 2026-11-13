<?php
require_once __DIR__ . '/conexion.php';

// LEEMOS EL ID DE LA URL Y VERIFICAMOS QUE LA INCIDENCIA EXISTE
$id = (int) ($_GET['id'] ?? 0);

if ($id === 0) {
    // Si no existe la incidencia, volvemos al listado
    header("Location: index.php");
    exit;
}

// Verificamos que la incidencia existe
$stmt = $pdo->prepare("SELECT id, titulo FROM incidencias WHERE id = :id");
$stmt->execute([':id' => $id]);
$incidencia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$incidencia) {
    // Si no existe, volvemos al listado
    header("Location: index.php");
    exit;
}

// Si el usuario confirmó el borrado (pulsó el botón eliminar)
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ejecutamos el DELETE
    $stmtDelete = $pdo->prepare("DELETE FROM incidencias WHERE id = :id");
    $stmtDelete->execute([':id' => $id]);

    // rowCount() muestra cuántas filas se borraron
    // Si devuelve 1 o más, el borrado fue exitoso
    if ($stmtDelete->rowCount() > 0) {
        $mensaje = 'exito';
    } else {
        $mensaje = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Incidencia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="caja">

    <?php if ($mensaje === 'exito'): ?>
        <div class="mensaje-exito">
            ✓ La incidencia #<?= (int) $id ?> ha sido eliminada correctamente.
        </div>
        <a href="index.php" class="boton" style="margin-top: 15px; display: inline-block;">
            ← Volver al listado
        </a>

    <?php elseif ($mensaje === 'error'): ?>
        <div class="mensaje-error">
            No se pudo eliminar la incidencia. Es posible que ya no exista.
        </div>
        <a href="index.php" class="boton boton-gris" style="margin-top: 15px; display: inline-block;">
            ← Volver al listado
        </a>

    <?php else: ?>
        <!-- Pantalla de confirmación (antes de borrar preguntamos al usuario) -->
        <h1>¿Eliminar incidencia?</h1>
        <p>ID: <?= (int) $incidencia['id'] ?></p>
        <p>Título: <?= htmlspecialchars($incidencia['titulo']) ?></p>

        <p style="color: #721c24; font-weight: bold;">Esta acción no se puede deshacer.</p>

        <form action="eliminar_incidencia.php?id=<?= (int) $id ?>" method="POST">
            <div class="fila-botones">
                <button type="submit" class="boton boton-rojo">Eliminar</button>
                <a href="index.php" class="boton boton-gris">Cancelar</a>
            </div>
        </form>
    <?php endif; ?>

</div>

</body>
</html>

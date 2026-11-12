<?php
require_once __DIR__ . '/conexion.php';

// Consultamos las incidencias que no estén resueltas
$stmt = $pdo->prepare("SELECT id, titulo, prioridad, estado, email_cliente FROM incidencias WHERE estado <> :estado");
$stmt->execute([':estado' => 'Resuelta']);
$incidencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taskshield - Incidencias Activas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="caja">
    <!-- Cabecera: Título y botón en la misma fila -->
    <div class="cabecera">
        <h1>Incidencias Activas</h1>
        <a href="nueva_incidencia.php" class="boton">+ Crear Incidencia</a>
    </div>

    <?php if (empty($incidencias)): ?>
        <p>No hay incidencias activas en este momento.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Email Cliente</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($incidencias as $incidencia): ?>
                    <tr>
                        <td><?= (int) $incidencia['id'] ?></td>
                        <td><?= htmlspecialchars($incidencia['titulo']) ?></td>
                        <td>
                            <!-- La clase 'etiqueta' da la forma y el valor de la base de datos (Alta, Media, Baja) le da el color -->
                            <span class="etiqueta <?= htmlspecialchars($incidencia['prioridad']) ?>">
                                <?= htmlspecialchars($incidencia['prioridad']) ?>
                            </span>
                        </td>
                        <td>
                            <!-- La clase 'etiqueta' da la forma y el estado (Pendiente, Proceso, Resuelta) le da el color -->
                            <span class="etiqueta <?= htmlspecialchars($incidencia['estado']) ?>">
                                <?= htmlspecialchars($incidencia['estado']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($incidencia['email_cliente']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>

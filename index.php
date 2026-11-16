<?php
require_once __DIR__ . '/conexion.php';

// MOSTRAMOS TODAS LAS INCIDENCIAS CON EL NOMBRE DEL TÉCNICO ASIGNADO
$sql = "SELECT i.id, i.titulo, i.prioridad, i.estado, i.email_cliente, t.nombre AS tecnico_nombre 
        FROM incidencias i 
        LEFT JOIN tecnicos t ON i.tecnico_id = t.id";
$stmt = $pdo->prepare($sql);
$stmt->execute();
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

<div class="lista">
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
                    <th>Técnico Asignado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($incidencias as $incidencia): ?>
                    <tr>
                        <td><?= (int) $incidencia['id'] ?></td>
                        <td><?= htmlspecialchars($incidencia['titulo']) ?></td>
                        <td>
                            <span class="etiqueta <?= htmlspecialchars($incidencia['prioridad']) ?>">
                                <?= htmlspecialchars($incidencia['prioridad']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="etiqueta <?= htmlspecialchars($incidencia['estado']) ?>">
                                <?= htmlspecialchars($incidencia['estado']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($incidencia['email_cliente']) ?></td>
                        <td>
                            <?php if (!empty($incidencia['tecnico_nombre'])): ?>
                                <?= htmlspecialchars($incidencia['tecnico_nombre']) ?>
                            <?php else: ?>
                                <span style="color: #888;">Sin asignar</span>
                            <?php endif; ?>
                        </td>       
                        <td>
                            
                            <a href="editar_incidencia.php?id=<?= (int) $incidencia['id'] ?>" class="boton" title="Editar">✎</a>
                            <a href="eliminar_incidencia.php?id=<?= (int) $incidencia['id'] ?>" class="boton boton-rojo" title="Eliminar">✕</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>

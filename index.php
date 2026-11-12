<?php
require_once __DIR__ . '/conexion.php';

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
<div class="container">
    <h1>Incidencias Activas</h1>

    <?php if (empty($incidencias)): ?>
        <p>No hay incidencias activas.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($incidencias as $incidencia): ?>
                    <?php
                        $prioridadClass = match(strtolower($incidencia['prioridad'])) {
                            'alta' => 'badge-alta',
                            'media' => 'badge-media',
                            'baja' => 'badge-baja',
                            default => ''
                        };
                        $estadoSlug = strtolower(str_replace(' ', '-', $incidencia['estado']));
                        $estadoClass = match($estadoSlug) {
                            'pendiente' => 'badge-pendiente',
                            'en-proceso' => 'badge-en-proceso',
                            'resuelta' => 'badge-resuelta',
                            default => ''
                        };
                    ?>
                    <tr>
                        <td><?= (int) $incidencia['id'] ?></td>
                        <td><?= htmlspecialchars($incidencia['titulo']) ?></td>
                        <td><span class="badge <?= $prioridadClass ?>"><?= htmlspecialchars($incidencia['prioridad']) ?></span></td>
                        <td><span class="badge <?= $estadoClass ?>"><?= htmlspecialchars($incidencia['estado']) ?></span></td>
                        <td><?= htmlspecialchars($incidencia['email_cliente']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>

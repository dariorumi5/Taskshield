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
    <title>Document</title>
</head>
<body>
<h1>Incidencias activas</h1>

<?php if (empty($incidencias)): ?>
    <p>No hay incidencias activas.</p>
<?php else: ?>
    <table border="1">
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
                <tr>
                    <td><?= (int) $incidencia['id'] ?></td>
                    <td><?= htmlspecialchars($incidencia['titulo']) ?></td>
                    <td><?= htmlspecialchars($incidencia['prioridad']) ?></td>
                    <td><?= htmlspecialchars($incidencia['estado']) ?></td>
                    <td><?= htmlspecialchars($incidencia['email_cliente']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</body>
</html>
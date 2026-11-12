<?php
require_once __DIR__ . '/conexion.php';

$error = '';

// Si el usuario pulsa el botón de crear la incidencia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo        = trim($_POST['titulo'] ?? '');
    $descripcion   = trim($_POST['descripcion'] ?? '');
    $prioridad     = $_POST['prioridad'] ?? 'Media';
    $email_cliente = trim($_POST['email_cliente'] ?? '');

    // Comprobamos que no haya ningún campo vacío
    if (empty($titulo) || empty($descripcion) || empty($email_cliente)) {
        $error = "Por favor, completa todos los campos obligatorios.";
    } elseif (!filter_var($email_cliente, FILTER_VALIDATE_EMAIL)) {
        $error = "El formato del correo electrónico no es válido.";
    } else {
        // Guardamos la nueva incidencia en la base de datos
        $sql = "INSERT INTO incidencias (titulo, descripcion, prioridad, email_cliente, estado) 
                VALUES (:titulo, :descripcion, :prioridad, :email_cliente, 'Pendiente')";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':prioridad', $prioridad);
        $stmt->bindParam(':email_cliente', $email_cliente);

        $stmt->execute();

        // Redirigimos de vuelta a la página principal
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nueva Incidencia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="caja caja-formulario">
    <h1>Crear nueva incidencia</h1>

    <!-- Si hay algún error, lo mostramos en un recuadro rojo -->
    <?php if (!empty($error)): ?>
        <div class="mensaje-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form action="" method="POST">
        <div class="campo">
            <label for="titulo">Título de la Incidencia:</label>
            <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>" required>
        </div>

        <div class="campo">
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="3" required><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
        </div>
        
        <div class="campo">
            <label for="prioridad">Prioridad:</label>
            <select id="prioridad" name="prioridad">
                <option value="Baja">Baja</option>
                <option value="Media" selected>Media</option>
                <option value="Alta">Alta</option>
            </select>
        </div>
        
        <div class="campo">
            <label for="email_cliente">Email Cliente:</label>
            <input type="email" id="email_cliente" name="email_cliente" value="<?= htmlspecialchars($_POST['email_cliente'] ?? '') ?>" required>
        </div>
        
        <div class="fila-botones">
            <button type="submit" class="boton">Crear Incidencia</button>
            <a href="index.php" class="boton boton-gris">Volver al inicio</a>
        </div>
    </form>
</div>

</body>
</html>

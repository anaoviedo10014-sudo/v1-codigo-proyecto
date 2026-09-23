<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT u.*, j.nombre as jornada_nombre FROM usuario u JOIN jornada j ON u.id_jornada = j.id WHERE u.id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre_completo']);
    $contrasena = trim($_POST['contrasena']);
    
    if ($nombre) {
        if ($contrasena) {
            $stmt = $pdo->prepare("UPDATE usuario SET nombre_completo = ?, contrasena = MD5(?) WHERE id = ?");
            $stmt->execute([$nombre, $contrasena, $_SESSION['usuario_id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE usuario SET nombre_completo = ? WHERE id = ?");
            $stmt->execute([$nombre, $_SESSION['usuario_id']]);
        }
        $_SESSION['mensaje'] = 'Perfil actualizado correctamente';
        $_SESSION['tipo_mensaje'] = 'success';
        header('Location: perfil.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - SENA</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1> Mi Perfil</h1>
            <div class="user-info">
                <span> <?php echo htmlspecialchars($usuario['nombre_completo']); ?></span>
                <span class="badge"><?php echo htmlspecialchars($usuario['rol']); ?></span>
                <a href="logout.php" class="btn-logout">Cerrar sesión</a>
            </div>
        </div>

        <?php include 'includes/menu.php'; ?>
        </div>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?php echo $_SESSION['tipo_mensaje']; ?>">
                <?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h3> Editar perfil</h3>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Carnet</label>
                        <input type="text" value="<?php echo htmlspecialchars($usuario['carnet']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Nombre completo</label>
                        <input type="text" name="nombre_completo" value="<?php echo htmlspecialchars($usuario['nombre_completo']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Jornada</label>
                        <input type="text" value="<?php echo htmlspecialchars($usuario['jornada_nombre']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Nueva contraseña (dejar vacío para no cambiar)</label>
                        <input type="password" name="contrasena" placeholder="Nueva contraseña">
                    </div>
                    <div class="form-group" style="flex: 0 0 auto;">
                        <button type="submit" class="btn-success">Actualizar</button>
                        <a href="dashboard.php" class="btn-secondary">Volver</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
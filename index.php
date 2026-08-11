<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}
require_once 'config/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $carnet = $_POST['carnet'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM usuario WHERE carnet = ? AND contrasena = MD5(?)");
    $stmt->execute([$carnet, $contrasena]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['nombre_completo'] = $user['nombre_completo'];
        $_SESSION['rol'] = $user['rol'];
        $_SESSION['carnet'] = $user['carnet'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Carnet o contraseña incorrectos';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Control Computadores SENA</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTlx53alW_fiQQUdJweqdLRPRV0Tve-3sYre5cCPnkLiA&s" alt="SENA" class="logo">
            <h2>Control de Computadores</h2>
            <p style="color:#555; margin-bottom:20px;">Sistema de registro de entrada/salida</p>
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label>🔑 Usuario</label>
                    <input type="text" name="carnet" placeholder="Ej: SENA123" required autofocus>
                </div>
                <div class="form-group">
                    <label>🔒 Contraseña</label>
                    <input type="password" name="contrasena" placeholder="Ingresa tu contraseña" required>
                </div>
                
                <button type="submit" class="btn-primary">Ingresar</button>
            </form>

        </div>
    </div>
</body>
</html>
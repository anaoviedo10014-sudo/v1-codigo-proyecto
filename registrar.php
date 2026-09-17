<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
require_once 'config/db.php';

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $carnet = trim($_POST['carnet'] ?? '');
    $serial = trim($_POST['serial'] ?? '');
    $tipo = $_POST['tipo'] ?? 'entrada';
    
    if (empty($carnet) || empty($serial)) {
        $mensaje = '⚠️ Debe escanear el carnet y el serial del portátil.';
        $tipo_mensaje = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM usuario WHERE carnet = ?");
            $stmt->execute([$carnet]);
            $usuario = $stmt->fetch();
            
            if (!$usuario) {
                $mensaje = '❌ Carnet no registrado.';
                $tipo_mensaje = 'error';
            } else {
                $stmt = $pdo->prepare("
                    SELECT p.*, m.nombre as marca, mo.nombre as modelo 
                    FROM portatil p
                    JOIN marca m ON p.id_marca = m.id
                    JOIN modelo mo ON p.id_modelo = mo.id
                    WHERE p.serial = ?
                ");
                $stmt->execute([$serial]);
                $portatil = $stmt->fetch();
                
                if (!$portatil) {
                    $mensaje = '❌ Portátil no registrado.';
                    $tipo_mensaje = 'error';
                } else {
                    if ($portatil['asignado_a'] != $usuario['id']) {
                        $mensaje = '🚨 ALERTA: El portátil NO está asignado a este usuario.';
                        $tipo_mensaje = 'error';
                    } elseif ($tipo === 'salida' && $portatil['estado'] === 'fuera') {
                        $mensaje = '⚠️ Este portátil ya está registrado como fuera del centro.';
                        $tipo_mensaje = 'error';
                    } elseif ($tipo === 'entrada' && $portatil['estado'] === 'dentro') {
                    $mensaje = '⚠️ Este portátil ya está registrado como dentro del centro.';
                    $tipo_mensaje = 'error';
                    } else {
                        $stmt = $pdo->prepare("
                        INSERT INTO registro_entrada_salida (id_usuario, id_portatil, tipo, fecha_hora) 
                        VALUES (?, ?, ?, NOW())
                        ");
                        $stmt->execute([$usuario['id'], $portatil['id'], $tipo]);
                        $nuevo_estado = ($tipo === 'salida') ? 'fuera' : 'dentro';
                        $stmt2 = $pdo->prepare("UPDATE portatil SET estado = ? WHERE id = ?");
                        $stmt2->execute([$nuevo_estado, $portatil['id']]);
                        $mensaje = "✅ Registro de $tipo exitoso para " . $usuario['nombre_completo'];
                        $tipo_mensaje = 'success';
                    }
                }
            }
        } catch (PDOException $e) {
            $mensaje = 'Error: ' . $e->getMessage();
            $tipo_mensaje = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar - SENA</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>📋 Registrar Entrada/Salida</h1>
            <div class="user-info">
                <span>👤 <?= htmlspecialchars($_SESSION['nombre_completo']) ?></span>
                <span> <?= htmlspecialchars($_SESSION['carnet']) ?></span>
                <span class="badge"><?= htmlspecialchars($_SESSION['rol']) ?></span>
                <a href="logout.php" class="btn-logout">Cerrar sesión</a>
            </div>
        </div>

        <?php include 'includes/menu.php'; ?>
            <?php if ($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'administrador'): ?>
            <?php endif; ?>
        </div>

        <?php if ($mensaje): ?>
            <div class="alert <?= $tipo_mensaje === 'success' ? 'alert-success' : 'alert-error' ?>">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h3>🔐 Registro de Entrada / Salida</h3>
            <p>Escanea el carnet y el serial del portátil.</p>
        </div>

        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>🪪 Carnet</label>
                    <input type="text" name="carnet" placeholder="Ej: SENA123" required autofocus>
                </div>
                <div class="form-group">
                    <label>💻 Serial</label>
                    <input type="text" name="serial" placeholder="Ej: PC-001" required>
                </div>
                <div class="form-group">
                    <label>🔄 Tipo</label>
                    <select name="tipo" required>
                        <option value="entrada">Entrada</option>
                        <option value="salida">Salida</option>
                    </select>
                </div>
                <div class="form-group" style="flex: 0 0 auto;">
                    <button type="submit" class="btn-success">Registrar</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
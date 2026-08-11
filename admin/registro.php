<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'administrador')) {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';
$titulo = 'Registro Manual'; // ← CORREGIDO

$usuarios = $pdo->query("SELECT id, carnet, nombre_completo FROM usuario ORDER BY nombre_completo")->fetchAll();
$portatiles = $pdo->query("
    SELECT p.*, m.nombre as marca 
    FROM portatil p 
    JOIN marca m ON p.id_marca = m.id 
    ORDER BY p.serial
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $titulo ?> - SENA</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>📝 <?= $titulo ?></h1>
            <div class="user-info">
                <span>👤 <?= htmlspecialchars($_SESSION['nombre_completo']) ?></span>
                <a href="../logout.php" class="btn-logout">Cerrar sesión</a>
            </div>
        </div>

        <div class="nav-menu">
            <a href="../dashboard.php">Inicio</a>
            <a href="../registrar.php">Registrar Escaneo</a>
            <a href="../historial.php">Historial</a>
            <a href="../gestion_usuarios.php">Usuarios</a>
            <a href="portatil.php">Portátiles</a>
            <a href="marca.php">Marcas</a>
            <a href="modelo.php">Modelos</a>
            <a href="jornada.php">Jornadas</a>
            <a href="rol.php">Roles</a>
            <a href="tipo.php">Tipos</a>
            <a href="registro.php" class="active">Registro Manual</a>
        </div>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
                <?= $_SESSION['mensaje']; unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h3>Registro Manual de Entrada/Salida</h3>
            <p>Selecciona el usuario, portátil y tipo de movimiento.</p>
        </div>

        <form action="guardar_registro.php" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Usuario</label>
                    <select name="id_usuario" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach ($usuarios as $u): ?>
                            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['carnet'] . ' - ' . $u['nombre_completo']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Portátil</label>
                    <select name="id_portatil" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach ($portatiles as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['serial'] . ' - ' . $p['marca']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tipo</label>
                    <select name="tipo" required>
                        <option value="entrada">Entrada</option>
                        <option value="salida">Salida</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fecha</label>
                    <input type="date" name="fecha_registro" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label>Hora</label>
                    <input type="time" name="hora" value="<?= date('H:i') ?>" required>
                </div>
                <div class="form-group">
                    <label>Observación</label>
                    <input type="text" name="observacion" placeholder="Observación (opcional)">
                </div>
                <div class="form-group" style="flex: 0 0 auto;">
                    <button type="submit" class="btn-success">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'administrador')) {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';
$titulo = 'Gestionar Jornadas'; // ← CORREGIDO

$jornadas = $pdo->query("SELECT * FROM jornada ORDER BY id")->fetchAll();
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
            <h1>📋 <?= $titulo ?></h1>
            <div class="user-info">
                <span>👤 <?= htmlspecialchars($_SESSION['nombre_completo']) ?></span>
                <a href="../logout.php" class="btn-logout">Cerrar sesión</a>
            </div>
        </div>

        <div class="nav-menu">
            <a href="../dashboard.php">Inicio</a>
            <a href="../registrar.php">Registrar</a>
            <a href="../historial.php">Historial</a>
            <a href="../gestion_usuarios.php">Usuarios</a>
            <a href="portatil.php">Portátiles</a>
            <a href="marca.php">Marcas</a>
            <a href="modelo.php">Modelos</a>
            <a href="jornada.php" class="active">Jornadas</a>
            <a href="rol.php">Roles</a>
            <a href="tipo.php">Tipos</a>
            <a href="registro.php">Registro Manual</a>
        </div>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
                <?= $_SESSION['mensaje']; unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h3>Agregar Nueva Jornada</h3>
            <form action="guardar_jornada.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" placeholder="Ej: Mañana" required>
                    </div>
                    <div class="form-group" style="flex: 0 0 auto;">
                        <button type="submit" class="btn-success">Guardar</button>
                    </div>
                </div>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jornadas as $j): ?>
                    <tr>
                        <td><?= $j['id'] ?></td>
                        <td><?= htmlspecialchars($j['nombre']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
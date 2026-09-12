<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'administrador')) {
    header('Location: index.php');
    exit;
}
require_once 'config/db.php';

$usuarios = $pdo->query("
    SELECT u.*, j.nombre as jornada_nombre,
           (SELECT serial FROM portatil WHERE asignado_a = u.id LIMIT 1) as computador_asignado
    FROM usuario u 
    LEFT JOIN jornada j ON u.id_jornada = j.id 
    WHERE u.rol IN ('aprendiz', 'instructor')
    ORDER BY u.id DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Usuarios - SENA</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>👤 Gestionar Usuarios</h1>
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

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Carnet</th>
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th>Jornada</th>
                    <th>Computador</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['carnet']) ?></td>
                        <td><?= htmlspecialchars($u['nombre_completo']) ?></td>
                        <td><span class="badge-<?= $u['rol'] ?>"><?= htmlspecialchars($u['rol']) ?></span></td>
                        <td><?= $u['jornada_nombre'] ?? 'Sin asignar' ?></td>
                        <td><?= $u['computador_asignado'] ? htmlspecialchars($u['computador_asignado']) : 'Sin computador' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
require_once 'config/db.php';

$stmt = $pdo->prepare("SELECT * FROM usuario WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

$totalUsuarios = $pdo->query("SELECT COUNT(*) FROM usuario")->fetchColumn();
$totalPortatiles = $pdo->query("SELECT COUNT(*) FROM portatil")->fetchColumn();
$totalRegistros = $pdo->query("SELECT COUNT(*) FROM registro_entrada_salida")->fetchColumn();
$totalDisponibles = $pdo->query("SELECT COUNT(*) FROM portatil WHERE estado = 'disponible'")->fetchColumn();

$registros = $pdo->query("
    SELECT r.*, u.nombre_completo, u.rol, p.serial, m.nombre as marca
    FROM registro_entrada_salida r
    JOIN usuario u ON r.id_usuario = u.id
    JOIN portatil p ON r.id_portatil = p.id
    JOIN marca m ON p.id_marca = m.id
    ORDER BY r.fecha_hora DESC
    LIMIT 10
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SENA</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>📋 Dashboard</h1>
            <div class="user-info">
                <span>👤 <?= htmlspecialchars($usuario['nombre_completo']) ?></span>
                <span>🪪 <?= htmlspecialchars($usuario['carnet']) ?></span>
                <span class="badge-<?= $usuario['rol'] ?>"><?= ucfirst($usuario['rol']) ?></span>
                <a href="logout.php" class="btn-logout">Cerrar sesión</a>
            </div>
        </div>

        <?php include 'includes/menu.php'; ?>
            <?php if ($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'administrador'): ?>
            <?php endif; ?>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
            <div class="card" style="text-align:center;">
                <h2><?= $totalUsuarios ?></h2>
                <p>Usuarios</p>
            </div>
            <div class="card" style="text-align:center;">
                <h2><?= $totalPortatiles ?></h2>
                <p>Portátiles</p>
            </div>
            <div class="card" style="text-align:center;">
                <h2><?= $totalRegistros ?></h2>
                <p>Movimientos</p>
            </div>
            <div class="card" style="text-align:center;">
                <h2><?= $totalDisponibles ?></h2>
                <p>Disponibles</p>
            </div>
        </div>

        <div class="card">
            <h3>📋 Últimos registros</h3>
            <table>
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Portátil</th>
                        <th>Tipo</th>
                        <th>Fecha/Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registros as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['nombre_completo']) ?></td>
                        <td><span class="badge-<?= $r['rol'] ?>"><?= htmlspecialchars($r['rol']) ?></span></td>
                        <td><?= htmlspecialchars($r['serial'] . ' - ' . $r['marca']) ?></td>
                        <td><span class="badge-<?= $r['tipo'] ?>"><?= ucfirst($r['tipo']) ?></span></td>
                        <td><?= date('d/m/Y H:i', strtotime($r['fecha_hora'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (count($registros) == 0): ?>
                    <tr><td colspan="5" style="text-align: center; color: #999;">No hay registros aún</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
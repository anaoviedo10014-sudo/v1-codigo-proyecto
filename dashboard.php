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
$totalDisponibles = $pdo->query("SELECT COUNT(*) FROM portatil WHERE estado = 'dentro'")->fetchColumn();

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
    <style>
        
        body, html { background: #f4f6f8 !important; background-color: #f4f6f8 !important; margin:0; }
        .dashboard-container { background: transparent !important; max-width:1150px; margin:0 auto; padding:15px; }
        .header { background: #5a9a3f !important; color: white; padding:15px 20px; border-radius:12px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; }
        .user-info { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
        .badge { background: rgba(255,255,255,0.25); padding:4px 10px; border-radius:6px; font-size:12px; }
        .btn-logout { background:white; color:#5a9a3f; padding:6px 12px; border-radius:6px; text-decoration:none; font-weight:bold; }
        
        .stats-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:15px; margin:20px 0; }
        .card { background:white !important; padding:20px; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); }
        .card h2 { margin:0; font-size:32px; color:#333; }
        .card p { margin:5px 0 0; color:#777; }
        .table-responsive { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; min-width:600px; }
        th { background:#5a9a3f; color:white; padding:10px; text-align:left; }
        td { padding:10px; border-bottom:1px solid #eee; }

        @media (max-width: 600px){
            .header { flex-direction:column; text-align:center; }
            .stats-grid { grid-template-columns:1fr 1fr; }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1 style="margin:0;font-size:22px">📋 Dashboard</h1>
            <div class="user-info">
                <span>👤 <?= htmlspecialchars($usuario['nombre_completo']) ?></span>
                <span><?= htmlspecialchars($_SESSION['carnet']) ?></span>
                <span class="badge"><?= htmlspecialchars($_SESSION['rol']) ?></span>
                <a href="logout.php" class="btn-logout">Cerrar sesión</a>
            </div>
        </div>

        <?php include 'includes/menu.php'; ?>

        <div class="stats-grid">
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
                <p>Dentro del centro</p>
            </div>
        </div>

        <div class="card">
            <h3>📋 Últimos registros</h3>
            <div class="table-responsive">
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
                            <td><?= htmlspecialchars($r['rol']) ?></td>
                            <td><?= htmlspecialchars($r['serial'] . ' - ' . $r['marca']) ?></td>
                            <td><?= ucfirst($r['tipo']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($r['fecha_hora'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (count($registros) == 0): ?>
                        <tr><td colspan="5" style="text-align:center;color:#999;">No hay registros aún</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
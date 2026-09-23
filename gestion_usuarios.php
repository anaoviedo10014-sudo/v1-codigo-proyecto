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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Usuarios - SENA</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body, html{background:#f4f6f8 !important; margin:0 !important; font-family:Arial, sans-serif;}
        .dashboard-container{max-width:1150px !important; margin:0 auto !important; padding:15px !important; background:transparent !important;}
        .header{background:#5a9a3f !important; color:white !important; padding:15px 20px !important; border-radius:12px !important; display:flex !important; justify-content:space-between !important; align-items:center !important; flex-wrap:wrap !important; gap:10px !important;}
        .user-info{display:flex; gap:8px; align-items:center; flex-wrap:wrap;}
        .badge{background:rgba(255,255,255,0.25); padding:4px 10px; border-radius:6px; font-size:12px;}
        .btn-logout{background:white; color:#5a9a3f; padding:6px 12px; border-radius:6px; text-decoration:none; font-weight:bold; border:1px solid #ddd;}
        
        .table-responsive{background:white; border-radius:12px; overflow-x:auto; box-shadow:0 2px 8px rgba(0,0,0,0.05); margin-top:15px;}
        table{width:100%; border-collapse:collapse; min-width:750px;}
        th{background:#5a9a3f; color:white; padding:12px; text-align:left; font-size:13px;}
        td{padding:10px 12px; border-bottom:1px solid #eee; font-size:14px;}

        @media(max-width:700px){
            .header{flex-direction:column; text-align:center;}
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1 style="margin:0;font-size:20px">👤 Gestionar Usuarios</h1>
            <div class="user-info">
                <span>👤 <?= htmlspecialchars($_SESSION['nombre_completo']) ?></span>
                <span><?= htmlspecialchars($_SESSION['carnet']) ?></span>
                <span class="badge"><?= htmlspecialchars($_SESSION['rol']) ?></span>
                <a href="logout.php" class="btn-logout">Cerrar sesión</a>
            </div>
        </div>

        <?php include 'includes/menu.php'; ?>

        <div class="table-responsive">
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
                            <td><?= htmlspecialchars($u['rol']) ?></td>
                            <td><?= $u['jornada_nombre'] ? htmlspecialchars($u['jornada_nombre']) : 'Sin asignar' ?></td>
                            <td><?= $u['computador_asignado'] ? htmlspecialchars($u['computador_asignado']) : 'Sin computador' ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(count($usuarios)==0): ?>
                        <tr><td colspan="6" style="text-align:center;color:#999;padding:20px">No hay usuarios</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
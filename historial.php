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

$where = [];
$params = [];
if (!empty($_GET['aprendiz'])) {
    $where[] = "(u.nombre_completo LIKE ? OR u.carnet LIKE ?)";
    $params[] = "%".$_GET['aprendiz']."%";
    $params[] = "%".$_GET['aprendiz']."%";
}
if (!empty($_GET['serial'])) {
    $where[] = "p.serial LIKE ?";
    $params[] = "%".$_GET['serial']."%";
}
if (!empty($_GET['desde'])) {
    $where[] = "DATE(r.fecha_hora) >= ?";
    $params[] = $_GET['desde'];
}
$sqlWhere = count($where) ? "WHERE ".implode(" AND ", $where) : "";
$registros = $pdo->prepare("
    SELECT r.*, u.nombre_completo, u.rol, u.carnet, p.serial, m.nombre as marca
    FROM registro_entrada_salida r
    JOIN usuario u ON r.id_usuario = u.id
    JOIN portatil p ON r.id_portatil = p.id
    JOIN marca m ON p.id_marca = m.id
    $sqlWhere
    ORDER BY r.fecha_hora DESC LIMIT 100
");
$registros->execute($params);
$registros = $registros->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial - SENA</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body, html { background: #f4f6f8 !important; margin:0; }
        .dashboard-container { max-width:1150px; margin:0 auto; padding:15px; }
        .header { background: #5a9a3f !important; color: white; padding:15px 20px; border-radius:12px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; }
        .card { background:white !important; padding:20px; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); margin-top:15px; }
        .filter-grid { display:grid; grid-template-columns: 1fr 1fr 1fr; gap:15px; }
        .filter-grid label { font-size:13px; font-weight:bold; display:block; margin-bottom:5px; }
        .filter-grid input { width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; box-sizing:border-box; }
        table { width:100%; border-collapse:collapse; min-width:700px; }
        th { background:#5a9a3f; color:white; padding:10px; text-align:left; }
        td { padding:10px; border-bottom:1px solid #eee; font-size:14px; }
        .table-responsive { overflow-x:auto; }
        .user-area { display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
        .btn-logout { background:white; color:#5a9a3f; padding:6px 14px; border-radius:6px; text-decoration:none; font-weight:bold; font-size:13px; }

        /* SOLO EN CELULAR SE CENTRA */
        @media(max-width:700px){ 
          .filter-grid { grid-template-columns:1fr; } 
          .header { flex-direction:column; text-align:center; }
          .user-area { width:100%; justify-content:center; flex-direction:column; }
          .btn-logout { margin:0 auto; display:block; }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1 style="margin:0;font-size:20px">📋 Historial</h1>
            <div class="user-area">
                <span>👤 <?= htmlspecialchars($usuario['nombre_completo']) ?></span>
                <span style="background:rgba(255,255,255,.25);padding:4px 10px;border-radius:6px;font-size:12px"><?= htmlspecialchars($_SESSION['rol']) ?></span>
                <a href="logout.php" class="btn-logout">Cerrar sesión</a>
            </div>
        </div>

        <?php include 'includes/menu.php'; ?>

        <div class="card">
            <form method="GET">
                <div class="filter-grid">
                    <div>
                        <label>Aprendiz (nombre o carnet)</label>
                        <input type="text" name="aprendiz" value="<?= htmlspecialchars($_GET['aprendiz'] ?? '') ?>" placeholder="Ej: Juan o SENA123">
                    </div>
                    <div>
                        <label>Serial del portátil</label>
                        <input type="text" name="serial" value="<?= htmlspecialchars($_GET['serial'] ?? '') ?>" placeholder="Ej: PC-001">
                    </div>
                    <div>
                        <label>Desde</label>
                        <input type="date" name="desde" value="<?= htmlspecialchars($_GET['desde'] ?? '') ?>">
                    </div>
                </div>
                <div style="margin-top:15px;display:flex;gap:10px;justify-content:flex-start;align-items:center">
                    <button type="submit" style="background:#5a9a3f;color:white;border:none;padding:10px 22px;border-radius:8px;font-weight:bold;cursor:pointer">Buscar</button>
                    <a href="historial.php" style="background:#f1f1f1;color:#333;padding:10px 18px;border-radius:8px;text-decoration:none;font-size:14px">Limpiar</a>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Carnet</th>
                            <th>Portátil</th>
                            <th>Tipo</th>
                            <th>Fecha/Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registros as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['nombre_completo']) ?></td>
                            <td><?= htmlspecialchars($r['carnet']) ?></td>
                            <td><?= htmlspecialchars($r['serial'].' - '.$r['marca']) ?></td>
                            <td><?= ucfirst($r['tipo']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($r['fecha_hora'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (count($registros)==0): ?>
                        <tr><td colspan="5" style="text-align:center;color:#999;padding:20px">No hay registros aún.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
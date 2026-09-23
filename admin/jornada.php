<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'administrador')) {
    header('Location: ../index.php'); exit;
}
require_once '../config/db.php';
$titulo = 'Gestionar Jornadas';
$jornadas = $pdo->query("SELECT * FROM jornada ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $titulo ?> - SENA</title>
<link rel="stylesheet" href="../css/style.css">
<style>
html,body{background:#f4f6f8 !important;margin:0 !important;font-family:Arial,sans-serif;width:100%;overflow-x:hidden}
.dashboard-container{max-width:1150px !important;width:95% !important;margin:0 auto !important;padding:15px !important}
.header{background:#5a9a3f !important;color:white !important;padding:15px 20px !important;border-radius:12px !important;display:flex !important;justify-content:space-between !important;align-items:center !important;flex-wrap:wrap !important;gap:10px}
.card{background:white !important;padding:20px !important;border-radius:12px !important;box-shadow:0 2px 8px rgba(0,0,0,0.05) !important;margin:15px 0 !important}
.form-grid{display:grid;grid-template-columns:1fr auto;gap:15px;align-items:end}
.form-group input{padding:10px;border:1px solid #ddd;border-radius:8px;width:100%;box-sizing:border-box}
.btn-success{background:#5a9a3f;color:white;border:none;padding:11px 18px;border-radius:8px;font-weight:bold;cursor:pointer}
.btn-danger{background:#e74c3c;color:white;padding:6px 12px;border-radius:6px;text-decoration:none;font-size:13px}
.table-responsive{background:white;border-radius:12px;overflow-x:auto;box-shadow:0 2px 8px rgba(0,0,0,0.05)}
table{width:100%;border-collapse:collapse;min-width:500px} th{background:#5a9a3f;color:white;padding:12px;text-align:left} td{padding:10px;border-bottom:1px solid #eee}
.alert{padding:12px;border-radius:8px;margin:15px 0} .alert-success{background:#d4edda;color:#155724} .alert-error{background:#f8d7da;color:#721c24}
@media(max-width:600px){.header{flex-direction:column;text-align:center} .form-grid{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="dashboard-container">
  <div class="header"><h1 style="margin:0;font-size:20px">📅 <?= $titulo ?></h1><a href="../logout.php" style="background:white;color:#5a9a3f;padding:6px 12px;border-radius:6px;text-decoration:none;font-weight:bold">Salir</a></div>
  <?php include_once '../includes/menu.php'; ?>
  <?php if(isset($_SESSION['mensaje'])): ?><div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>"><?= $_SESSION['mensaje']; unset($_SESSION['mensaje'],$_SESSION['tipo_mensaje']); ?></div><?php endif; ?>
  <div class="card">
    <form action="guardar_jornada.php" method="POST">
      <div class="form-grid">
        <div class="form-group"><label><b>Nombre de la jornada</b></label><input type="text" name="nombre" placeholder="Ej: Mañana" required></div>
        <button type="submit" class="btn-success">Guardar</button>
      </div>
    </form>
  </div>
  <div class="table-responsive">
    <table><thead><tr><th>ID</th><th>Nombre</th><th>Acción</th></tr></thead>
    <tbody><?php foreach($jornadas as $j): ?><tr><td><?= $j['id'] ?></td><td><?= htmlspecialchars($j['nombre']) ?></td><td><a href="eliminar.php?tabla=jornada&id=<?= $j['id'] ?>" onclick="return confirm('¿Eliminar?')" class="btn-danger">Eliminar</a></td></tr><?php endforeach; ?></tbody></table>
  </div>
</div>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'administrador')) {
    header('Location: index.php'); exit;
}
require_once 'config/db.php';
$portatiles = $pdo->query("SELECT p.*, m.nombre as marca, mo.nombre as modelo, u.nombre_completo as asignado_nombre FROM portatil p JOIN marca m ON p.id_marca = m.id JOIN modelo mo ON p.id_modelo = mo.id LEFT JOIN usuario u ON p.asignado_a = u.id ORDER BY p.id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestionar Portátiles - SENA</title>
<style>
body{background:#f4f6f8;margin:0;font-family:Arial,sans-serif}
.dashboard-container{max-width:1150px;margin:0 auto;padding:15px}
.header{background:#5a9a3f;color:white;padding:15px 20px;border-radius:12px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px}
.btn-logout{background:white;color:#5a9a3f;padding:6px 12px;border-radius:6px;text-decoration:none;font-weight:bold}

/* MENU */
.navbar{background:white;padding:12px;border-radius:12px;margin:15px 0;box-shadow:0 2px 8px rgba(0,0,0,0.06)}
.menu-toggle{display:none;background:#5a9a3f;color:white;border:none;padding:12px;width:100%;border-radius:8px;font-size:16px;font-weight:bold;cursor:pointer}
.nav-links{display:flex;flex-wrap:wrap;gap:8px}
.nav-links a{text-decoration:none;color:#333;background:#f1f1f1;padding:8px 14px;border-radius:20px;font-size:14px}
.nav-links a:hover{background:#5a9a3f;color:white}
.table-responsive{background:white;border-radius:12px;overflow-x:auto}
table{width:100%;border-collapse:collapse;min-width:650px}
th{background:#5a9a3f;color:white;padding:12px;text-align:left} td{padding:10px;border-bottom:1px solid #eee}


@media (max-width: 900px){
  .menu-toggle{display:block !important}
  .nav-links{display:none !important;flex-direction:column;margin-top:10px}
  .nav-links.show{display:flex !important}
  .nav-links a{width:100%;text-align:center;box-sizing:border-box}
}
</style>
</head>
<body>
<div class="dashboard-container">
  <div class="header">
    <h1 style="margin:0;font-size:20px">💻 Gestionar Portátiles</h1>
    <div>👤 <?= htmlspecialchars($_SESSION['nombre_completo']) ?> <a href="logout.php" class="btn-logout">Salir</a></div>
  </div>

  <nav class="navbar">
    <button class="menu-toggle" onclick="document.getElementById('navLinks').classList.toggle('show')">☰ Menú</button>
    <div class="nav-links" id="navLinks">
      <a href="dashboard.php">Inicio</a>
      <a href="registrar.php">Registrar</a>
      <a href="historial.php">Historial</a>
      <a href="gestion_usuarios.php">Usuarios</a>
      <a href="gestion_portatiles.php" style="background:#5a9a3f;color:white">Gestion Portatiles</a>
      <a href="guardar_marca.php">Marcas</a>
      <a href="guardar_modelo.php">Modelos</a>
      <a href="guardar_jornada.php">Jornadas</a>
      <a href="guardar_rol.php">Roles</a>
      <a href="guardar_tipo.php">Tipos</a>
      <a href="registro_manual.php">Registro Manual</a>
      <a href="reportes.php">Reportes</a>
    </div>
  </nav>

  <div class="table-responsive">
    <table>
      <thead><tr><th>ID</th><th>SERIAL</th><th>MARCA</th><th>MODELO</th><th>ASIGNADO A</th><th>ESTADO</th></tr></thead>
      <tbody>
      <?php foreach($portatiles as $p): ?>
        <tr><td><?= $p['id'] ?></td><td><?= htmlspecialchars($p['serial']) ?></td><td><?= htmlspecialchars($p['marca']) ?></td><td><?= htmlspecialchars($p['modelo']) ?></td><td><?= $p['asignado_nombre'] ? htmlspecialchars($p['asignado_nombre']) : 'Sin asignar' ?></td><td><?= ucfirst(str_replace('_',' ',$p['estado'])) ?></td></tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
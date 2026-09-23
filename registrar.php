<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tipo_movimiento'])) {
    $id_usuario = $_POST['id_usuario'];
    $id_portatil = $_POST['id_portatil'];
    $tipo = $_POST['tipo_movimiento'];
    $obs = $_POST['observacion'] ?? '';
    $stmt = $pdo->prepare("INSERT INTO registro_entrada_salida (id_usuario, id_portatil, tipo, fecha_hora, observacion) VALUES (?,?,?,NOW(),?)");
    $stmt->execute([$id_usuario, $id_portatil, $tipo, $obs]);
    $_SESSION['mensaje'] = "Registro de $tipo guardado";
    $_SESSION['tipo_mensaje'] = "success";
    header('Location: registrar.php');
    exit;
}
$usuarios = $pdo->query("SELECT id, carnet, nombre_completo FROM usuario ORDER BY nombre_completo")->fetchAll();
$portatiles = $pdo->query("SELECT p.id, p.serial, m.nombre as marca FROM portatil p JOIN marca m ON p.id_marca = m.id ORDER BY p.serial")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrador - SENA</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* QUITA TODO EL ROJO */
        body{background:#f4f6f8 !important}
        .dashboard-container{background:transparent !important;border:none !important;box-shadow:none !important;padding:15px !important}
        .header{background:#5a9a3f !important;border:none !important}
        .card{background:white !important;border:none !important}
        .navbar{background:white !important;border:none !important}
    </style>
</head>
<body>
<div class="dashboard-container">
    <div class="header">
        <h1>📝 Registrar</h1>
        <a href="logout.php" class="btn-logout">Cerrar sesión</a>
    </div>

    <?php include 'includes/menu.php'; ?>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>"><?= $_SESSION['mensaje']; unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?></div>
    <?php endif; ?>

    <div class="card" style="display:flex;justify-content:space-between;align-items:center;gap:10px">
        <span>¿Computador nuevo?</span>
        <a href="admin/registro_manual.php" style="background:#ff9800;color:white;padding:10px 18px;border-radius:8px;text-decoration:none;font-weight:bold">+ Registrar por primera vez</a>
    </div>

    <div class="card">
        <form method="POST">
            <label>Usuario</label><select name="id_usuario" required style="width:100%;padding:10px;margin-bottom:10px"><option value="">Seleccionar...</option><?php foreach($usuarios as $u):?><option value="<?=$u['id']?>"><?=$u['carnet'].' - '.$u['nombre_completo']?></option><?php endforeach;?></select>
            <label>Portátil</label><select name="id_portatil" required style="width:100%;padding:10px;margin-bottom:10px"><option value="">Seleccionar...</option><?php foreach($portatiles as $p):?><option value="<?=$p['id']?>"><?=$p['serial'].' - '.$p['marca']?></option><?php endforeach;?></select>
            <label>Tipo</label><select name="tipo_movimiento" required style="width:100%;padding:10px;margin-bottom:10px"><option value="entrada">Entrada</option><option value="salida">Salida</option></select>
            <label>Observación</label><input type="text" name="observacion" style="width:100%;padding:10px;margin-bottom:15px">
            <button type="submit" style="background:#5a9a3f;color:white;padding:12px 25px;border:none;border-radius:8px;font-weight:bold;width:100%">Guardar</button>
        </form>
    </div>
</div>
</body>
</html>
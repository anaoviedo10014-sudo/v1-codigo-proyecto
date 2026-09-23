<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php'); exit;
}
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try{
        $serial = trim($_POST['serial']);
        $id_marca = $_POST['id_marca'];
        $id_usuario = $_POST['id_usuario'];
        $check = $pdo->prepare("SELECT id FROM portatil WHERE serial = ?");
        $check->execute([$serial]);
        if($check->fetch()){
            $_SESSION['mensaje'] = "El serial ya existe";
            $_SESSION['tipo_mensaje'] = "error";
        } else {
            $pdo->prepare("INSERT INTO portatil (serial, id_marca, id_usuario) VALUES (?,?,?)")->execute([$serial, $id_marca, $id_usuario]);
            $_SESSION['mensaje'] = "Portátil $serial registrado";
            $_SESSION['tipo_mensaje'] = "success";
            header('Location: ../registrar.php'); exit;
        }
    }catch(Exception $e){
        $_SESSION['mensaje'] = "Error: ".$e->getMessage();
        $_SESSION['tipo_mensaje'] = "error";
    }
}
$marcas = $pdo->query("SELECT * FROM marca ORDER BY nombre")->fetchAll();
$usuarios = $pdo->query("SELECT id, carnet, nombre_completo FROM usuario ORDER BY nombre_completo")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro primera vez - SENA</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        *{box-sizing:border-box}
        body{margin:0;background:#f4f6f8 !important;font-family:Arial,sans-serif}
        .dashboard-container{max-width:1150px;margin:0 auto;padding:12px;background:transparent !important;border:none !important;box-shadow:none !important}
        .header{background:#5a9a3f !important;color:white;padding:16px 20px;border-radius:12px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px}
        .header h1{margin:0;font-size:18px}
        .card{background:white !important;padding:18px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.06);margin-top:12px;border:none !important;width:100%}
        .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
        label{font-size:13px;font-weight:bold;display:block;margin-bottom:6px}
        select,input{width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;font-size:15px}
        .btn-main{background:#5a9a3f;color:white;padding:13px 20px;border:none;border-radius:8px;font-weight:bold;font-size:16px;cursor:pointer;width:100%}
        .btn-logout{background:white;color:#5a9a3f;padding:6px 14px;border-radius:6px;text-decoration:none;font-weight:bold;font-size:13px}
        
        @media(max-width:700px){
            .dashboard-container{padding:8px}
            .header{flex-direction:column;text-align:center}
            .form-grid{grid-template-columns:1fr}
        }
    </style>
</head>
<body>
<div class="dashboard-container">
    <div class="header">
        <h1>💻 Registro primera vez</h1>
        <a href="../registrar.php" class="btn-logout">Volver</a>
    </div>

    <?php include '../includes/menu.php'; ?>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>" style="margin-top:12px;padding:12px;border-radius:8px;background:#fff"><?= $_SESSION['mensaje']; unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?></div>
    <?php endif; ?>

    <div class="card">
        <h3 style="margin-top:0">Nuevo computador en el sistema</h3>
        <p style="color:#666;font-size:14px;margin-top:0">Llena los datos del portátil que ingresa por primera vez.</p>
        
        <form method="POST">
            <div class="form-grid">
                <div>
                    <label>Serial *</label>
                    <input type="text" name="serial" required placeholder="Ej: 5CD1234XYZ">
                </div>
                <div>
                    <label>Marca *</label>
                    <select name="id_marca" required>
                        <option value="">Seleccionar</option>
                        <?php foreach($marcas as $m):?><option value="<?=$m['id']?>"><?=htmlspecialchars($m['nombre'])?></option><?php endforeach;?>
                    </select>
                </div>
                <div style="grid-column:1/-1">
                    <label>Usuario propietario *</label>
                    <select name="id_usuario" required>
                        <option value="">Seleccionar</option>
                        <?php foreach($usuarios as $u):?><option value="<?=$u['id']?>"><?=htmlspecialchars($u['carnet'].' - '.$u['nombre_completo'])?></option><?php endforeach;?>
                    </select>
                </div>
            </div>
            <div style="margin-top:18px">
                <button type="submit" class="btn-main">Guardar computador</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
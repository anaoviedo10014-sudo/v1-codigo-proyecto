<?php
session_start();
require_once 'config/db.php';
if (isset($_SESSION['usuario_id'])) { header('Location: dashboard.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_user = trim($_POST['usuario'] ?? $_POST['carnet'] ?? '');
    $input_pass = trim($_POST['password'] ?? $_POST['contrasena'] ?? '');
    try {
        $user = false;
        try {
            $stmt = $pdo->prepare("SELECT * FROM usuario WHERE carnet = ? OR usuario = ? LIMIT 1");
            $stmt->execute([$input_user, $input_user]);
            $user = $stmt->fetch();
        } catch (PDOException $e) {
            $stmt = $pdo->prepare("SELECT * FROM usuario WHERE carnet = ? LIMIT 1");
            $stmt->execute([$input_user]);
            $user = $stmt->fetch();
        }
        $loginOk = false; $col_pass = '';
        if ($user) {
            if (isset($user['contrasena'])) { $col_pass = 'contrasena'; $hash = $user['contrasena']; }
            else { $col_pass = 'password'; $hash = $user['password']; }
            if (password_verify($input_pass, $hash) || $hash === md5($input_pass) || $input_pass == $hash) {
                $loginOk = true;
                if ($hash === md5($input_pass)) {
                    $nuevoHash = password_hash($input_pass, PASSWORD_DEFAULT);
                    $up = $pdo->prepare("UPDATE usuario SET $col_pass = ? WHERE id = ?");
                    $up->execute([$nuevoHash, $user['id']]);
                }
            }
        }
        if ($loginOk) {
            $_SESSION['usuario_id'] = $user['id']; $_SESSION['user_id'] = $user['id']; $_SESSION['id'] = $user['id'];
            $_SESSION['nombre_completo'] = $user['nombre_completo']; $_SESSION['nombre'] = $user['nombre_completo'];
            $_SESSION['rol'] = $user['rol']; $_SESSION['carnet'] = $user['carnet'] ?? $user['usuario'] ?? '';
            $_SESSION['usuario'] = $user['usuario'] ?? $user['carnet'] ?? '';
            header('Location: dashboard.php'); exit;
        } else { $error = 'Usuario o contraseña incorrectos'; }
    } catch (Exception $e) { $error = 'Error: ' . $e->getMessage(); }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Control Computadores</title>
<link href="https://fonts.cdnfonts.com/css/daustley" rel="stylesheet">
<style>
*{box-sizing:border-box}
:root{ --verde: #2A8500; --verde-claro: #3daa0a; --verde-oscuro:#1a4d00; }
body{margin:0;min-height:100vh;background: linear-gradient(135deg, #f0f9e8 0%, #b6e39a 25%, var(--verde) 65%, var(--verde-oscuro) 100%);display:flex;align-items:center;justify-content:center;font-family:Arial,sans-serif;padding:15px}
.login-card{width:100%;max-width:360px;background: linear-gradient(180deg, #ffffff 0%, #f7fdf2 100%);border-radius:18px;padding:26px 24px 22px;box-shadow:0 15px 35px rgba(0,60,0,.25);border:1px solid rgba(42,133,0,.2);position:relative;overflow:hidden}
.login-card::before{content:"";position:absolute;top:0;left:0;right:0;height:5px;background: linear-gradient(90deg, #d9f0c7, var(--verde), var(--verde-oscuro));}
.logo-wrap{text-align:center;margin-bottom:8px}
.logo-wrap img{width:85px;height:auto;display:block;margin:0 auto 14px;object-fit:contain}
.title{font-family:Arial Black, sans-serif;font-size:22px;font-weight:900;line-height:1.1;color:#123800;margin:0;text-align:left;}
.title span{display:block}
.subtitle{font-family:'Daustley', cursive;font-size:26px;color:var(--verde);margin:10px 0 28px;line-height:1.1;text-align:left;}
.form-group{margin-bottom:14px;transition: all .3s cubic-bezier(.34,1.56,.64,1);}
.form-group label{font-size:11px;font-weight:800;color:var(--verde);display:block;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px}
.form-group input{width:100%;padding:12px 14px;border:1.5px solid #aed39a;border-radius:10px;font-size:14px;outline:none;background:#fbfff8;transition: all .3s ease;}
.form-group:hover{transform: translateX(6px) translateY(-1px);}
.form-group input:focus{border-color:var(--verde);background:white;box-shadow:0 0 0 3px rgba(42,133,0,.2);transform: scale(1.03) translateX(6px);}
.btn-ingresar{width:100%;margin-top:38px;padding:13px;background: var(--verde);background: linear-gradient(90deg, var(--verde-claro) 0%, var(--verde) 50%, var(--verde-oscuro) 100%);color:white;border:none;border-radius:10px;font-weight:800;font-size:15px;cursor:pointer;box-shadow:0 4px 12px rgba(42,133,0,.4);transition:.3s}
.btn-ingresar:hover{transform: translateY(-3px) scale(1.02);background: linear-gradient(90deg, var(--verde) 0%, var(--verde-oscuro) 100%);}
.alert{padding:10px;background:#fee2e2;color:#991b1b;border-radius:8px;font-size:13px;margin-bottom:14px;text-align:center}
</style>
</head>
<body>
<div class="login-card">
    <div class="logo-wrap">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTlx53alW_fiQQUdJweqdLRPRV0Tve-3sYre5cCPnkLiA&s" alt="Logo SENA">
        <h1 class="title">Control de <span>Computadores</span></h1>
        <p class="subtitle">Sistema de registro de entrada / salida</p>
    </div>
    <?php if($error): ?><div class="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST">
        <div class="form-group"><label>Usuario</label><input type="text" name="usuario" required value="ADMIN001"></div>
        <div class="form-group"><label>Contraseña</label><input type="password" name="password" required placeholder="••••••••"></div>
        <button type="submit" class="btn-ingresar">Ingresar</button>
    </form>
</div>
</body>
</html>
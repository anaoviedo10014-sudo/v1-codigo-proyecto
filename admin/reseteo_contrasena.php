<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'administrador')) {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';

$id = $_GET['id'] ?? 0;

$claveTemporal = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 8);
$hash = password_hash($claveTemporal, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE usuario SET contrasena = ? WHERE id = ?");
$stmt->execute([$hash, $id]);

$stmt2 = $pdo->prepare("SELECT nombre_completo FROM usuario WHERE id = ?");
$stmt2->execute([$id]);
$usuario = $stmt2->fetch();

$_SESSION['mensaje'] = "🔑 Nueva contraseña para " . htmlspecialchars($usuario['nombre_completo']) . ": <strong>$claveTemporal</strong> (comunícasela en persona, no queda guardada en ningún lado)";
$_SESSION['tipo_mensaje'] = 'success';

header('Location: usuario.php');
exit;
?>
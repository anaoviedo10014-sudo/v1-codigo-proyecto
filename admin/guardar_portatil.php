<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'administrador')) {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';

$serial = trim($_POST['serial'] ?? '');
$id_marca = $_POST['id_marca'] ?? '';
$id_modelo = $_POST['id_modelo'] ?? '';
$asignado_a = $_POST['asignado_a'] ?: null;
$estado = $_POST['estado'] ?? 'disponible';

if ($serial && $id_marca && $id_modelo) {
    try {
        $stmt = $pdo->prepare("INSERT INTO portatil (serial, id_marca, id_modelo, asignado_a, estado) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$serial, $id_marca, $id_modelo, $asignado_a, $estado]);
        $_SESSION['mensaje'] = '✅ Portátil guardado correctamente';
        $_SESSION['tipo_mensaje'] = 'success';
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = '❌ Error: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'error';
    }
} else {
    $_SESSION['mensaje'] = '⚠️ Serial, marca y modelo son obligatorios';
    $_SESSION['tipo_mensaje'] = 'error';
}

header('Location: portatil.php');
exit;
?>
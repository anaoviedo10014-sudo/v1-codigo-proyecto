<?php
session_start();
if (!isset($_SESSION['usuario_id'])) { header('Location: ../index.php'); exit; }
require_once '../config/db.php';
$nombre = trim($_POST['nombre'] ?? '');
if (empty($nombre)) {
    $_SESSION['mensaje'] = 'El nombre es obligatorio';
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: marca.php');
    exit;
}
try {
    $stmt = $pdo->prepare("INSERT INTO marca (nombre) VALUES (?)");
    $stmt->execute([$nombre]);
    $_SESSION['mensaje'] = '✅ Marca guardada correctamente';
    $_SESSION['tipo_mensaje'] = 'success';
} catch (PDOException $e) {
    $_SESSION['mensaje'] = '❌ Ya existe esa marca o error: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'error';
}
header('Location: marca.php');
exit;
?>
<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'administrador')) {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';

$nombre = trim($_POST['nombre'] ?? '');
$id_marca = $_POST['id_marca'] ?? '';

if ($nombre && $id_marca) {
    try {
        $stmt = $pdo->prepare("INSERT INTO modelo (nombre, id_marca) VALUES (?, ?)");
        $stmt->execute([$nombre, $id_marca]);
        $_SESSION['mensaje'] = '✅ Modelo guardado correctamente';
        $_SESSION['tipo_mensaje'] = 'success';
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['mensaje'] = '❌ Ya existe un modelo con ese nombre.';
        } else {
            $_SESSION['mensaje'] = '❌ Error al guardar: '.$e->getMessage();
        }
        $_SESSION['tipo_mensaje'] = 'error';
    }
} else {
    $_SESSION['mensaje'] = '⚠️ Todos los campos son obligatorios';
    $_SESSION['tipo_mensaje'] = 'error';
}
header('Location: modelo.php');
exit;
?>
<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'administrador')) {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';

$nombre = trim($_POST['nombre'] ?? '');

if ($nombre) {
    try {
        $stmt = $pdo->prepare("INSERT INTO tipo (nombre) VALUES (?)");
        $stmt->execute([$nombre]);
        $_SESSION['mensaje'] = '✅ Tipo guardado correctamente';
        $_SESSION['tipo_mensaje'] = 'success';
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['mensaje'] = '❌ Ya existe un tipo registrado con ese nombre.';
        } else {
            $_SESSION['mensaje'] = '❌ Ocurrió un error al guardar el tipo. Intenta de nuevo.';
        }
        $_SESSION['tipo_mensaje'] = 'error';
    }
} else {
    $_SESSION['mensaje'] = '⚠️ El nombre es obligatorio';
    $_SESSION['tipo_mensaje'] = 'error';
}

header('Location: tipo.php');
exit;

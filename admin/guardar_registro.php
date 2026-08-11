<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';

$id_usuario = $_POST['id_usuario'] ?? '';
$id_portatil = $_POST['id_portatil'] ?? '';
$tipo = $_POST['tipo'] ?? 'entrada';
$fecha = $_POST['fecha_registro'] ?? date('Y-m-d');
$hora = $_POST['hora'] ?? date('H:i:s');
$observacion = trim($_POST['observacion'] ?? '');

$fecha_hora = $fecha . ' ' . $hora . ':00';

if ($id_usuario && $id_portatil && $tipo) {
    try {
        // Verificar que el portátil esté asignado al usuario
        $stmt = $pdo->prepare("SELECT asignado_a FROM portatil WHERE id = ?");
        $stmt->execute([$id_portatil]);
        $portatil = $stmt->fetch();
        
        if ($portatil && $portatil['asignado_a'] != $id_usuario) {
            $_SESSION['mensaje'] = '⚠️ ALERTA: El portátil NO está asignado a este usuario.';
            $_SESSION['tipo_mensaje'] = 'error';
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO registro_entrada_salida (id_usuario, id_portatil, tipo, fecha_hora, observacion) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$id_usuario, $id_portatil, $tipo, $fecha_hora, $observacion]);
            $_SESSION['mensaje'] = '✅ Registro guardado correctamente';
            $_SESSION['tipo_mensaje'] = 'success';
        }
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = '❌ Error: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'error';
    }
} else {
    $_SESSION['mensaje'] = '⚠️ Todos los campos son obligatorios';
    $_SESSION['tipo_mensaje'] = 'error';
}

header('Location: registro.php');
exit;
?>
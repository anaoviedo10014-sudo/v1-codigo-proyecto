<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'administrador')) {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';

$carnet = trim($_POST['carnet'] ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$id_jornada = $_POST['id_jornada'] ?? '';
$id_rol = $_POST['id_rol'] ?? '';
$contrasena = trim($_POST['contrasena'] ?? '123456');

$nombre_completo = $nombre . ' ' . $apellido;

if ($carnet && $nombre && $apellido && $id_jornada && $id_rol) {
    try {
        
        $stmt = $pdo->prepare("SELECT nombre FROM rol WHERE id = ?");
        $stmt->execute([$id_rol]);
        $rol_data = $stmt->fetch();
        $rol_nombre = $rol_data ? strtolower($rol_data['nombre']) : 'aprendiz';

        $hashContrasena = password_hash($contrasena, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
        INSERT INTO usuario (carnet, nombre_completo, contrasena, rol, id_jornada) 
        VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$carnet, $nombre_completo, $hashContrasena, $rol_nombre, $id_jornada]);
        $_SESSION['mensaje'] = '✅ Usuario guardado correctamente';
        $_SESSION['tipo_mensaje'] = 'success';
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['mensaje'] = '❌ Ya existe un usuario registrado con ese carnet.';
        } else {
            $_SESSION['mensaje'] = '❌ Ocurrió un error al guardar el usuario. Intenta de nuevo.';
        }
        $_SESSION['tipo_mensaje'] = 'error';
    }
} else {
    $_SESSION['mensaje'] = '⚠️ Todos los campos son obligatorios';
    $_SESSION['tipo_mensaje'] = 'error';
}

header('Location: usuario.php');
exit;

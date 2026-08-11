<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
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
        // Obtener el nombre del rol para usarlo como rol en la tabla usuario
        $stmt = $pdo->prepare("SELECT nombre FROM rol WHERE id = ?");
        $stmt->execute([$id_rol]);
        $rol_data = $stmt->fetch();
        $rol_nombre = $rol_data ? strtolower($rol_data['nombre']) : 'aprendiz';
        
        $stmt = $pdo->prepare("
            INSERT INTO usuario (carnet, nombre_completo, contrasena, rol, id_jornada) 
            VALUES (?, ?, MD5(?), ?, ?)
        ");
        $stmt->execute([$carnet, $nombre_completo, $contrasena, $rol_nombre, $id_jornada]);
        $_SESSION['mensaje'] = '✅ Usuario guardado correctamente';
        $_SESSION['tipo_mensaje'] = 'success';
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = '❌ Error: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'error';
    }
} else {
    $_SESSION['mensaje'] = '⚠️ Todos los campos son obligatorios';
    $_SESSION['tipo_mensaje'] = 'error';
}

header('Location: usuario.php');
exit;
?>
<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'administrador')) {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';

$tablasPermitidas = [
    'marca'    => ['volver' => 'marca.php',    'dependencia' => 'modelo',   'columna' => 'id_marca'],
    'modelo'   => ['volver' => 'modelo.php',   'dependencia' => 'portatil', 'columna' => 'id_modelo'],
    'jornada'  => ['volver' => 'jornada.php',  'dependencia' => 'usuario',  'columna' => 'id_jornada'],
    'rol'      => ['volver' => 'rol.php',      'dependencia' => null,       'columna' => null],
    'tipo'     => ['volver' => 'tipo.php',     'dependencia' => null,       'columna' => null],
    'usuario'  => ['volver' => 'usuario.php',  'dependencia' => 'registro_entrada_salida', 'columna' => 'id_usuario'],
    'portatil' => ['volver' => 'portatil.php', 'dependencia' => 'registro_entrada_salida', 'columna' => 'id_portatil'],
];

$tabla = $_GET['tabla'] ?? '';
$id = $_GET['id'] ?? 0;

if (!isset($tablasPermitidas[$tabla])) {
    die('Tabla no permitida.');
}

$config = $tablasPermitidas[$tabla];


if ($config['dependencia']) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM {$config['dependencia']} WHERE {$config['columna']} = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['mensaje'] = '❌ No se puede eliminar: hay registros que dependen de este elemento.';
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: ' . $config['volver']);
        exit;
    }
}

$stmt = $pdo->prepare("DELETE FROM {$tabla} WHERE id = ?");
$stmt->execute([$id]);

$_SESSION['mensaje'] = '✅ Eliminado correctamente.';
$_SESSION['tipo_mensaje'] = 'success';
header('Location: ' . $config['volver']);
exit;
<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'administrador')) {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';
$titulo = 'Gestionar Tipos'; // ← CORREGIDO

$tipos = $pdo->query("SELECT * FROM tipo ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?= $titulo ?> - SENA</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>📌 <?= $titulo ?></h1>
            <div class="user-info">
                <span>👤 <?= htmlspecialchars($_SESSION['nombre_completo']) ?></span>
                <span> <?= htmlspecialchars($_SESSION['carnet']) ?></span>
                <span class="badge"><?= htmlspecialchars($_SESSION['rol']) ?></span>
                <a href="../logout.php" class="btn-logout">Cerrar sesión</a>
            </div>
        </div>

        <?php include '../includes/menu.php'; ?>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
                <?= $_SESSION['mensaje'];
                unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h3>Agregar Nuevo Tipo</h3>
            <form action="guardar_tipo.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" placeholder="Ej: Entrada/Salida" required>
                    </div>
                    <div class="form-group" style="flex: 0 0 auto;">
                        <button type="submit" class="btn-success">Guardar</button>
                    </div>
                </div>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tipos as $t): ?>
                    <tr>
                        <td><?= $t['id'] ?></td>
                        <td><?= htmlspecialchars($t['nombre']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
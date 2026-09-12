<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'administrador')) {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';
$titulo = 'Gestionar Modelos'; // ← CORREGIDO

$modelos = $pdo->query("
    SELECT mo.*, m.nombre as marca_nombre 
    FROM modelo mo 
    JOIN marca m ON mo.id_marca = m.id 
    ORDER BY m.nombre, mo.nombre
")->fetchAll();

$marcas = $pdo->query("SELECT * FROM marca ORDER BY nombre")->fetchAll();
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
            <h1>📱 <?= $titulo ?></h1>
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
            <h3>Agregar Nuevo Modelo</h3>
            <form action="guardar_modelo.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" placeholder="Ej: Galaxy S24" required>
                    </div>
                    <div class="form-group">
                        <label>Marca</label>
                        <select name="id_marca" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach ($marcas as $m): ?>
                                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
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
                    <th>Marca</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($modelos as $mo): ?>
                    <tr>
                        <td><?= $mo['id'] ?></td>
                        <td><?= htmlspecialchars($mo['nombre']) ?></td>
                        <td><?= htmlspecialchars($mo['marca_nombre']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
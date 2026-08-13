<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'administrador')) {
    header('Location: index.php');
    exit;
}
require_once 'config/db.php';

$portatiles = $pdo->query("
    SELECT p.*, m.nombre as marca, mo.nombre as modelo, u.nombre_completo as asignado_nombre
    FROM portatil p
    JOIN marca m ON p.id_marca = m.id
    JOIN modelo mo ON p.id_modelo = mo.id
    LEFT JOIN usuario u ON p.asignado_a = u.id
    ORDER BY p.id DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestionar Portátiles - SENA</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>💻 Gestionar Portátiles</h1>
            <div class="user-info">
                <span>👤 <?= htmlspecialchars($_SESSION['nombre_completo']) ?></span>
                <span>🪪 <?= htmlspecialchars($_SESSION['carnet']) ?></span>
                <span class="badge"><?= htmlspecialchars($_SESSION['rol']) ?></span>
                <a href="../logout.php" class="btn-logout">Cerrar sesión</a>
            </div>
        </div>

        <?php include 'includes/menu.php'; ?>
        <?php if ($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'administrador'): ?>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Serial</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Asignado a</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($portatiles as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= htmlspecialchars($p['serial']) ?></td>
                    <td><?= htmlspecialchars($p['marca']) ?></td>
                    <td><?= htmlspecialchars($p['modelo']) ?></td>
                    <td><?= $p['asignado_nombre'] ? htmlspecialchars($p['asignado_nombre']) : 'Sin asignar' ?></td>
                    <td><?= ucfirst(str_replace('_', ' ', $p['estado'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</body>

</html>
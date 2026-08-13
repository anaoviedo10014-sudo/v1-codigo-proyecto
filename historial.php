<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
require_once 'config/db.php';

$registros = $pdo->query("
    SELECT r.*, u.nombre_completo, u.carnet, u.rol, p.serial, m.nombre as marca
    FROM registro_entrada_salida r
    JOIN usuario u ON r.id_usuario = u.id
    JOIN portatil p ON r.id_portatil = p.id
    JOIN marca m ON p.id_marca = m.id
    ORDER BY r.fecha_hora DESC
    LIMIT 100
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial - SENA</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>📋 Historial</h1>
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

    <?php if (count($registros) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Fecha/Hora</th>
                    <th>Usuario</th>
                    <th>Carnet</th>
                    <th>Rol</th>
                    <th>Tipo</th>
                    <th>Portátil</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registros as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['fecha_hora']) ?></td>
                        <td><?= htmlspecialchars($r['nombre_completo']) ?></td>
                        <td><?= htmlspecialchars($r['carnet']) ?></td>
                        <td><span class="badge-<?= $r['rol'] ?>"><?= htmlspecialchars($r['rol']) ?></span></td>
                        <td><span class="<?= $r['tipo'] === 'entrada' ? 'badge-entrada' : 'badge-salida' ?>"><?= ucfirst($r['tipo']) ?></span></td>
                        <td><?= htmlspecialchars($r['serial']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No hay registros aún.</div>
    <?php endif; ?>
    </div>
</body>

</html>
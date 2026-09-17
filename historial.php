<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
require_once 'config/db.php';

$sql = "
    SELECT r.*, u.nombre_completo, u.carnet, u.rol, p.serial, m.nombre as marca
    FROM registro_entrada_salida r
    JOIN usuario u ON r.id_usuario = u.id
    JOIN portatil p ON r.id_portatil = p.id
    JOIN marca m ON p.id_marca = m.id
    WHERE 1=1
";
$params = [];
if (!empty($_GET['buscar'])) {
    $sql .= " AND (u.nombre_completo LIKE ? OR u.carnet LIKE ?)";
    $params[] = '%' . $_GET['buscar'] . '%';
    $params[] = '%' . $_GET['buscar'] . '%';
}
if (!empty($_GET['serial'])) {
    $sql .= " AND p.serial LIKE ?";
    $params[] = '%' . $_GET['serial'] . '%';
}
if (!empty($_GET['desde'])) {
    $sql .= " AND r.fecha_hora >= ?";
    $params[] = $_GET['desde'] . ' 00:00:00';
}
if (!empty($_GET['hasta'])) {
    $sql .= " AND r.fecha_hora <= ?";
    $params[] = $_GET['hasta'] . ' 23:59:59';
}
$sql .= " ORDER BY r.fecha_hora DESC LIMIT 100";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$registros = $stmt->fetchAll();
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
                <span> <?= htmlspecialchars($_SESSION['carnet']) ?></span>
                <span class="badge"><?= htmlspecialchars($_SESSION['rol']) ?></span>
                <a href="logout.php" class="btn-logout">Cerrar sesión</a>
            </div>
        </div>

        <?php include 'includes/menu.php'; ?>
            <?php if ($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'administrador'): ?>
            <?php endif; ?>
        </div>
        <div class="card">
    <form method="GET">
        <div class="form-row">
            <div class="form-group">
                <label>Aprendiz (nombre o carnet)</label>
                <input type="text" name="buscar" placeholder="Ej: Juan o SENA123" value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Serial del portátil</label>
                <input type="text" name="serial" placeholder="Ej: PC-001" value="<?= htmlspecialchars($_GET['serial'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Desde</label>
                <input type="date" name="desde" value="<?= htmlspecialchars($_GET['desde'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Hasta</label>
                <input type="date" name="hasta" value="<?= htmlspecialchars($_GET['hasta'] ?? '') ?>">
            </div>
            <div class="form-group" style="flex: 0 0 auto;">
                <button type="submit" class="btn-success">Buscar</button>
                <a href="historial.php" class="btn-logout">Limpiar</a>
            </div>
        </div>
    </form>
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
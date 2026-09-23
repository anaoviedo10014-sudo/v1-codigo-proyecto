<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'administrador')) {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';
$titulo = 'Reportes';
$aprendicesConPortatiles = $pdo->query("
    SELECT u.nombre_completo, u.carnet, p.serial, m.nombre as marca, p.estado
    FROM usuario u
    LEFT JOIN portatil p ON p.asignado_a = u.id
    LEFT JOIN marca m ON p.id_marca = m.id
    WHERE u.rol = 'aprendiz'
    ORDER BY u.nombre_completo
")->fetchAll();
$sql = "
    SELECT r.*, u.nombre_completo, u.carnet, p.serial
    FROM registro_entrada_salida r
    JOIN usuario u ON r.id_usuario = u.id
    JOIN portatil p ON r.id_portatil = p.id
    WHERE 1=1
";
$params = [];
if (!empty($_GET['desde'])) {
    $sql .= " AND r.fecha_hora >= ?";
    $params[] = $_GET['desde'] . ' 00:00:00';
}
if (!empty($_GET['hasta'])) {
    $sql .= " AND r.fecha_hora <= ?";
    $params[] = $_GET['hasta'] . ' 23:59:59';
}
$sql .= " ORDER BY r.fecha_hora DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$movimientos = $stmt->fetchAll();
$portatilesPorEstado = $pdo->query("
    SELECT p.serial, m.nombre as marca, p.estado, u.nombre_completo as asignado
    FROM portatil p
    JOIN marca m ON p.id_marca = m.id
    LEFT JOIN usuario u ON p.asignado_a = u.id
    ORDER BY p.estado, p.serial
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?> - SENA</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>📊 <?= $titulo ?></h1>
            <div class="user-info">
                <span>👤 <?= htmlspecialchars($_SESSION['nombre_completo']) ?></span>
                <span class="badge"><?= htmlspecialchars($_SESSION['rol']) ?></span>
                <a href="../logout.php" class="btn-logout">Cerrar sesión</a>
            </div>
        </div>

        <?php include '../includes/menu.php'; ?>

        <div class="card">
            <button class="btn-primary tab-btn" onclick="mostrarTab('tab1')">Aprendices y Portátiles</button>
            <button class="btn-primary tab-btn" onclick="mostrarTab('tab2')">Movimientos por Fecha</button>
            <button class="btn-primary tab-btn" onclick="mostrarTab('tab3')">Portátiles por Estado</button>
        </div>

        <div id="tab1" class="tab-content">
            <h3>Aprendices y sus portátiles asignados</h3>
            <table>
                <thead>
                    <tr><th>Aprendiz</th><th>Carnet</th><th>Serial</th><th>Marca</th><th>Estado</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($aprendicesConPortatiles as $a): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['nombre_completo']) ?></td>
                            <td><?= htmlspecialchars($a['carnet']) ?></td>
                            <td><?= $a['serial'] ? htmlspecialchars($a['serial']) : 'Sin portátil' ?></td>
                            <td><?= htmlspecialchars($a['marca'] ?? '-') ?></td>
                            <td><?= $a['estado'] ? ucfirst($a['estado']) : '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="tab2" class="tab-content" style="display:none;">
            <h3>Movimientos por rango de fechas</h3>
            <form method="GET">
                <input type="hidden" name="tab" value="tab2">
                <div class="form-row">
                    <div class="form-group">
                        <label>Desde</label>
                        <input type="date" name="desde" value="<?= htmlspecialchars($_GET['desde'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Hasta</label>
                        <input type="date" name="hasta" value="<?= htmlspecialchars($_GET['hasta'] ?? '') ?>">
                    </div>
                    <div class="form-group" style="flex: 0 0 auto;">
                        <button type="submit" class="btn-success">Filtrar</button>
                    </div>
                </div>
            </form>
            <table>
                <thead>
                    <tr><th>Aprendiz</th><th>Carnet</th><th>Serial</th><th>Tipo</th><th>Fecha/Hora</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($movimientos as $mv): ?>
                        <tr>
                            <td><?= htmlspecialchars($mv['nombre_completo']) ?></td>
                            <td><?= htmlspecialchars($mv['carnet']) ?></td>
                            <td><?= htmlspecialchars($mv['serial']) ?></td>
                            <td><?= ucfirst($mv['tipo']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($mv['fecha_hora'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="tab3" class="tab-content" style="display:none;">
            <h3>Portátiles por estado</h3>
            <table>
                <thead>
                    <tr><th>Serial</th><th>Marca</th><th>Estado</th><th>Asignado a</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($portatilesPorEstado as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['serial']) ?></td>
                            <td><?= htmlspecialchars($p['marca']) ?></td>
                            <td><?= ucfirst($p['estado']) ?></td>
                            <td><?= $p['asignado'] ? htmlspecialchars($p['asignado']) : 'Sin asignar' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function mostrarTab(id) {
            document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
            document.getElementById(id).style.display = 'block';
        }

        <?php if (isset($_GET['tab'])): ?>
            mostrarTab('<?= htmlspecialchars($_GET['tab']) ?>');
        <?php endif; ?>
    </script>
</body>
</html>
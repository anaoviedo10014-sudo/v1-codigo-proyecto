<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'administrador')) {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';
$titulo = 'Gestionar Portátiles'; // ← CORREGIDO

$portatiles = $pdo->query("
    SELECT p.*, m.nombre as marca, mo.nombre as modelo, u.nombre_completo as asignado_nombre
    FROM portatil p
    JOIN marca m ON p.id_marca = m.id
    JOIN modelo mo ON p.id_modelo = mo.id
    LEFT JOIN usuario u ON p.asignado_a = u.id
    ORDER BY p.id DESC
")->fetchAll();

$marcas = $pdo->query("SELECT * FROM marca ORDER BY nombre")->fetchAll();
$modelos = $pdo->query("SELECT * FROM modelo ORDER BY nombre")->fetchAll();
$usuarios = $pdo->query("SELECT id, carnet, nombre_completo FROM usuario WHERE rol IN ('aprendiz', 'instructor')")->fetchAll();
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
            <h1>💻 <?= $titulo ?></h1>
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
            <h3>Agregar Nuevo Portátil</h3>
            <form action="guardar_portatil.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Serial</label>
                        <input type="text" name="serial" placeholder="Ej: PC-010" required>
                    </div>
                    <div class="form-group">
                        <label>Marca</label>
                        <select name="id_marca" id="select-marca" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach ($marcas as $m): ?>
                                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Modelo</label>
                        <select name="id_modelo" id="select-modelo" required>
                            <option value="">Selecciona una marca primero</option>
                            <?php foreach ($modelos as $mo): ?>
                                <option value="<?= $mo['id'] ?>" data-marca="<?= $mo['id_marca'] ?>">
                                    <?= htmlspecialchars($mo['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Asignar a</label>
                        <select name="asignado_a">
                            <option value="">Sin asignar</option>
                            <?php foreach ($usuarios as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['carnet'] . ' - ' . $u['nombre_completo']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 0 0 auto;">
                        <button type="submit" class="btn-success">Guardar</button>
                    </div>
                </div>
            </form>

            <script>
                const modeloOptions = Array.from(document.querySelectorAll('#select-modelo option[data-marca]'));
                const selectMarca = document.getElementById('select-marca');
                const selectModelo = document.getElementById('select-modelo');

                function filtrarModelos() {
                    const marcaSeleccionada = selectMarca.value;
                    selectModelo.innerHTML = '<option value="">Seleccionar...</option>';

                    modeloOptions
                        .filter(opt => opt.dataset.marca === marcaSeleccionada)
                        .forEach(opt => selectModelo.appendChild(opt.cloneNode(true)));
                }

                selectMarca.addEventListener('change', filtrarModelos);
            </script>
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
                    <th>Accion</th>
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
                        <td>
                            <a href="eliminar.php?tabla=portatil&id=<?= $p['id'] ?>"
                                onclick="return confirm('¿Seguro que quieres eliminar este portátil?');"
                                class="btn-danger btn-sm">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
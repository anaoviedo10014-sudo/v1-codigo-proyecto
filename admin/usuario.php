<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'administrador')) {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';
$titulo = 'Gestionar Usuarios';

$usuarios = $pdo->query("
    SELECT u.*, j.nombre as jornada_nombre 
    FROM usuario u 
    LEFT JOIN jornada j ON u.id_jornada = j.id 
    ORDER BY u.id DESC
")->fetchAll();

$jornadas = $pdo->query("SELECT * FROM jornada")->fetchAll();
$roles = $pdo->query("SELECT * FROM rol")->fetchAll();
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
            <h1>👤 <?= $titulo ?></h1>
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
            <h3>Agregar Nuevo Usuario</h3>
            <form action="guardar_usuario.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Carnet</label>
                        <input type="text" name="carnet" placeholder="Ej: SENA999" required>
                    </div>
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" placeholder="Nombre" required>
                    </div>
                    <div class="form-group">
                        <label>Apellido</label>
                        <input type="text" name="apellido" placeholder="Apellido" required>
                    </div>
                    <div class="form-group">
                        <label>Jornada</label>
                        <select name="id_jornada" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach ($jornadas as $j): ?>
                                <option value="<?= $j['id'] ?>"><?= htmlspecialchars($j['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Rol</label>
                        <select name="id_rol" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nombre']) ?></option>
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
                    <th>Carnet</th>
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th>Jornada</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['carnet']) ?></td>
                        <td><?= htmlspecialchars($u['nombre_completo']) ?></td>
                        <td><?= htmlspecialchars($u['rol']) ?></td>
                        <td><?= $u['jornada_nombre'] ?? 'Sin asignar' ?></td>
                        <td>
                            <a href="eliminar.php?tabla=usuario&id=<?= $u['id'] ?>"
                                onclick="return confirm('¿Seguro que quieres eliminar este usuario?');"
                                class="btn-danger btn-sm">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
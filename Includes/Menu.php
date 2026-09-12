<?php

$rol_actual = $_SESSION['rol'] ?? '';
$carnet_actual = $_SESSION['carnet'] ?? '';
$nombre_actual = $_SESSION['nombre_completo'] ?? '';


$es_admin = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
$prefix = $es_admin ? '../' : '';


$pagina_actual = basename($_SERVER['REQUEST_URI']);
$pagina_actual = explode('?', $pagina_actual)[0];
?>

<div class="nav-menu">
    <a href="<?= $prefix ?>dashboard.php" class="<?= $pagina_actual == 'dashboard.php' ? 'active' : '' ?>">Inicio</a>
    <a href="<?= $prefix ?>registrar.php" class="<?= $pagina_actual == 'registrar.php' ? 'active' : '' ?>">Registrar</a>
    <a href="<?= $prefix ?>historial.php" class="<?= $pagina_actual == 'historial.php' ? 'active' : '' ?>">Historial</a>
    <a href="<?= $prefix ?>gestion_usuarios.php" class="<?= $pagina_actual == 'gestion_usuarios.php' ? 'active' : '' ?>">Usuarios</a>
    <a href="<?= $prefix ?>gestion_portatiles.php" class="<?= $pagina_actual == 'gestion_portatiles.php' ? 'active' : '' ?>">Computadores</a>
    
    
    <?php if ($rol_actual == 'admin' || $rol_actual == 'administrador'): ?>
        <a href="<?= $prefix ?>admin/usuario.php" class="<?= $pagina_actual == 'usuario.php' ? 'active' : '' ?>"> Admin Usuarios</a>
        <a href="<?= $prefix ?>admin/portatil.php" class="<?= $pagina_actual == 'portatil.php' ? 'active' : '' ?>"> Admin Portátiles</a>
        <a href="<?= $prefix ?>admin/marca.php" class="<?= $pagina_actual == 'marca.php' ? 'active' : '' ?>"> Marcas</a>
        <a href="<?= $prefix ?>admin/modelo.php" class="<?= $pagina_actual == 'modelo.php' ? 'active' : '' ?>"> Modelos</a>
        <a href="<?= $prefix ?>admin/jornada.php" class="<?= $pagina_actual == 'jornada.php' ? 'active' : '' ?>"> Jornadas</a>
        <a href="<?= $prefix ?>admin/rol.php" class="<?= $pagina_actual == 'rol.php' ? 'active' : '' ?>"> Roles</a>
        <a href="<?= $prefix ?>admin/tipo.php" class="<?= $pagina_actual == 'tipo.php' ? 'active' : '' ?>"> Tipos</a>
        <a href="<?= $prefix ?>admin/registro.php" class="<?= $pagina_actual == 'registro.php' ? 'active' : '' ?>"> Registro Manual</a>
    <?php endif; ?>
</div>
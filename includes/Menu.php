<?php
$enAdmin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$irRaiz = $enAdmin ? '../' : '';
$irAdmin = $enAdmin ? '' : 'admin/';
?>
<style>
.navbar{background:white;padding:12px;border-radius:12px;margin:15px 0;box-shadow:0 2px 8px rgba(0,0,0,.06);width:100%;box-sizing:border-box}
.menu-toggle{display:none;background:#5a9a3f;color:white;border:none;padding:12px;border-radius:8px;font-weight:bold;width:100%;cursor:pointer}
.nav-links{display:flex;flex-wrap:wrap;gap:8px}
.nav-links a{padding:8px 14px;border-radius:20px;text-decoration:none;color:#333;background:#f1f1f1;font-size:14px}
.nav-links a:hover{background:#5a9a3f;color:white}
@media(max-width:900px){
  .menu-toggle{display:block}
  .nav-links{display:none;flex-direction:column;margin-top:12px}
  .nav-links.show{display:flex}
  .nav-links a{width:100%;text-align:center;box-sizing:border-box}
}
</style>

<nav class="navbar">
  <button class="menu-toggle" onclick="document.getElementById('navLinks').classList.toggle('show')">☰ Menú</button>
  <div class="nav-links" id="navLinks">
    <a href="<?= $irRaiz ?>dashboard.php">Inicio</a>
    <a href="<?= $irRaiz ?>registrar.php">Registrar</a>
    <a href="<?= $irRaiz ?>historial.php">Historial</a>
    <a href="<?= $irAdmin ?>marca.php">Marcas</a>
    <a href="<?= $irAdmin ?>modelo.php">Modelos</a>
    <a href="<?= $irAdmin ?>jornada.php">Jornadas</a>
    <a href="<?= $irAdmin ?>rol.php">Roles</a>
    <a href="<?= $irAdmin ?>tipo.php">Tipos</a>
  </div>
</nav>
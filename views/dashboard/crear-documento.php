<?php include_once __DIR__ . '/header-dashboard.php'; ?>

<div class="contenedor-sm">
<?php include_once __DIR__ . '/../templates/alertas.php'; ?>
<form class="formulario" method="POST" action="/crear-documento">
<?php include_once __DIR__ . '/formulario-documento.php'; ?>
    <input type="submit" value="Crear Documento">
</form>
</div>

<?php include_once __DIR__ . '/footer-dashboard.php'; ?>
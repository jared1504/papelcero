<aside class="sidebar">
    <div class="contenedor-sidebar">
        <h2>PapelCero</h2>
        <div class="cerrar-menu">
            <img id="cerrar-menu" src="build/img/cerrar.svg" alt="imagen cerrar menu">
        </div>
    </div>

    <nav class="sidebar-nav">
        <a class="<?php echo ($titulo === 'Documentos Pendientes') ? 'activo' : ''; ?>" href="/dashboard">Documentos pendientes</a>
        <a class="<?php echo ($titulo === 'Documentos Firmados') ? 'activo' : ''; ?>" href="/firmados">Documentos firmados</a>
        <a class="<?php echo ($titulo === 'crear') ? 'activo' : ''; ?>" href="/crear-documento">Crear Documento</a>
        <a class="<?php echo ($titulo === 'Perfil') ? 'activo' : '';  ?>" href="/perfil">Perfil</a>
    </nav>
    <div class="cerrar-sesion-mobile">
    <a href="/logout" class="cerrar-sesion">Cerrar Sesión</a>
    </div>
</aside>
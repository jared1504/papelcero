<?php include_once __DIR__ . '/header-dashboard.php'; ?>

<div class="contenedor-sm">
    <div class="contenedor-nueva-tarea">
        <button type="button" class="agregar-tarea" id="agregar-tarea">&#43; Nueva Tarea</button>
    </div>

    <div class="filtros" id="filtros">
        <div class="filtros-inputs">
            <h2>Filtros:</h2>
            <div class="campo">
                <input type="radio" id="todas" name="filtro" value="" checked>
                <label for="todas">Todas</label>
            </div>
            <div class="campo">
                <input type="radio" id="completas" name="filtro" value="1">
                <label for="completas">Completas</label>
            </div>
            <div class="campo">
                <input type="radio" id="pendientes" name="filtro" value="0">
                <label for="pendientes">Pendientes</label>
            </div>
        </div>
    </div>
    <ul id="listado-tareas" class="listado-tareas"></ul>
</div>

<?php include_once __DIR__ . '/footer-dashboard.php'; ?>
<?php $script .= '
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="build/js/tareas.js"></script>
' ?>
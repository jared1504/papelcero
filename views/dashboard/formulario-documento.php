<div class="campo">
    <label for="nombre">Titulo</label>
    <input type="text" name="nombre" id="nombre" placeholder="Titulo del Documento">
</div>
<div class="campo">
    <label for="descripcion">Descripción</label>
    <input type="text" name="descripcion" id="descripcion" placeholder="Descripción del Documento">
</div>

<div class="campo">
    <label for="usuario">Usuario</label>
    <select name="usuario" id="usuario">
        <option value="">-- Seleccione --</option>
        <?php foreach ($usuarios as $usuario) { ?>
            <option 
                value="<?php echo s($usuario->id); ?>">
                <?php echo s($usuario->nombre); ?> </option>
        <?php } ?>
    
</div>
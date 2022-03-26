<div class="contenedor reestablecer">
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>
    <div class="contenedor-sm">
        <?php include_once __DIR__ . '/../templates/alertas.php';
        if ($mostrar) { ?>
        <p class="descripcion-pagina">Coloca tu nuevo Password</p>
            <form class="formulario" method="POST">

                <div class="campo">
                    <label for="password">Password</label>
                    <input type="password" id="password" placeholder="Tu Password" name="password">
                </div>
                <div class="campo">
                    <label for="password2">Confirmar Password</label>
                    <input type="password" id="password2" placeholder="Confirma tu Password" name="password2">
                </div>
                <input type="submit" class="boton" value="Guardar Password">
            </form>
        <?php } ?>
        <div class="acciones">
            <a href="/crear">¿Aún no tienes una cuenta? Obtener una</a>
            <a href="/olvide">¿Olvidaste tu Password?</a>
        </div>
    </div>
    <!--Contenedor-sm-->
</div>
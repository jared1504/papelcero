(function () {//IIFE -> proteger las variables de otros archivos

    obtenerTareas();
    let tareas = [];
    let filtradas = [];

    //boton para mostrar el modal de agregar tarea
    const nuevaTareaBtn = document.querySelector('#agregar-tarea');
    nuevaTareaBtn.addEventListener('click', function () {
        mostrarFormulario();
    });

    //filtros de busqueda
    const filtros = document.querySelectorAll('#filtros input[type="radio"]')
    filtros.forEach(radio => {
        radio.addEventListener('input', filtrarTareas);
    })
    function filtrarTareas(e) {
        const filtro = e.target.value;
        if (filtro !== '') {//filtrar tareas
            filtradas = tareas.filter(tarea => tarea.estado === filtro);
        } else {//mostrar todas las tareas
            filtradas = [];
        }
        mostrarTareas();
    }

    async function obtenerTareas() {
        try {
            const id = obtenerProyecto();
            const url = `/api/tareas?url=${id}`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();
            tareas = resultado.tareas;
            mostrarTareas();
        } catch (e) {
            console.log(e);
        }
    }
    function mostrarTareas() {
        limpiarTareas();
        totalPendientes();
        totalCompletadas();

        const arrayTareas = filtradas.length ? filtradas : tareas;
        if (arrayTareas.length === 0) {
            const contenedorTareas = document.querySelector('#listado-tareas');
            const textoNoTareas = document.createElement('LI');
            textoNoTareas.textContent = 'No Hay Tareas';
            textoNoTareas.classList.add('no-tareas');
            contenedorTareas.appendChild(textoNoTareas);
            return;
        }
        const estados = {
            0: 'Pendiente',
            1: 'Completa'
        }
        arrayTareas.forEach(tarea => {
            const contenedorTarea = document.createElement('LI');
            contenedorTarea.dataset.tareaId = tarea.id;
            contenedorTarea.classList.add('tarea');

            const nombreTarea = document.createElement('P');
            nombreTarea.textContent = tarea.nombre;

            const opcionesDiv = document.createElement('DIV');
            opcionesDiv.classList.add('opciones');

            //Botones
            const btnEditarTarea = document.createElement('BUTTON');
            btnEditarTarea.classList.add('editar-tarea');
            btnEditarTarea.textContent = 'Editar';
            btnEditarTarea.onclick = function () {
                mostrarFormulario(true, { ...tarea });//para editar
            };

            const btnEstadoTarea = document.createElement('BUTTON');
            btnEstadoTarea.classList.add('estado-tarea');
            btnEstadoTarea.classList.add(`${estados[tarea.estado].toLowerCase()}`);
            btnEstadoTarea.textContent = estados[tarea.estado];
            btnEstadoTarea.dataset.estadoTarea = tarea.estado;
            btnEstadoTarea.onclick = function () {//doble click
                cambiarEstadoTarea({ ...tarea });
            }

            const btnEliminarTarea = document.createElement('BUTTON');
            btnEliminarTarea.classList.add('eliminar-tarea');
            btnEliminarTarea.dataset.idTarea = tarea.id;
            btnEliminarTarea.textContent = 'Eliminar';
            btnEliminarTarea.onclick = function () {
                confirmarEliminarTarea({ ...tarea });
            };

            opcionesDiv.appendChild(btnEditarTarea);
            opcionesDiv.appendChild(btnEstadoTarea);
            opcionesDiv.appendChild(btnEliminarTarea);

            contenedorTarea.appendChild(nombreTarea);
            contenedorTarea.appendChild(opcionesDiv);

            const listadoTareas = document.querySelector('#listado-tareas');
            listadoTareas.appendChild(contenedorTarea);

        });
    }

    function totalPendientes() {
        const totalPendientes = tareas.filter(tarea => tarea.estado === "0");
        const pendientesRadio = document.querySelector('#pendientes');
        if (totalPendientes.length === 0) {
            pendientesRadio.disabled = true;
        }else{
            pendientesRadio.disabled = false;
        }
    }

    function totalCompletadas() {
        const totalPendientes = tareas.filter(tarea => tarea.estado === "1");
        const pendientesRadio = document.querySelector('#completas');
        if (totalPendientes.length === 0) {
            pendientesRadio.disabled = true;
        }else{
            pendientesRadio.disabled = false;
        }
    }
    function mostrarFormulario(editar = false, tarea = {}) {

        const modal = document.createElement('DIV');
        modal.classList.add('modal');
        modal.innerHTML = `
        <form class="formulario nueva-tarea">
            <legend>${editar ? 'Editar Tarea' : 'Añade una nueva tarea'}</legend>
            <div class="campo">
                <label>Tarea</label>
                <input type="text" name="tarea" 
                placeholder="${tarea.nombre ? 'Editar la Tarea' : 'Añadir Tarea al Proyecto Actual'}" 
                id="tarea" value="${tarea.nombre ? tarea.nombre : ''}"/>
            </div>
            <div class="opciones">
                <input type="submit" class="submit-nueva-tarea" 
                value="${tarea.nombre ? 'Guardar Cambios' : 'Añadir Tarea'}"/>
                <button type="button" class="cerrar-modal">Cancelar</button>
            </div>
        </form>
        `;
        setTimeout(() => {
            const formulario = document.querySelector('.formulario');
            formulario.classList.add('animar');
        }, 0);

        modal.addEventListener('click', function (e) {
            e.preventDefault();
            if (e.target.classList.contains('cerrar-modal')) {
                setTimeout(() => {
                    const formulario = document.querySelector('.formulario');
                    formulario.classList.add('cerrar');
                    modal.remove();
                }, 100);
            }
            if (e.target.classList.contains('submit-nueva-tarea')) {
                const nombreTarea = document.querySelector('#tarea').value.trim();//.trim()->limpiar espacios
                if (!nombreTarea) {//tarea vacia
                    //mostrar alerta de error
                    Swal.fire(
                        'El nombre de la tarea es obligatorio',
                        'El nombre de la tarea es obligatorio',
                        'error'
                    );
                    return;
                }
                if (editar) {
                    tarea.nombre = nombreTarea;//reescribir el nombre con lo que se puso en el formulario
                    actualizarTarea(tarea);
                } else {
                    agregarTarea(nombreTarea);
                }
            }
        });
        document.querySelector('.dashboard').appendChild(modal);
    }

    //muestra un mensaje en la interfaz
    function mostrarAlerta(mensaje, tipo, referencia) {
        //previene la creacion de multiples alertas
        const alertaPrevia = document.querySelector('.alerta');
        if (alertaPrevia) {
            alertaPrevia.remove();
        }
        const alerta = document.createElement('DIV');
        alerta.classList.add('alerta', tipo);
        alerta.textContent = mensaje;
        //inserta la alerta antes de la referencia
        referencia.parentElement.insertBefore(alerta, referencia.nextElementSibling);
        //Eliminar la alerta despues de 5 seg
        setTimeout(() => {
            alerta.remove();
        }, 1000);

    }

    //consultar el servidor para añadir una nueva tarea al proyecto actual
    async function agregarTarea(tarea) {
        //construir la peticion
        const datos = new FormData();
        datos.append('nombre', tarea);
        datos.append('url', obtenerProyecto());

        try {
            const url = 'http://localhost:3000/api/tarea';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });
            const resultado = await respuesta.json();
            Swal.fire(
                resultado.mensaje,
                resultado.mensaje,
                'success'
            );
            //mostrarAlerta(resultado.mensaje, resultado.tipo, document.querySelector('.formulario legend'));
            if (resultado.tipo === 'exito') {//Cerrar modal
                const modal = document.querySelector('.modal');
                setTimeout(() => {
                    modal.remove();
                }, 1000);
                //agregar el objeto de tarea al global de tarea
                const tareaObj = {
                    id: String(resultado.id),
                    nombre: tarea,
                    estado: "0",
                    proyectoId: resultado.proyectoId
                }
                tareas = [...tareas, tareaObj];
                mostrarTareas();
            }
        } catch (e) {
            console.log(e);
        }
    }

    function cambiarEstadoTarea(tarea) {
        tarea.estado = tarea.estado === "1" ? "0" : "1";//actulizar estado
        actualizarTarea(tarea);
    }

    async function actualizarTarea(tarea) {
        const { estado, id, nombre } = tarea;
        const datos = new FormData();
        datos.append('id', id);
        datos.append('nombre', nombre);
        datos.append('estado', estado);
        datos.append('url', obtenerProyecto());
        try {
            const url = 'http://localhost:3000/api/tarea/actualizar';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });
            const resultado = await respuesta.json();
            if (resultado.respuesta.tipo === 'exito') {
                Swal.fire(
                    resultado.respuesta.mensaje,
                    resultado.respuesta.mensaje,
                    'success'
                );
                const modal = document.querySelector('.modal');
                if (modal) {
                    modal.remove();
                }
                tareas = tareas.map(tareaMemoria => {
                    if (tareaMemoria.id === id) {
                        tareaMemoria.estado = estado;
                        tareaMemoria.nombre = nombre;
                    }
                    return tareaMemoria;
                });
                mostrarTareas();
            }
        } catch (error) {
            console.log(error);
        }
    }
    function confirmarEliminarTarea(tarea) {
        Swal.fire({
            title: '¿Eliminar Tarea?',
            text: "¡La tarea se eliminara!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, eliminar tarea'

        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                eliminarTarea(tarea);
            }
        })
    }
    async function eliminarTarea(tarea) {
        const { estado, id, nombre } = tarea;
        const datos = new FormData();
        datos.append('id', id);
        datos.append('nombre', nombre);
        datos.append('estado', estado);
        datos.append('url', obtenerProyecto());
        try {
            const url = 'http://localhost:3000/api/tarea/eliminar';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();
            if (resultado.resultado) {
                Swal.fire(
                    '¡Tarea Eliminada!',
                    resultado.mensaje,
                    'success'
                )

                tareas = tareas.filter(tareaMemoria => tareaMemoria.id !== tarea.id);
                mostrarTareas();
            }
        } catch (error) {
            console.log(e);
        }
    }
    function obtenerProyecto() {
        const proyectoParams = new URLSearchParams(window.location.search);
        const proyecto = Object.fromEntries(proyectoParams.entries());
        return proyecto.url;
    }
    function limpiarTareas() {
        const listadoTareas = document.querySelector('#listado-tareas');
        while (listadoTareas.firstChild) {
            listadoTareas.removeChild(listadoTareas.firstChild);
        }
    }
})();

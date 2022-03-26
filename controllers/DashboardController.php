<?php

namespace Controllers;

use Model\Proyecto;
use Model\Usuario;
use MVC\Router;

class DashboardController
{
    public static function index(Router $router)
    {
        //Iniciar la sesión del usuario
        session_start();
        isAuth();
        $id = $_SESSION['id'];
        //$proyectos = Proyecto::belongsTo('propietarioId', $id);
        $proyectos=[];
        $router->render('dashboard/index', [
            'titulo' => 'Documentos Pendientes',
            'proyectos' => $proyectos
        ]);
    }
    public static function firmados(Router $router)
    {
        //Iniciar la sesión del usuario
        session_start();
        isAuth();
        $id = $_SESSION['id'];
        //$proyectos = Proyecto::belongsTo('propietarioId', $id);
        $proyectos=[];
        $router->render('dashboard/firmados', [
            'titulo' => 'Documentos Firmados',
            'proyectos' => $proyectos
        ]);
    }
    public static function crear_documento(Router $router)
    {
        //Iniciar la sesión del usuario
        session_start();
        isAuth();
        $alertas = [];
        $usuarios= [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proyecto = new Proyecto($_POST);
            //Validacio
            $alertas = $proyecto->validarProyecto();
            if (empty($alertas)) {
                //Generar una URL única
                $proyecto->url = md5(uniqid());
                //almacenar el creador del proyecto
                $proyecto->propietarioId = $_SESSION['id'];
                //guardar el proyecto
                $proyecto->guardar();

                //Redireccionar
                header('Location: /proyecto?url=' . $proyecto->url);
            }
        }
        $router->render('dashboard/crear-documento', [
            'titulo' => 'Crear Documento',
            'alertas' => $alertas,
            'usuarios'=>$usuarios
        ]);
    }

    public static function proyecto(Router $router)
    {
        //Iniciar la sesión del usuario
        session_start();
        isAuth();

        $token = $_GET['url'];
        if (!$token) header('Location: /dashboard');
        //Revisar que la persona que visita el proyecto es quien lo creo
        $proyecto = Proyecto::where('url', $token);
        if ($proyecto->propietarioId !== $_SESSION['id']) {
            header('Location: /dashboard');
        }

        $router->render('dashboard/proyecto', [
            'titulo' => $proyecto->proyecto
        ]);
    }
    public static function perfil(Router $router)
    {
        //Iniciar la sesión del usuario
        session_start();
        isAuth();
        $alertas = [];
        $usuario = Usuario::find($_SESSION['id']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validar_perfil();
            if (empty($alertas)) {
                //verificar que el email no pertenesca a otro usuario
                $existeUsuario = Usuario::where('email', $usuario->email);

                if ($existeUsuario &&  $existeUsuario->id !== $usuario->id) {
                    //Mensaje de error
                    Usuario::setAlerta('error', 'El email ya es ocupado por otro usuario');
                } else {
                    //Actualizar Usuario
                    $usuario->guardar();
                    Usuario::setAlerta('exito', 'Actualizado Correctamente');

                    //Actualizar los datos de la session
                    $_SESSION['nombre'] = $usuario->nombre;
                    $_SESSION['email'] = $usuario->nombre;
                }
                $alertas = Usuario::getAlertas();
            }
        }
        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil',
            'alertas' => $alertas,
            'usuario' => $usuario
        ]);
    }
    public static function cambiar_password(Router $router)
    {
        //Iniciar la sesión del usuario
        session_start();
        isAuth();
        $alertas = [];
        //$usuario = Usuario::find($_SESSION['id']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = Usuario::find($_SESSION['id']);
            //sincronizar con los datos del usuraio
            $usuario->sincronizar($_POST);
            $alertas = $usuario->nuevo_password();
            if (empty($alertas)) {
                $resultado = $usuario->comprobar_password();
                if ($resultado) {
                    //asignar el nuevo password

                    unset($usuario->password2);
                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo2);
                    $usuario->password = $usuario->password_nuevo;
                    unset($usuario->password_nuevo);


                    //hashear el nuevo password
                    $usuario->hashPassword();
                    //actualizar
                    $resultado=$usuario->guardar();
                    if($resultado){
                        Usuario::setAlerta('exito', 'Password Actualizado Correctamente');
                    }
                } else {
                    Usuario::setAlerta('error', 'Password incorrecto');
                }
                 $alertas = Usuario::getAlertas();
            }
        }
        $router->render('dashboard/cambiar-password', [
            'titulo' => 'Cambiar Password',
            'alertas' => $alertas
        ]);
    }
}

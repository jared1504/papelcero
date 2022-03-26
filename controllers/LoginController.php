<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController
{
    public static function login(Router $router)
    {
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarLogin();
            if (empty($alertas)) {
                //buscar usuario
                $usuario = Usuario::where('email', $usuario->email);
                if (!$usuario) {
                    Usuario::setAlerta('error', 'El Usuario no Existe');
                } else if (!$usuario->confirmado) {
                    Usuario::setAlerta('error', 'El Usuario no está confirmado');
                } else {
                    //El usuario existe
                    if (password_verify($_POST['password'], $usuario->password)) {
                        //Iniciar la sesión del usuario
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //Redireccionar
                        header('Location: /dashboard');
                        
                    } else {
                        Usuario::setAlerta('error', 'El Password es Incorrecto');
                    }
                }
            }
        }
        $alertas = Usuario::getAlertas();
        //render a la vista
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }
    public static function logout()
    {
        session_start();
        $_SESSION=[];
        header('Location: /');
    }
    public static function crear(Router $router)
    {
        $usuario = new Usuario;
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarCuentaNueva();
            if (empty($alertas)) {
                $existeUsuario = Usuario::where('email', $usuario->email);
                if ($existeUsuario) {
                    Usuario::setAlerta('error', 'El email ya está asociado a una cuenta existente');
                    $alertas = Usuario::getAlertas();
                } else {
                    //hashear password
                    $usuario->hashPassword();
                    //eliminar password2
                    unset($usuario->password2);
                    //crear token
                    $usuario->crearToken();
                    //confirmado se pone 0 en automatico
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();
                    //crear nuevo usuario
                    $resultado = $usuario->guardar();
                    //Enviar Email
                    if ($resultado) {
                        header('Location: /mensaje');
                    }
                }
            }
        }
        //render a la vista
        $router->render('auth/crear', [
            'titulo' => 'Crea tu cuenta en UpTask',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }
    public static function olvide(Router $router)
    {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();
            if (empty($alertas)) {
                //buscar usuario
                $usuario = Usuario::where('email', $usuario->email);
                if ($usuario) {
                    //usuario encontrado
                    if ($usuario->confirmado) {
                        //Usuario existe y esta confirmado
                        //eliminar password2
                        unset($usuario->password2);
                        //Generar un nuevo token
                        $usuario->crearToken();
                        //Actualizar usuario
                        $usuario->guardar();

                        //Envial email
                        $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                        $email->enviarRecuperar();
                        //Imprimir Alerta
                        Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');
                    } else {
                        Usuario::setAlerta('error', 'el Usuario no está confirmado');
                    }
                } else {
                    //usuario no encontrado
                    Usuario::setAlerta('error', 'el Usuario no existe');
                }
            }
        }
        $alertas = Usuario::getAlertas();
        //muestra la vista
        $router->render('auth/olvide', [
            'titulo' => 'Olvide mi Password',
            'alertas' => $alertas
        ]);
    }
    public static function reestablecer(Router $router)
    {
        $mostrar = true;
        $token = s($_GET['token']);
        if (!$token) header('Location: /');

        //Encontrar al usuario con este token
        $usuario = Usuario::where('token', $token);
        if (empty($usuario)) {
            //no se encontro un usuario con ese token
            Usuario::setAlerta('error', 'Token No Válido');
            $mostrar = false;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //añadir el nuevo password
            $usuario->sincronizar($_POST);
            //validar password
            $alertas = $usuario->validarPassword();
            if (empty($alertas)) {
                //hashear password
                $usuario->hashPassword();
                //Reestablecer password
                $usuario->token = "";
                //eliminar la varible password2
                unset($usuario->password2);
                //Guardar en la base de datos
                $resultado = $usuario->guardar();
                if ($resultado) {
                    header('Location: /');
                }
            }
        }
        $alertas = Usuario::getAlertas();
        //muestra la vista
        $router->render('auth/reestablecer', [
            'titulo' => 'Reestablecer Password',
            'alertas' => $alertas,
            'mostrar' => $mostrar

        ]);
    }
    public static function mensaje(Router $router)
    {
        //muestra la vista
        $router->render('auth/mensaje', [
            'titulo' => 'Mensaje'
        ]);
    }
    public static function confirmar(Router $router)
    {
        $token = s($_GET['token']);
        if (!$token) header('Location: /');

        //Encontrar al usuario con este token
        $usuario = Usuario::where('token', $token);
        if (empty($usuario)) {
            //no se encontro un usuario con ese token
            Usuario::setAlerta('error', 'Token No Válido');
        } else {
            //Confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->token = "";
            //eliminar la varible password2
            unset($usuario->password2);
            //Guardar en la base de datos
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta Confirmada con éxito');
        }
        $alertas = Usuario::getAlertas();
        //muestra la vista
        $router->render('auth/confirmar', [
            'titulo' => 'Cuenta Confirmada',
            'alertas' => $alertas
        ]);
    }
}

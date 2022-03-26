<?php

namespace Model;

class Usuario extends ActiveRecord
{
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->password_nuevo2 = $args['password_nuevo2'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }
    //validar el login de usuarios
    public function validarLogin()
    {
        if (!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        } //validar formato del email
        else if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'El Email no es válido';
        }
        if (!$this->password) {
            self::$alertas['error'][] = 'El password no puede ir vacío';
        }
        return self::$alertas;
    }

    //validacion para cuentas nuevas
    public function validarCuentaNueva()
    {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre es Obligatorio';
        }
        if (!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }
        if (!$this->password) {
            self::$alertas['error'][] = 'El password no puede ir vacío';
        }
        if (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';
        }
        if ($this->password !== $this->password2) {
            self::$alertas['error'][] = 'Los passwords no coinciden';
        }
        return self::$alertas;
    }

    //validacion para cambio de password
    public function validarPassword()
    {
        if (!$this->password) {
            self::$alertas['error'][] = 'El password no puede ir vacío';
        } else if (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';
        } else if ($this->password !== $this->password2) {
            self::$alertas['error'][] = 'Los passwords no coinciden';
        }
        return self::$alertas;
    }
    public function nuevo_password()
    {
        if (!$this->password_actual) {
            self::$alertas['error'][] = 'El password actual no puede ir vacío';
        } else if (!$this->password_nuevo) {
            self::$alertas['error'][] = 'El nuevo password no puede ir vacío';
        } elseif (strlen($this->password_nuevo) < 6) {
            self::$alertas['error'][] = 'El nuevo password debe contener al menos 6 caracteres';
        } else if ($this->password_nuevo !== $this->password_nuevo2) {
            self::$alertas['error'][] = 'Los passwords nuevos no coinciden';
        }
        return self::$alertas;
    }

    public function validar_perfil()
    {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre es obligatorio';
        } else if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        return self::$alertas;
    }

    public function comprobar_password()
    {
        return password_verify($this->password_actual, $this->password);
    }
    //hashe el password
    public function hashPassword()
    {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }
    //generar un token
    public function crearToken()
    {
        $this->token = uniqid();
    }
    //valida un email
    public function validarEmail()
    {
        if (!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }
        //validar formato del email
        else if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'El Email no es válido';
        }
        return self::$alertas;
    }
}

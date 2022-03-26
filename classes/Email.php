<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email
{
    protected $email;
    protected $nombre;
    protected $token;
    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }


    public function enviarInstrucciones()
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '94b0bca18e6ef5';
        $mail->Password = '980cd11796b079';
        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('jaredvega1504@gmail.com', 'uptask.com');
        $mail->Subject = 'Confirma tu Cuenta';
        $mail->isHTML(true);
        $mail->CharSet = 'UTF_8';

        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has Creado tu cuenta en UpTask, 
        solo debes confirmarla en el siguiente enlace</p>";
        $contenido .= "<p>Presiona aquí: <a href='http://localhost:3000/confirmar?token=" . $this->token . "'>Confirmar Cuenta</a></p>";
        $contenido .= "<p>Si tú no creaste esta cuenta puedes ignorar este mensaje</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        //enviar el email
        $mail->send();
    }
    public function enviarRecuperar()
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '94b0bca18e6ef5';
        $mail->Password = '980cd11796b079';
        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'uptask.com');
        $mail->Subject = 'Reestablece tu Password';
        $mail->isHTML(true);
        $mail->CharSet = 'UTF_8';

        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Parece que has olvidado tu Password de tu cuenta de UpTask, 
        solo debes confirmarla en el siguiente enlace</p>";
        $contenido .= "<p>Presiona aquí: <a href='http://localhost:3000/reestablecer?token=" .
            $this->token . "'>Reestablecer Password</a></p>";
        $contenido .= "<p>Si tú no creaste esta cuenta puedes ignorar este mensaje</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        //enviar el email
        $mail->send();
    }
}

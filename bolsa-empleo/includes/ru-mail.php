<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviar_correo($para, $asunto, $mensaje_html, $nombre_para = '') {
    $mail = new PHPMailer(true);

    try {
        // Obtener configuración desde las opciones de WordPress
        $email_remitente = get_option('be_email_remitente', 'juansepulveda8010@gmail.com');
        $email_password = get_option('be_email_password', 'ukwv yeqq gphc jzkk');
        $nombre_remitente = get_option('be_email_nombre_remitente', 'Academia Aess');
        
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->CharSet = 'UTF-8';
        $mail->SMTPAuth   = true;
        $mail->Username   = $email_remitente;
        $mail->Password   = $email_password;
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom($email_remitente, $nombre_remitente);
        $mail->addAddress($para, $nombre_para);

        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $mensaje_html;

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Puedes guardar el error en un log si quieres
        return false;
    }
}
?>
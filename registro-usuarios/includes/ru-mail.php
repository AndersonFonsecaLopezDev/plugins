<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviar_correo($para, $asunto, $mensaje_html, $nombre_para = '') {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'camilocuencadev@gmail.com'; // Cambia esto
        $mail->Password   = 'fuqr ewex qtps zcmn'; // Cambia esto
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('camilocuencadev@gmail.com', 'Camilo Cuenca'); // Cambia esto
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
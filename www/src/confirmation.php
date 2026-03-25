<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'libs/vendor/autoload.php'; // Inclut PHPMailer

$email_utilisateur = $_SESSION['email']; 
$name = $_SESSION['name'];

$mail = new PHPMailer(true);

try {
    // Configuration SMTP Gmail
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'group18.inf1763@gmail.com'; // ton adresse Gmail
    $mail->Password   = 'isvf ydfy rugg ewbu '; 
    $mail->SMTPSecure = 'ssl'; // ou 'tls'
    $mail->Port       = 465;

    // Expéditeur et destinataire
    $mail->setFrom('group18.inf1763@gmail.com', 'Group18-INF1763');
    $mail->addAddress($email_utilisateur);    // destinataire = utilisateur
    $mail->addBCC('group18.inf1763@gmail.com');       // copie cachée envoyée à toi

    # recuperation du fichier json
    $data = json_decode(file_get_contents('php://input'), true);    #On recupere le fichier json contenant les infos
    $start = $data['start']['dateTime'] ?? null;
    $end = $data['end']['dateTime'] ?? null;

    // Contenu du mail
    $mail->isHTML(true);
    $mail->Subject = 'Confirmation de votre reservation';
    $mail->Body    = "
        <h2>Merci pour votre réservation !</h2>
        <p>Bonjour ".$name.",</p>
        <p>Votre séance pour le <b>" .date('d/m/Y à H:i', strtotime($start)) . "</b> est confirmée 🎉</p>
        <p>Nous avons hâte de vous rencontrer !</p>
        <p>
            Cordialement, 
            <br>
            Chelsea (Coach de vie)
        </p>
    ";

    $mail->send();
    
} catch (Exception $e) {
    echo "❌ Erreur : {$mail->ErrorInfo}";
}

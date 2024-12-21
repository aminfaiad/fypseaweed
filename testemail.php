<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '/var/www/html/phplib/vendor/autoload.php'; // Updated vendor path

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.zoho.com';            // Zoho SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'support@smartseaweed.site'; // Your Zoho email
    $mail->Password = "Gvg6EQZ'q6yrM,.";       // Your email password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use SSL
    $mail->Port = 465;                        // SMTP port for SSL

    // Email settings
    $mail->setFrom('support@smartseaweed.site', 'Smart Seaweed Support'); // Sender
    $mail->addAddress('amin.fauad@student.aiu.edu.my', 'Amin Fauad');    // Recipient
    $mail->Subject = 'Hello World';                                     // Subject
    $mail->Body = 'Hello World! This is a test email from Smart Seaweed.'; // Email body

    // Send the email
    $mail->send();
    echo 'Email has been sent successfully!';
} catch (Exception $e) {
    echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>


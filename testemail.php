<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '/var/www/html/phplib/vendor/autoload.php'; // Updated vendor path

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

$message = 'This is an automated message.' . PHP_EOL . PHP_EOL .
           'Your registration code is: 934920' . PHP_EOL . PHP_EOL .
           'Please enter this code within the next 10 minutes to proceed.' . PHP_EOL . PHP_EOL .
           'If you did not request this code, no further action is required.' . PHP_EOL . PHP_EOL .
           'Smartseaweed.site';




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
    $mail->addAddress('amin.fauad@student.aiu.edu.my');    // Recipient
    $mail->Subject = 'Verification code for registration';                                     // Subject
    //$mail->Body = 'Please enter this registration code within 10 minutes : 934920'; // Email body
    $mail->Body = $message; // Email body

    // Send the email
    $mail->send();
    echo 'Email has been sent successfully!';
} catch (Exception $e) {
    echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>


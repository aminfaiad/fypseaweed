<?php
require '/var/www/html/phplib/vendor/autoload.php'; // Updated vendor path
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


/**
 * Send a password reset email for Support Seaweed
 * 
 * @param string $email Recipient's email address
 * @param string $passwordResetLink Password reset link
 * @return bool|string Returns true on success or an error message on failure
 */
function sendPasswordResetEmail($email, $passwordResetLink) {
    $mail = new PHPMailer(true);
    

    try {
        
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.zoho.com';   // Replace with your SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'support@smartseaweed.site'; // SMTP username
        $mail->Password   = "Gvg6EQZ'q6yrM,.";             // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        // Email details
        $mail->setFrom('support@smartseaweed.site', 'Smart Seaweed Support'); // Sender
        $mail->addAddress($email); // Add recipient
        $mail->isHTML(true);       // Enable HTML format
        $mail->Subject = 'Reset Your Password - Support Seaweed';

        // Email body content
        $mail->Body = "
            <h3>Hello from Support Seaweed!</h3>
            <p>We received a request to reset your password. Click the link below to set a new password:</p>
            <p><a href='{$passwordResetLink}'>Reset Password</a></p>
            <p>If you didnt request this, please ignore this email. Your password will remain unchanged.</p>
            <br>
            <p>Thank you,<br>The Support Seaweed Team</p>
        ";

        $mail->AltBody = "Hello from Support Seaweed!\n\nWe received a request to reset your password. Copy and paste the following link into your browser to reset it:\n{$passwordResetLink}\n\nIf you didn’t request this, please ignore this email. Your password will remain unchanged.\n\nThank you,\nThe Support Seaweed Team";

        // Send the email
        if ($mail->send()) {
            return true; // Success
        }
        else{
            return false;
        }
    } catch (Exception $e) {
        return "Mailer Error: {$mail->ErrorInfo}"; // Return the error message
    }
}


sendPasswordResetEmail("amin.fauad@student.aiu.edu.my", "https://www.smartseaweed.com/")
?>




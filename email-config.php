<?php
/**
 * Email Configuration for Bhosale Opticians
 * 
 * SETUP INSTRUCTIONS:
 * 1. Install PHPMailer: composer require phpmailer/phpmailer
 * 2. Configure your email settings below
 * 3. For Gmail: Enable "Less secure app access" or use App Password
 */

// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');           // SMTP server (Gmail: smtp.gmail.com)
define('SMTP_PORT', 587);                        // SMTP port (587 for TLS, 465 for SSL)
define('SMTP_USERNAME', 'bhosaleopticians@gmail.com');  // Your email address
define('SMTP_PASSWORD', 'iaaqemzoyutsmdda');      // Your email password or App Password
define('SMTP_ENCRYPTION', 'tls');                // Encryption type (tls or ssl)

// Sender Information
define('MAIL_FROM_EMAIL', 'bhosaleopticians@gmail.com');
define('MAIL_FROM_NAME', 'Bhosale Opticians');

// Email Settings
define('MAIL_ENABLED', true);  // Set to true when email is configured

/**
 * Send Email Function
 */
function sendEmail($to, $subject, $body, $altBody = '') {
    // If email is not enabled, return false
    if (!MAIL_ENABLED) {
        return false;
    }
    
    // Check if PHPMailer is installed
    if (!file_exists('vendor/autoload.php')) {
        return false;
    }
    
    require_once 'vendor/autoload.php';
    
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION;
        $mail->Port       = SMTP_PORT;
        
        // Recipients
        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altBody ?: strip_tags($body);
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email Error: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Send Password Reset Email
 */
function sendPasswordResetEmail($email, $resetLink) {
    $subject = "Password Reset Request - Bhosale Opticians";
    
    $body = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%); color: white; padding: 30px; text-align: center; }
            .content { background: #f9f9f9; padding: 30px; }
            .button { display: inline-block; padding: 15px 30px; background: #8B4513; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Bhosale Opticians</h1>
            </div>
            <div class='content'>
                <h2>Password Reset Request</h2>
                <p>Hello,</p>
                <p>We received a request to reset your password. Click the button below to reset it:</p>
                <p style='text-align: center;'>
                    <a href='$resetLink' class='button'>Reset Password</a>
                </p>
                <p>Or copy and paste this link into your browser:</p>
                <p style='word-break: break-all; color: #8B4513;'>$resetLink</p>
                <p><strong>This link will expire in 1 hour.</strong></p>
                <p>If you didn't request a password reset, please ignore this email.</p>
                <p>Best regards,<br>Bhosale Opticians Team</p>
            </div>
            <div class='footer'>
                <p>Bhosale Opticians<br>
                1st Floor, Silver Springs, Hotgi Road<br>
                Solapur, Maharashtra 413003<br>
                Phone: +91 9960815363 | Email: bhosaleopticians@gmail.com</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $altBody = "Password Reset Request\n\n"
             . "We received a request to reset your password.\n\n"
             . "Click this link to reset it: $resetLink\n\n"
             . "This link will expire in 1 hour.\n\n"
             . "If you didn't request a password reset, please ignore this email.\n\n"
             . "Best regards,\nBhosale Opticians Team";
    
    return sendEmail($email, $subject, $body, $altBody);
}
?>

<?php
/**
 * Email Debug Test - Shows detailed error messages
 */

require_once 'email-config.php';

if (!file_exists('vendor/autoload.php')) {
    die('PHPMailer not installed. Run install-phpmailer-no-zip.php first.');
}

require_once 'vendor/autoload.php';

$error = '';
$success = '';

if (isset($_POST['send_test'])) {
    $testEmail = $_POST['test_email'];
    
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Server settings
        $mail->SMTPDebug = 2; // Enable verbose debug output
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION;
        $mail->Port       = SMTP_PORT;
        
        // Recipients
        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail->addAddress($testEmail);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Test Email from Bhosale Opticians';
        $mail->Body    = '<h1>Test Email</h1><p>If you received this, email is working!</p>';
        
        ob_start();
        $mail->send();
        $debug = ob_get_clean();
        
        $success = 'Email sent successfully!';
    } catch (Exception $e) {
        $debug = ob_get_clean();
        $error = "Email Error: {$mail->ErrorInfo}";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Debug Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h2>Email Debug Test</h2>
        
        <div class="card mb-4">
            <div class="card-body">
                <h5>Current Configuration:</h5>
                <table class="table table-sm">
                    <tr><td>SMTP Host:</td><td><?= SMTP_HOST ?></td></tr>
                    <tr><td>SMTP Port:</td><td><?= SMTP_PORT ?></td></tr>
                    <tr><td>SMTP Username:</td><td><?= SMTP_USERNAME ?></td></tr>
                    <tr><td>SMTP Password:</td><td><?= str_repeat('*', strlen(SMTP_PASSWORD)) ?> (<?= strlen(SMTP_PASSWORD) ?> characters)</td></tr>
                    <tr><td>Encryption:</td><td><?= SMTP_ENCRYPTION ?></td></tr>
                    <tr><td>Mail Enabled:</td><td><?= MAIL_ENABLED ? 'Yes' : 'No' ?></td></tr>
                </table>
            </div>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if (isset($debug)): ?>
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">Debug Output:</div>
                <div class="card-body">
                    <pre style="font-size: 11px; max-height: 400px; overflow-y: auto;"><?= htmlspecialchars($debug) ?></pre>
                </div>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="card">
            <div class="card-body">
                <h5>Send Test Email:</h5>
                <div class="mb-3">
                    <label>Email Address:</label>
                    <input type="email" name="test_email" class="form-control" required>
                </div>
                <button type="submit" name="send_test" class="btn btn-primary">Send Test Email</button>
            </div>
        </form>
        
        <div class="mt-4">
            <h5>Troubleshooting Steps:</h5>
            <ol>
                <li>Verify 2-Step Verification is enabled: <a href="https://myaccount.google.com/security" target="_blank">Google Security</a></li>
                <li>Generate new App Password: <a href="https://myaccount.google.com/apppasswords" target="_blank">App Passwords</a></li>
                <li>Copy the password WITHOUT spaces</li>
                <li>Update email-config.php with the new password</li>
                <li>Make sure SMTP_USERNAME is your full Gmail address</li>
            </ol>
        </div>
    </div>
</body>
</html>

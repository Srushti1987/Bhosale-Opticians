<?php
/**
 * Email Configuration Test Script
 * Use this to test if your email settings are working correctly
 */

require_once 'email-config.php';

// Check if PHPMailer is installed
$phpmailerInstalled = file_exists('vendor/autoload.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Configuration Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">📧 Email Configuration Test</h3>
                    </div>
                    <div class="card-body">
                        <h5>Configuration Status:</h5>
                        
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>PHPMailer Installed:</strong></td>
                                <td>
                                    <?php if($phpmailerInstalled): ?>
                                        <span class="badge bg-success">✓ Yes</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">✗ No</span>
                                        <br><small class="text-muted">Run: composer require phpmailer/phpmailer</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Email Enabled:</strong></td>
                                <td>
                                    <?php if(MAIL_ENABLED): ?>
                                        <span class="badge bg-success">✓ Enabled</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">⚠ Disabled</span>
                                        <br><small class="text-muted">Set MAIL_ENABLED = true in email-config.php</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>SMTP Host:</strong></td>
                                <td><?= SMTP_HOST ?></td>
                            </tr>
                            <tr>
                                <td><strong>SMTP Port:</strong></td>
                                <td><?= SMTP_PORT ?></td>
                            </tr>
                            <tr>
                                <td><strong>SMTP Username:</strong></td>
                                <td><?= SMTP_USERNAME ?></td>
                            </tr>
                            <tr>
                                <td><strong>SMTP Password:</strong></td>
                                <td>
                                    <?php if(SMTP_PASSWORD == 'your-app-password-here'): ?>
                                        <span class="badge bg-danger">✗ Not Configured</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">✓ Configured</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>From Email:</strong></td>
                                <td><?= MAIL_FROM_EMAIL ?></td>
                            </tr>
                            <tr>
                                <td><strong>From Name:</strong></td>
                                <td><?= MAIL_FROM_NAME ?></td>
                            </tr>
                        </table>

                        <?php if(isset($_POST['send_test'])): ?>
                            <?php
                            $testEmail = $_POST['test_email'];
                            
                            // Enable error reporting for debugging
                            error_reporting(E_ALL);
                            ini_set('display_errors', 1);
                            
                            // Try to send email and capture any errors
                            ob_start();
                            $result = sendPasswordResetEmail($testEmail, 'http://example.com/reset-password.php?token=test123');
                            $output = ob_get_clean();
                            ?>
                            <div class="alert alert-<?= $result ? 'success' : 'danger' ?>">
                                <?php if($result): ?>
                                    ✓ Test email sent successfully to <?= htmlspecialchars($testEmail) ?>!
                                    <br>Check your inbox (and spam folder).
                                <?php else: ?>
                                    ✗ Failed to send test email.
                                    <br><br>
                                    <strong>Possible issues:</strong>
                                    <ul class="text-start">
                                        <li>Gmail App Password might be incorrect</li>
                                        <li>2-Step Verification not enabled on Gmail</li>
                                        <li>Firewall blocking SMTP connection</li>
                                        <li>Internet connection issue</li>
                                    </ul>
                                    <strong>Check your email-config.php:</strong>
                                    <ul class="text-start">
                                        <li>SMTP_USERNAME should be your full Gmail address</li>
                                        <li>SMTP_PASSWORD should be the 16-character App Password (no spaces)</li>
                                        <li>MAIL_ENABLED should be true</li>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <hr>

                        <h5>Send Test Email:</h5>
                        <?php if($phpmailerInstalled && MAIL_ENABLED): ?>
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Test Email Address:</label>
                                    <input type="email" name="test_email" class="form-control" required 
                                           placeholder="Enter email to receive test">
                                </div>
                                <button type="submit" name="send_test" class="btn btn-primary">
                                    Send Test Email
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                Please install PHPMailer and enable email in email-config.php first.
                            </div>
                        <?php endif; ?>

                        <hr>

                        <h5>Setup Instructions:</h5>
                        <ol>
                            <li>Install PHPMailer: <code>composer require phpmailer/phpmailer</code></li>
                            <li>Configure email settings in <code>email-config.php</code></li>
                            <li>For Gmail, get App Password from Google Account settings</li>
                            <li>Set <code>MAIL_ENABLED = true</code></li>
                            <li>Test using the form above</li>
                        </ol>

                        <a href="EMAIL_SETUP_GUIDE.txt" class="btn btn-info" target="_blank">
                            📖 View Full Setup Guide
                        </a>
                        <a href="forgot-password.php" class="btn btn-secondary">
                            Go to Forgot Password
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

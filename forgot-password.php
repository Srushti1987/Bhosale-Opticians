<?php
require_once 'config.php';
require_once 'email-config.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    
    $conn = getDBConnection();
    $email = $conn->real_escape_string($email);
    
    // Check if email exists
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Delete old tokens for this email
        $delete_sql = "DELETE FROM password_resets WHERE email = '$email'";
        $conn->query($delete_sql);
        
        // Insert new token - use NOW() for created_at and DATE_ADD for expires_at
        $insert_sql = "INSERT INTO password_resets (email, token, created_at, expires_at) 
                      VALUES ('$email', '$token', NOW(), DATE_ADD(NOW(), INTERVAL 1 HOUR))";
        
        if ($conn->query($insert_sql)) {
            // Generate reset link - hardcode localhost for development
            $reset_link = "http://localhost/project/reset-password.php?token=" . $token;
            
            // Try to send email
            $emailSent = sendPasswordResetEmail($email, $reset_link);
            
            if ($emailSent) {
                $message = "Password reset link has been sent to your email address. Please check your inbox (and spam folder).";
            } else {
                // Fallback: Show link on page if email is not configured
                $message = "Password reset link has been generated!<br><br>"
                         . "<strong>Email sending is not configured yet.</strong><br>"
                         . "Click here to reset: <a href='$reset_link' class='alert-link'>Reset Password</a><br><br>"
                         . "<small class='text-muted'>To enable email sending, configure email-config.php with your SMTP settings.</small>";
            }
        } else {
            $error = 'Failed to generate reset link. Please try again.';
        }
    } else {
        $error = 'Email not found in our system.';
    }
    
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Bhosale Opticians</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Header -->
    <header class="sticky-top bg-white shadow-sm">
        <nav class="navbar navbar-expand-lg container">
            <div class="container-fluid px-3">
                <a class="navbar-brand fw-bold fs-4" href="index_updated.php">
                    Bhosale Opticians
                </a>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="index_updated.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Forgot Password Form -->
    <section class="container my-5 py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4 fw-bold">Forgot Password</h2>
                        <p class="text-center text-muted mb-4">Enter your email to receive a password reset link</p>
                        
                        <?php if($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <?php if($message): ?>
                        <div class="alert alert-success"><?= $message ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-4">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-lg" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-3 mb-3">Send Reset Link</button>
                        </form>
                        
                        <p class="text-center text-muted">
                            Remember your password? <a href="login.php" class="text-brown">Login</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

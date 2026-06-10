<?php
require_once 'config.php';

$error = '';
$success = '';
$token = $_GET['token'] ?? '';

// Verify token
$conn = getDBConnection();
$token = $conn->real_escape_string($token);

$sql = "SELECT * FROM password_resets WHERE token = '$token' AND expires_at > NOW()";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    $error = 'Invalid or expired reset token.';
    $valid_token = false;
} else {
    $valid_token = true;
    $reset_data = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $valid_token) {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all fields';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $email = $reset_data['email'];
        $email = $conn->real_escape_string($email);
        
        // Update password
        $update_sql = "UPDATE users SET password = '$hashed_password' WHERE email = '$email'";
        
        if ($conn->query($update_sql)) {
            // Delete used token
            $delete_sql = "DELETE FROM password_resets WHERE token = '$token'";
            $conn->query($delete_sql);
            
            $success = 'Password reset successful! You can now login with your new password.';
            $valid_token = false; // Hide the form after success
        } else {
            $error = 'Failed to reset password. Please try again. Error: ' . $conn->error;
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Bhosale Opticians</title>
    
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
            </div>
        </nav>
    </header>

    <!-- Reset Password Form -->
    <section class="container my-5 py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4 fw-bold">Reset Password</h2>
                        
                        <?php if($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <?php if($success): ?>
                        <div class="alert alert-success text-center">
                            <h4 class="alert-heading">✓ Success!</h4>
                            <p class="mb-3"><?= $success ?></p>
                            <hr>
                            <a href="login.php" class="btn btn-primary btn-lg w-100 mt-2">Go to Login Page</a>
                        </div>
                        <?php elseif($valid_token): ?>
                        
                        <form method="POST" action="reset-password.php?token=<?= htmlspecialchars($token) ?>">
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="password" class="form-control form-control-lg" required minlength="6">
                                <small class="text-muted">Minimum 6 characters</small>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control form-control-lg" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-3">Reset Password</button>
                        </form>
                        
                        <?php endif; ?>
                        
                        <p class="text-center text-muted mt-3">
                            <a href="login.php" class="text-brown">Back to Login</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

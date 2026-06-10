<?php
require_once 'config.php';

$error = '';
$role_type = isset($_GET['role']) ? $_GET['role'] : 'user';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $conn = getDBConnection();
    $email = $conn->real_escape_string($email);
    
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            // Redirect based on role
            if ($user['role'] == 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: user/dashboard.php');
            }
            exit();
        } else {
            $error = 'Invalid email or password';
        }
    } else {
        $error = 'Invalid email or password';
    }
    
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bhosale Opticians</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Header -->
    <header class="sticky-top bg-white shadow-sm">
        <nav class="navbar navbar-expand-lg container">
            <div class="container-fluid px-3">
                <a class="navbar-brand fw-bold fs-4" href="index.php">
                    Bhosale Opticians
                </a>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                        <li class="nav-item"><a class="nav-link active" href="login.php">Login</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Login Form -->
    <section class="container my-5 py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <?php if($role_type == 'admin'): ?>
                        <div class="text-center mb-4">
                            <div class="badge bg-danger fs-5 mb-3">👨‍💼 Admin Login</div>
                            <h2 class="fw-bold">Administrator Access</h2>
                        </div>
                        <?php else: ?>
                        <div class="text-center mb-4">
                            <div class="badge bg-primary fs-5 mb-3">👤 User Login</div>
                            <h2 class="fw-bold">Welcome Back</h2>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control form-control-lg" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control form-control-lg" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-3 mb-3">Login</button>
                        </form>
                        
                        <p class="text-center text-muted">
                            <?php if($role_type == 'admin'): ?>
                            <a href="role-selection.php" class="text-brown">← Back to Role Selection</a>
                            <?php else: ?>
                            Don't have an account? <a href="register.php" class="text-brown">Register</a>
                            <?php endif; ?>
                        </p>
                        
                        <p class="text-center">
                            <a href="forgot-password.php" class="text-muted small">Forgot Password?</a>
                        </p>
                        
                        <?php if($role_type == 'user'): ?>
                        <p class="text-center mt-3">
                            <a href="role-selection.php" class="text-muted small">← Back to Role Selection</a>
                        </p>
                        <?php endif; ?>
                        
                        <hr class="my-4">
                        
                        <p class="text-center text-muted small">
                            <strong>Demo Accounts:</strong><br>
                            User: john@example.com / password123<br>
                            Admin: admin@sunray.com / password123
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

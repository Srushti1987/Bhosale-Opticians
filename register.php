<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = isset($_POST['phone']) ? $_POST['phone'] : NULL;
    $role = isset($_POST['role']) ? $_POST['role'] : 'user'; // Get role from form
    
    if ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) != 6) {
        $error = 'Password must be exactly 6 characters';
    } else {
        $conn = getDBConnection();
        $name = $conn->real_escape_string($name);
        $email = $conn->real_escape_string($email);
        $phone = $phone ? $conn->real_escape_string($phone) : NULL;
        $role = $conn->real_escape_string($role);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Check if email exists
        $check_sql = "SELECT * FROM users WHERE email = '$email'";
        $check_result = $conn->query($check_sql);
        
        if ($check_result->num_rows > 0) {
            $error = 'Email already exists';
        } else {
            // Check if phone column exists
            $column_check = $conn->query("SHOW COLUMNS FROM users LIKE 'phone'");
            
            if ($column_check->num_rows > 0) {
                // Phone column exists, include it
                $phone_value = $phone ? "'$phone'" : "NULL";
                $sql = "INSERT INTO users (name, email, password, phone, role) VALUES ('$name', '$email', '$hashed_password', $phone_value, '$role')";
            } else {
                // Phone column doesn't exist, skip it
                $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$hashed_password', '$role')";
            }
            
            if ($conn->query($sql) === TRUE) {
                // Close connection before redirect
                $conn->close();
                // Redirect immediately to login page
                header("Location: login.php");
                exit();
            } else {
                $error = 'Registration failed. Please try again. Error: ' . $conn->error;
            }
        }
        
        // Only close connection if not already closed
        if (isset($conn) && $conn->ping()) {
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Bhosale Opticians</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Very faint placeholder text */
        input[type="tel"]::placeholder {
            color: #d0d0d0;
            opacity: 0.6;
        }
    </style>
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
                        <li class="nav-item"><a class="nav-link active" href="register.php">Register</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Register Form -->
    <section class="container my-5 py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4 fw-bold">Create Account</h2>
                        
                        <?php if($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <?php if($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control form-control-lg" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control form-control-lg" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Phone Number (Optional)</label>
                                <input type="tel" name="phone" class="form-control form-control-lg" placeholder="+91 8757883654" pattern="[0-9]{10}">
                                <small class="text-muted">Enter 10-digit mobile number</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control form-control-lg" minlength="6" maxlength="6" required>
                                <small class="text-muted">Must be exactly 6 characters</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control form-control-lg" minlength="6" maxlength="6" required>
                            </div>
                            
                            <input type="hidden" name="role" value="user">
                            
                            <button type="submit" class="btn btn-primary w-100 py-3 mb-3">Register</button>
                        </form>
                        
                        <p class="text-center text-muted">
                            Already have an account? <a href="login.php" class="text-brown">Login</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

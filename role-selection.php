<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Login Type - Bhosale Opticians</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    
    <style>
        .role-card {
            transition: all 0.3s ease;
            cursor: pointer;
            border: 3px solid transparent;
        }
        
        .role-card:hover {
            transform: translateY(-10px);
            border-color: var(--brown);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .role-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
        }
        
        .selection-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
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
                
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="index_updated.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Role Selection -->
    <section class="selection-container">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold mb-3">Welcome to Bhosale Opticians</h1>
                <p class="lead text-muted">Please select how you want to login</p>
            </div>

            <div class="row g-4 justify-content-center">
                <!-- User Login -->
                <div class="col-md-5">
                    <div class="card role-card shadow-lg border-0 rounded-4 h-100" onclick="window.location.href='login.php?role=user'">
                        <div class="card-body text-center p-5">
                            <div class="role-icon">👤</div>
                            <h2 class="mb-3 fw-bold">User Login</h2>
                            <p class="text-muted mb-4">Login as a customer to shop products, manage orders, and update your profile</p>
                            
                            <ul class="list-unstyled text-start mb-4">
                                <li class="mb-2">✓ Browse & Shop Products</li>
                                <li class="mb-2">✓ Manage Shopping Cart</li>
                                <li class="mb-2">✓ Track Orders</li>
                                <li class="mb-2">✓ Update Profile</li>
                            </ul>
                            
                            <a href="login.php?role=user" class="btn btn-primary btn-lg w-100 py-3">
                                Login as User
                            </a>
                            
                            <p class="mt-3 text-muted small">
                                Don't have an account? <a href="register.php" class="text-brown">Register</a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Admin Login -->
                <div class="col-md-5">
                    <div class="card role-card shadow-lg border-0 rounded-4 h-100" onclick="window.location.href='login.php?role=admin'">
                        <div class="card-body text-center p-5">
                            <div class="role-icon">👨‍💼</div>
                            <h2 class="mb-3 fw-bold">Admin Login</h2>
                            <p class="text-muted mb-4">Login as an administrator to manage products, orders, and users</p>
                            
                            <ul class="list-unstyled text-start mb-4">
                                <li class="mb-2">✓ Manage Products (CRUD)</li>
                                <li class="mb-2">✓ Manage Orders</li>
                                <li class="mb-2">✓ Manage Users</li>
                                <li class="mb-2">✓ View Statistics</li>
                            </ul>
                            
                            <a href="login.php?role=admin" class="btn btn-danger btn-lg w-100 py-3">
                                Login as Admin
                            </a>
                            
                            <p class="mt-3 text-muted small">
                                Admin access only
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

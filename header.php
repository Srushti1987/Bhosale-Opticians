<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Bhosale Opticians - Premium Eyewear'; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo isset($base_url) ? $base_url : ''; ?>style.css">
</head>
<body>

    <!-- Header -->
    <header class="sticky-top bg-white shadow-sm">
        <nav class="navbar navbar-expand-lg container">
            <div class="container-fluid px-3">
                <a class="navbar-brand fw-bold fs-4" href="<?php echo isset($base_url) ? $base_url : ''; ?>index_updated.php">
                    Bhosale Opticians
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($active_page) && $active_page == 'home') ? 'active' : ''; ?>" href="<?php echo isset($base_url) ? $base_url : ''; ?>index_updated.php">
                                <i class="bi bi-house-door me-1"></i>Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($active_page) && $active_page == 'products') ? 'active' : ''; ?>" href="<?php echo isset($base_url) ? $base_url : ''; ?>products.php">
                                <i class="bi bi-grid me-1"></i>Products
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($active_page) && $active_page == 'appointment') ? 'active' : ''; ?>" href="<?php echo isset($base_url) ? $base_url : ''; ?>book-appointment.php">
                                <i class="bi bi-calendar-check me-1"></i>Book Appointment
                            </a>
                        </li>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-list-ul me-1"></i>Categories
                            </a>
                            <ul class="dropdown-menu border-0 shadow-sm">
                                <li><a class="dropdown-item" href="<?php echo isset($base_url) ? $base_url : ''; ?>products.php?gender=Men"><i class="bi bi-person me-2"></i>Men</a></li>
                                <li><a class="dropdown-item" href="<?php echo isset($base_url) ? $base_url : ''; ?>products.php?gender=Women"><i class="bi bi-person-dress me-2"></i>Women</a></li>
                                <li><a class="dropdown-item" href="<?php echo isset($base_url) ? $base_url : ''; ?>products.php?gender=Kids"><i class="bi bi-emoji-smile me-2"></i>Kids</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?php echo isset($base_url) ? $base_url : ''; ?>products.php?sale=1"><i class="bi bi-tag-fill me-2"></i>Sale Items</a></li>
                            </ul>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link position-relative <?php echo (isset($active_page) && $active_page == 'cart') ? 'active' : ''; ?>" href="<?php echo isset($base_url) ? $base_url : ''; ?>cart-session.php">
                                <i class="bi bi-cart3 me-1"></i>Cart
                                <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo count($_SESSION['cart']); ?>
                                </span>
                                <?php endif; ?>
                            </a>
                        </li>
                        
                        <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($_SESSION['user_name']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                                <?php if($_SESSION['user_role'] == 'admin'): ?>
                                <li><a class="dropdown-item" href="<?php echo isset($base_url) ? $base_url : ''; ?>admin/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                                <?php else: ?>
                                <li><a class="dropdown-item" href="<?php echo isset($base_url) ? $base_url : ''; ?>user/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?php echo isset($base_url) ? $base_url : ''; ?>user/profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?php echo isset($base_url) ? $base_url : ''; ?>logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                        <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo isset($base_url) ? $base_url : ''; ?>role-selection.php">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Login
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <!-- Search Icon -->
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#searchModal">
                                <i class="bi bi-search"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Search Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="searchModalLabel">Search Products</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo isset($base_url) ? $base_url : ''; ?>products.php" method="GET">
                        <div class="input-group input-group-lg">
                            <input type="text" name="search" class="form-control" placeholder="Search for eyewear..." required>
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </form>
                    <div class="mt-3">
                        <small class="text-muted">Popular searches:</small>
                        <div class="mt-2">
                            <a href="<?php echo isset($base_url) ? $base_url : ''; ?>products.php?gender=Men" class="badge bg-light text-dark me-2 mb-2">Men's Eyewear</a>
                            <a href="<?php echo isset($base_url) ? $base_url : ''; ?>products.php?gender=Women" class="badge bg-light text-dark me-2 mb-2">Women's Eyewear</a>
                            <a href="<?php echo isset($base_url) ? $base_url : ''; ?>products.php?sale=1" class="badge bg-danger text-white me-2 mb-2">Sale</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

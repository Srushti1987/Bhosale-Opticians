<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - SUNRAY</title>
    
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
                    <span class="text-brown">SUN</span>RAY
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                        <li class="nav-item"><a class="nav-link active" href="cart.php">Cart <span class="badge bg-brown">0</span></a></li>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Page Header -->
    <section class="py-5 bg-light">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">Shopping Cart</h1>
            <p class="lead text-muted">Review your items</p>
        </div>
    </section>

    <!-- Cart Items -->
    <section class="container my-5">
        <div class="row">
            <div class="col-lg-8">
                <div id="cart-items">
                    <!-- Cart items will be loaded here by JavaScript -->
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="mb-4">Order Summary</h4>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span>Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Total:</strong>
                            <strong id="total" class="text-brown fs-4">₹0.00</strong>
                        </div>
                        <a href="checkout.php" class="btn btn-primary w-100 py-3">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-section text-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h4 class="fw-bold mb-3"><span class="text-brown">SUN</span>RAY</h4>
                    <p>Premium eyewear for every style and occasion.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
    <script>
        // Load cart items
        function loadCart() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const cartItemsDiv = document.getElementById('cart-items');
            
            if (cart.length === 0) {
                cartItemsDiv.innerHTML = `
                    <div class="text-center py-5">
                        <h4 class="text-muted">Your cart is empty</h4>
                        <a href="products.php" class="btn btn-primary mt-3">Continue Shopping</a>
                    </div>
                `;
                return;
            }
            
            let html = '';
            let subtotal = 0;
            
            cart.forEach((item, index) => {
                const price = parseFloat(item.price.replace('$', ''));
                subtotal += price;
                
                html += `
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <img src="${item.image}" class="img-fluid rounded" alt="${item.name}">
                                </div>
                                <div class="col-md-4">
                                    <h5>${item.name}</h5>
                                </div>
                                <div class="col-md-3">
                                    <h5 class="text-brown">${item.price}</h5>
                                </div>
                                <div class="col-md-3 text-end">
                                    <button class="btn btn-danger" onclick="removeFromCart(${index})">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            cartItemsDiv.innerHTML = html;
            document.getElementById('subtotal').textContent = '₹' + subtotal.toFixed(2);
            document.getElementById('total').textContent = '₹' + subtotal.toFixed(2);
        }
        
        function removeFromCart(index) {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            cart.splice(index, 1);
            localStorage.setItem('cart', JSON.stringify(cart));
            loadCart();
            updateCartCount();
        }
        
        // Load cart on page load
        document.addEventListener('DOMContentLoaded', loadCart);
    </script>
</body>
</html>

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

    <header class="sticky-top bg-white shadow-sm">
        <nav class="navbar navbar-expand-lg container">
            <div class="container-fluid px-3">
                <a class="navbar-brand fw-bold fs-4" href="index_updated.php">
                    <span class="text-brown">SUN</span>RAY
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="index_updated.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                        <li class="nav-item"><a class="nav-link active" href="cart_fixed.php">Cart <span class="badge bg-brown" id="cartCount">0</span></a></li>
                        <li class="nav-item"><a class="nav-link" href="role-selection.php">Login</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <section class="py-5 bg-light">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">Shopping Cart</h1>
            <p class="lead text-muted">Review your items</p>
        </div>
    </section>

    <section class="container my-5">
        <div class="row">
            <div class="col-lg-8">
                <div id="cart-items"></div>
            </div>
            
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-body">
                        <h4 class="mb-4">Order Summary</h4>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal">₹0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span class="text-success">Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Total:</strong>
                            <strong id="total" class="text-brown fs-4">₹0.00</strong>
                        </div>
                        <a href="checkout.php" class="btn btn-primary w-100 py-3" id="checkoutBtn">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            document.getElementById('cartCount').textContent = cart.length;
        }

        function loadCart() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const cartItemsDiv = document.getElementById('cart-items');
            
            if (cart.length === 0) {
                cartItemsDiv.innerHTML = `
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5">
                            <h4 class="text-muted mb-4">Your cart is empty</h4>
                            <a href="products.php" class="btn btn-primary">Continue Shopping</a>
                        </div>
                    </div>
                `;
                document.getElementById('subtotal').textContent = '₹0.00';
                document.getElementById('total').textContent = '₹0.00';
                document.getElementById('checkoutBtn').classList.add('disabled');
                return;
            }
            
            let html = '';
            let subtotal = 0;
            
            cart.forEach((item, index) => {
                // Extract numeric price
                let priceValue = 0;
                if (typeof item.price === 'string') {
                    priceValue = parseFloat(item.price.replace(/[₹,]/g, '').trim());
                } else {
                    priceValue = parseFloat(item.price);
                }
                
                if (!isNaN(priceValue)) {
                    subtotal += priceValue;
                }
                
                html += `
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-2 mb-3 mb-md-0">
                                    <img src="${item.image || 'https://via.placeholder.com/150'}" 
                                         class="img-fluid rounded" 
                                         alt="${item.name}"
                                         style="max-height: 100px; object-fit: cover;">
                                </div>
                                <div class="col-md-5 mb-3 mb-md-0">
                                    <h5 class="mb-1">${item.name}</h5>
                                    <p class="text-muted small mb-0">Product ID: ${item.id || 'N/A'}</p>
                                </div>
                                <div class="col-md-3 mb-3 mb-md-0">
                                    <h5 class="text-brown mb-0">₹${priceValue.toFixed(2)}</h5>
                                </div>
                                <div class="col-md-2 text-end">
                                    <button class="btn btn-danger btn-sm" onclick="removeFromCart(${index})">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            cartItemsDiv.innerHTML = html;
            document.getElementById('subtotal').textContent = '₹' + subtotal.toFixed(2);
            document.getElementById('total').textContent = '₹' + subtotal.toFixed(2);
            document.getElementById('checkoutBtn').classList.remove('disabled');
        }
        
        function removeFromCart(index) {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            cart.splice(index, 1);
            localStorage.setItem('cart', JSON.stringify(cart));
            loadCart();
            updateCartCount();
        }
        
        // Load cart on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadCart();
            updateCartCount();
        });
    </script>
</body>
</html>

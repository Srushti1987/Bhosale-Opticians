// Show quantity selector when Add to Cart is clicked
function showQtySelector(button) {
    // First, reset all other product forms to initial state
    document.querySelectorAll('.product-form').forEach(form => {
        const otherBtn = form.querySelector('.add-to-cart-btn');
        const otherWrapper = form.querySelector('.qty-selector-wrapper');
        if (otherBtn && otherWrapper && form !== button.closest('.product-form')) {
            otherBtn.style.display = 'block';
            otherWrapper.style.display = 'none';
            // Reset quantity to 1
            const qtyDisplay = form.querySelector('.qty-display');
            const qtyValue = form.querySelector('.qty-value');
            if (qtyDisplay) qtyDisplay.value = '1';
            if (qtyValue) qtyValue.value = '1';
        }
    });
    
    // Now show the quantity selector for the clicked product
    const form = button.closest('.product-form');
    const qtyWrapper = form.querySelector('.qty-selector-wrapper');
    
    // Hide the Add to Cart button
    button.style.display = 'none';
    
    // Show the quantity selector
    qtyWrapper.style.display = 'block';
}

// Validate form before submission
function validateForm(form) {
    console.log('Form submitting...');
    console.log('Quantity:', form.querySelector('.qty-value').value);
    return true; // Allow form submission
}

// Quantity selector functions
function increaseQty(button) {
    const form = button.closest('.product-form');
    const display = form.querySelector('.qty-display');
    const hiddenInput = form.querySelector('.qty-value');
    const maxStock = parseInt(form.querySelector('input[name="max_stock"]').value);
    
    let value = parseInt(display.value);
    
    if (value < maxStock) {
        value++;
        display.value = value;
        hiddenInput.value = value;
    }
}

function decreaseQty(button) {
    const form = button.closest('.product-form');
    const display = form.querySelector('.qty-display');
    const hiddenInput = form.querySelector('.qty-value');
    
    let value = parseInt(display.value);
    
    if (value > 1) {
        value--;
        display.value = value;
        hiddenInput.value = value;
    }
}

// Show quantity selector when Add to Cart is clicked
function showQuantity(button) {
    // Hide all other quantity selectors first
    document.querySelectorAll('.quantity-section').forEach(section => {
        section.style.display = 'none';
    });
    document.querySelectorAll('.add-cart-btn').forEach(btn => {
        btn.style.display = 'block';
    });
    
    // Show this product's quantity selector
    const form = button.closest('.product-form');
    button.style.display = 'none';
    form.querySelector('.quantity-section').style.display = 'block';
}

// Increase quantity
function increaseQuantity(button) {
    const form = button.closest('.product-form');
    const display = form.querySelector('.qty-display');
    const hiddenInput = form.querySelector('.qty-value');
    const maxStock = parseInt(form.querySelector('input[name="max_stock"]').value);
    
    let value = parseInt(display.value);
    if (value < maxStock) {
        value++;
        display.value = value;
        hiddenInput.value = value;
    }
}

// Decrease quantity
function decreaseQuantity(button) {
    const form = button.closest('.product-form');
    const display = form.querySelector('.qty-display');
    const hiddenInput = form.querySelector('.qty-value');
    
    let value = parseInt(display.value);
    if (value > 1) {
        value--;
        display.value = value;
        hiddenInput.value = value;
    }
}

// Cart functionality
let cart = JSON.parse(localStorage.getItem('cart')) || [];

// Update cart count
function updateCartCount() {
    const cartBadge = document.querySelector('.badge.bg-brown');
    if (cartBadge) {
        cartBadge.textContent = cart.length;
    }
}

// Add to cart
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
    
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productCard = this.closest('.product-card');
            const productId = this.getAttribute('data-id');
            const productName = productCard.querySelector('.product-title').textContent;
            const productPriceElement = productCard.querySelector('.product-price .text-brown');
            const productPrice = productPriceElement.textContent.trim();
            const productImage = productCard.querySelector('.product-image img').src;
            
            const product = {
                id: productId,
                name: productName,
                price: productPrice,
                image: productImage,
                quantity: 1
            };
            
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            // Check if product already exists
            const existingIndex = cart.findIndex(item => item.id === productId);
            if (existingIndex > -1) {
                cart[existingIndex].quantity += 1;
            } else {
                cart.push(product);
            }
            
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
            
            // Show success message
            showNotification('Product added to cart!');
            
            // Button animation
            this.innerHTML = '✓ Added';
            this.classList.add('btn-success');
            this.classList.remove('btn-primary');
            
            setTimeout(() => {
                this.innerHTML = 'Add to Cart';
                this.classList.remove('btn-success');
                this.classList.add('btn-primary');
            }, 2000);
        });
    });
});

// Show notification
function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3';
    notification.style.zIndex = '9999';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});

// Navbar scroll effect
window.addEventListener('scroll', function() {
    const header = document.querySelector('header');
    if (window.scrollY > 50) {
        header.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
    } else {
        header.style.boxShadow = '0 1px 3px rgba(0,0,0,0.05)';
    }
});

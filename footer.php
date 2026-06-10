    <!-- Footer -->
    <footer class="footer-section py-5 mt-5" style="background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%); color: blue;">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <h4 class="fw-bold mb-3" style="color: #ffffff;">Bhosale Opticians</h4>
                    <p style="color: #ffffff;">Premium eyewear for every style and occasion. Quality you can see, comfort you can feel.</p>
                    <div class="mt-3">
        
                        
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="mb-3" style="color: #blue;">Shop</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="<?php echo isset($base_url) ? $base_url : ''; ?>products.php" style="color: #ffffff;" class="text-decoration-none">
                                <i class="bi bi-chevron-right"></i> All Products
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo isset($base_url) ? $base_url : ''; ?>products.php?gender=Men" style="color: #ffffff;" class="text-decoration-none">
                                <i class="bi bi-chevron-right"></i> Men's Eyewear
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo isset($base_url) ? $base_url : ''; ?>products.php?gender=Women" style="color: #ffffff;" class="text-decoration-none">
                                <i class="bi bi-chevron-right"></i> Women's Eyewear
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo isset($base_url) ? $base_url : ''; ?>products.php?gender=Kids" style="color: #ffffff;" class="text-decoration-none">
                                <i class="bi bi-chevron-right"></i> Kids Eyewear
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo isset($base_url) ? $base_url : ''; ?>products.php?sale=1" style="color: #ffffff;" class="text-decoration-none">
                                <i class="bi bi-chevron-right"></i> Sale Items
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="mb-3" style="color: #ffffff;">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="#" style="color: #ffffff;" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#aboutModal">
                                <i class="bi bi-chevron-right"></i> About Us
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" style="color: #ffffff;" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#contactModal">
                                <i class="bi bi-chevron-right"></i> Contact Us
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" style="color: #ffffff;" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#shippingModal">
                                <i class="bi bi-chevron-right"></i> Shipping Info
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" style="color: #ffffff;" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#returnsModal">
                                <i class="bi bi-chevron-right"></i> Returns & Exchange
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" style="color: #ffffff;" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                <i class="bi bi-chevron-right"></i> Payment Options
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="mb-3" style="color: #ffffff;">Contact Us</h5>
                    <ul class="list-unstyled" style="color: #ffffff;">
                        <li class="mb-3">
                            <i class="bi bi-geo-alt-fill me-2"></i>
                            1st Floor, Silver Springs, Hotgi Road<br>
                            <span class="ms-4">Solapur, Maharashtra 413003</span>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-envelope-fill me-2"></i>
                            <a href="mailto:bhosaleopticians@gmail.com" style="color: #ffffff;" class="text-decoration-none">
                                bhosaleopticians@gmail.com
                            </a>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-telephone-fill me-2"></i>
                            <a href="tel:+919960815363" style="color: #ffffff;" class="text-decoration-none">
                                +91 9960815363
                            </a>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-clock-fill me-2"></i>
                            Mon - Sat: 10:00 AM - 8:00 PM
                        </li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255, 255, 255, 0.3);">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-2">
                    <p class="mb-0" style="color: #ffffff;">&copy; 2026 Bhosale Opticians. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" style="color: #ffffff;" class="text-decoration-none me-3" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy Policy</a>
                    <a href="#" style="color: #ffffff;" class="text-decoration-none me-3" data-bs-toggle="modal" data-bs-target="#termsModal">Terms & Conditions</a>
                    <a href="#" style="color: #ffffff;" class="text-decoration-none" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;">
                        <i class="bi bi-arrow-up-circle"></i> Back to Top
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Modals for Policies -->
    <!-- Privacy Policy Modal -->
    <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="privacyModalLabel">Privacy Policy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Information We Collect</h6>
                    <p>We collect information you provide directly to us, such as when you create an account, make a purchase, or contact us for support.</p>
                    
                    <h6>How We Use Your Information</h6>
                    <p>We use the information we collect to process your orders, communicate with you, and improve our services.</p>
                    
                    <h6>Data Security</h6>
                    <p>We implement appropriate security measures to protect your personal information from unauthorized access, alteration, or disclosure.</p>
                    
                    <h6>Contact Us</h6>
                    <p>If you have questions about our privacy policy, please contact us at bhosaleopticians@gmail.com</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms & Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Terms & Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Use of Website</h6>
                    <p>By accessing this website, you agree to be bound by these terms and conditions.</p>
                    
                    <h6>Product Information</h6>
                    <p>We strive to provide accurate product information, but we do not warrant that product descriptions or other content is accurate, complete, or error-free.</p>
                    
                    <h6>Orders and Payment</h6>
                    <p>All orders are subject to acceptance and availability. We reserve the right to refuse or cancel any order.</p>
                    
                    <h6>Returns and Refunds</h6>
                    <p>Please refer to our Returns & Exchange policy for information about returns and refunds.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- About Us Modal -->
    <div class="modal fade" id="aboutModal" tabindex="-1" aria-labelledby="aboutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="aboutModalLabel">About Bhosale Opticians</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Welcome to Bhosale Opticians</h6>
                    <p>At Bhosale Opticians, we believe that eyewear is more than just a necessity—it's a fashion statement, a reflection of your personality, and an essential part of your daily life.</p>
                    
                    <h6>Our Mission</h6>
                    <p>We are committed to providing premium quality eyewear that combines style, comfort, and affordability. Our extensive collection features the latest trends in eyeglasses and sunglasses for men, women, and kids.</p>
                    
                    <h6>Why Choose Us?</h6>
                    <ul>
                        <li>Wide range of stylish and trendy eyewear</li>
                        <li>Premium quality products at competitive prices</li>
                        <li>Expert guidance and personalized service</li>
                        <li>Easy returns and exchange policy</li>
                        <li>Secure online shopping experience</li>
                    </ul>
                    
                    <h6>Visit Our Store</h6>
                    <p>Located at 1st Floor, Silver Springs, Hotgi Road, Solapur, Maharashtra 413003</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Us Modal -->
    <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactModalLabel">Contact Us</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 class="mb-3">Get in Touch</h6>
                    
                    <div class="mb-3">
                        <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                        <strong>Address:</strong><br>
                        <span class="ms-4">1st Floor, Silver Springs, Hotgi Road<br>Solapur, Maharashtra 413003</span>
                    </div>
                    
                    <div class="mb-3">
                        <i class="bi bi-envelope-fill text-primary me-2"></i>
                        <strong>Email:</strong><br>
                        <span class="ms-4"><a href="mailto:bhosaleopticians@gmail.com">bhosaleopticians@gmail.com</a></span>
                    </div>
                    
                    <div class="mb-3">
                        <i class="bi bi-telephone-fill text-primary me-2"></i>
                        <strong>Phone:</strong><br>
                        <span class="ms-4"><a href="tel:+919960815363">+91 9960815363</a></span>
                    </div>
                    
                    <div class="mb-3">
                        <i class="bi bi-clock-fill text-primary me-2"></i>
                        <strong>Business Hours:</strong><br>
                        <span class="ms-4">Monday - Saturday: 10:00 AM - 8:00 PM<br>Sunday: Closed</span>
                    </div>
                    
                    <hr>
                    
                    <h6 class="mb-3">Follow Us</h6>
                    <div class="text-center">
                        <a href="https://facebook.com" target="_blank" class="btn btn-outline-primary btn-sm me-2">
                            <i class="bi bi-facebook"></i> Facebook
                        </a>
                        <a href="https://instagram.com" target="_blank" class="btn btn-outline-danger btn-sm me-2">
                            <i class="bi bi-instagram"></i> Instagram
                        </a>
                        <a href="https://wa.me/919960815363" target="_blank" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-whatsapp"></i> WhatsApp
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo isset($base_url) ? $base_url : ''; ?>script.js"></script>
</body>
</html>

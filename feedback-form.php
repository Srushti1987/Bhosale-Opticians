<?php
session_start();
require_once 'config.php';

// Set page variables
$page_title = "Share Your Feedback - Bhosale Opticians";
$active_page = "feedback";
$base_url = "";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?role=user');
    exit();
}

// Get order ID if provided
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : null;
$order_details = null;

if ($order_id) {
    // Fetch order details to show context
    $conn = getDBConnection();
    
    // Check if feedback table exists
    $feedback_table_exists = false;
    try {
        $conn->query("SELECT 1 FROM feedback LIMIT 1");
        $feedback_table_exists = true;
    } catch (Exception $e) {
        $feedback_table_exists = false;
    }
    
    if (!$feedback_table_exists) {
        // Create the feedback table automatically
        $create_sql = "CREATE TABLE IF NOT EXISTS feedback (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            order_id INT DEFAULT NULL,
            rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
            feedback_text TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (order_id) REFERENCES orders(id)
        )";
        $conn->query($create_sql);
    }
    
    $sql = "SELECT o.*, COUNT(oi.id) as item_count 
            FROM orders o 
            LEFT JOIN order_items oi ON o.id = oi.order_id 
            WHERE o.id = $order_id AND o.user_id = {$_SESSION['user_id']} 
            GROUP BY o.id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $order_details = $result->fetch_assoc();
    }
    $conn->close();
}

$success = '';
$error = '';

// Process feedback submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = intval($_POST['rating']);
    $feedback_text = trim($_POST['feedback_text']);
    $order_id_post = isset($_POST['order_id']) ? intval($_POST['order_id']) : null;
    
    if ($rating >= 1 && $rating <= 5 && !empty($feedback_text)) {
        $conn = getDBConnection();
        $user_id = $_SESSION['user_id'];
        $feedback_text_escaped = $conn->real_escape_string($feedback_text);
        
        // Check if feedback already exists for this order
        if ($order_id_post) {
            $check_sql = "SELECT id FROM feedback WHERE user_id = $user_id AND order_id = $order_id_post";
            $check_result = $conn->query($check_sql);
            if ($check_result->num_rows > 0) {
                $error = 'You have already submitted feedback for this order.';
            }
        }
        
        if (!$error) {
            $conn = getDBConnection();
            
            // Ensure feedback table exists
            $create_sql = "CREATE TABLE IF NOT EXISTS feedback (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                order_id INT DEFAULT NULL,
                rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
                feedback_text TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (order_id) REFERENCES orders(id)
            )";
            $conn->query($create_sql);
            
            $order_part = $order_id_post ? $order_id_post : 'NULL';
            $sql = "INSERT INTO feedback (user_id, order_id, rating, feedback_text) 
                    VALUES ($user_id, $order_part, $rating, '$feedback_text_escaped')";
            
            if ($conn->query($sql)) {
                $success = 'Thank you for your feedback! Your review has been submitted successfully.';
                // Redirect to bill page after successful feedback
                if ($order_id_post) {
                    header("Location: bill.php?order_id=$order_id_post&feedback=success");
                    exit();
                }
            } else {
                $error = 'Failed to submit feedback. Please try again.';
            }
            $conn->close();
        }
    } else {
        $error = 'Please provide both rating and feedback text.';
    }
}

// Include header
include 'includes/header.php';
?>

    <section class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if ($order_details): ?>
                <div class="text-center mb-5">
                    <h2 class="display-5 fw-bold mb-3 text-success">🎉 Order Placed Successfully!</h2>
                    <p class="lead text-muted">Thank you for your purchase! We'd love to hear about your shopping experience.</p>
                    
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h5 class="text-primary">Order Summary</h5>
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <strong>Order #<?= str_pad($order_details['id'], 6, '0', STR_PAD_LEFT) ?></strong>
                                </div>
                                <div class="col-md-4">
                                    <strong><?= $order_details['item_count'] ?> Item(s)</strong>
                                </div>
                                <div class="col-md-4">
                                    <strong>₹<?= number_format($order_details['total_amount'], 2) ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="text-center mb-5">
                    <h2 class="display-5 fw-bold mb-3">Share Your Experience</h2>
                    <p class="lead text-muted">Your feedback helps us improve our service and helps other customers make better choices.</p>
                </div>
                <?php endif; ?>

                <?php if($success): ?>
                <div class="alert alert-success">
                    <h5>✅ Thank You!</h5>
                    <?= $success ?>
                </div>
                <?php endif; ?>

                <?php if($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <form method="POST">
                            <?php if ($order_id): ?>
                            <input type="hidden" name="order_id" value="<?= $order_id ?>">
                            <?php endif; ?>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Rate Your Overall Experience</label>
                                <div class="rating-stars mb-2">
                                    <input type="radio" name="rating" value="5" id="star5" required>
                                    <label for="star5" class="star">⭐</label>
                                    <input type="radio" name="rating" value="4" id="star4">
                                    <label for="star4" class="star">⭐</label>
                                    <input type="radio" name="rating" value="3" id="star3">
                                    <label for="star3" class="star">⭐</label>
                                    <input type="radio" name="rating" value="2" id="star2">
                                    <label for="star2" class="star">⭐</label>
                                    <input type="radio" name="rating" value="1" id="star1">
                                    <label for="star1" class="star">⭐</label>
                                </div>
                                <small class="text-muted">Click on the stars to rate (5 = Excellent, 1 = Poor)</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Your Feedback</label>
                                <textarea name="feedback_text" class="form-control" rows="5" 
                                          placeholder="Tell us about your experience with our products, service, website, or any suggestions for improvement..." 
                                          required></textarea>
                            </div>

                            <div class="mb-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>What you can share:</strong>
                                        <ul class="list-unstyled mt-2 text-muted small">
                                            <li>✓ Product quality</li>
                                            <li>✓ Customer service</li>
                                            <li>✓ Website experience</li>
                                            <li>✓ Checkout process</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Your feedback helps us:</strong>
                                        <ul class="list-unstyled mt-2 text-muted small">
                                            <li>✓ Improve our products</li>
                                            <li>✓ Enhance customer service</li>
                                            <li>✓ Better website experience</li>
                                            <li>✓ Help other customers</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5 me-3">
                                    Submit Feedback
                                </button>
                                <?php if ($order_id): ?>
                                <a href="bill.php?order_id=<?= $order_id ?>" class="btn btn-outline-secondary btn-lg px-5">
                                    Skip & View Invoice
                                </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Recent Reviews Section -->
                <?php if (!$order_id): ?>
                <div class="mt-5">
                    <h4 class="mb-4">What Our Customers Say</h4>
                    <?php
                    $conn = getDBConnection();
                    
                    // Check if feedback table exists before querying
                    $has_feedback = false;
                    try {
                        $recent_sql = "SELECT f.rating, f.feedback_text, f.created_at, u.name 
                                       FROM feedback f 
                                       JOIN users u ON f.user_id = u.id 
                                       ORDER BY f.created_at DESC 
                                       LIMIT 3";
                        $recent_result = $conn->query($recent_sql);
                        $has_feedback = true;
                    } catch (Exception $e) {
                        $has_feedback = false;
                    }
                    
                    if ($has_feedback && $recent_result->num_rows > 0):
                        while($review = $recent_result->fetch_assoc()):
                    ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1"><?= htmlspecialchars($review['name']) ?></h6>
                                    <div class="text-warning">
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <?= $i <= $review['rating'] ? '⭐' : '☆' ?>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <small class="text-muted"><?= date('M d, Y', strtotime($review['created_at'])) ?></small>
                            </div>
                            <p class="mb-0 text-muted"><?= nl2br(htmlspecialchars($review['feedback_text'])) ?></p>
                        </div>
                    </div>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <p class="text-muted text-center py-4">Be the first to share your feedback!</p>
                    <?php endif; ?>
                    <?php $conn->close(); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

<?php
// Include footer
include 'includes/footer.php';
?>
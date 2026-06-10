<?php
require_once 'config.php';

// Auto-setup mode - create table automatically
$auto_setup = isset($_GET['auto']) && $_GET['auto'] == '1';

if ($auto_setup) {
    $conn = getDBConnection();
    
    // Create feedback table
    $sql = "CREATE TABLE IF NOT EXISTS feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        order_id INT DEFAULT NULL,
        rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
        feedback_text TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (order_id) REFERENCES orders(id)
    )";
    
    if ($conn->query($sql) === TRUE) {
        $conn->close();
        // Redirect back to admin feedback page
        header('Location: admin/feedback.php?setup=success');
        exit();
    } else {
        echo "Error: " . $conn->error;
        $conn->close();
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Feedback System - Bhosale Opticians</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 class="display-5 fw-bold mb-3">🔧 Feedback System Setup</h2>
                    <p class="lead text-muted">Setting up the feedback system for the first time</p>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body p-5 text-center">
                        <div class="mb-4">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                 style="width: 80px; height: 80px; font-size: 2rem;">
                                📝
                            </div>
                            <h4>Create Feedback Database Table</h4>
                            <p class="text-muted">This will create the necessary database table to store customer feedback and reviews.</p>
                        </div>

                        <div class="mb-4">
                            <h6 class="text-primary">What will be created:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>✓ Feedback table with proper structure</li>
                                <li>✓ User and order relationships</li>
                                <li>✓ Rating and review storage</li>
                                <li>✓ Timestamp tracking</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="setup-feedback-table.php?auto=1" class="btn btn-primary btn-lg px-5">
                                🚀 Setup Feedback System
                            </a>
                            <a href="admin/dashboard.php" class="btn btn-outline-secondary btn-lg px-5">
                                ← Back to Dashboard
                            </a>
                        </div>

                        <div class="mt-4">
                            <small class="text-muted">
                                This is a one-time setup. Once completed, the feedback system will be fully functional.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="text-primary">Manual Setup (Alternative)</h6>
                            <p class="small text-muted mb-2">If you prefer to set up manually, you can:</p>
                            <ol class="small text-muted">
                                <li>Go to phpMyAdmin</li>
                                <li>Select your database (sunray_db)</li>
                                <li>Run the SQL from <code>add_feedback_table.sql</code></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
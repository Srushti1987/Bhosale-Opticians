<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Validate input
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : null;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$feedback_text = isset($_POST['feedback_text']) ? trim($_POST['feedback_text']) : '';

// Validate data
if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Invalid rating']);
    exit();
}

if (empty($feedback_text)) {
    echo json_encode(['success' => false, 'message' => 'Feedback text is required']);
    exit();
}

try {
    $conn = getDBConnection();
    
    // Check if feedback already exists for this order
    if ($order_id) {
        $check_sql = "SELECT id FROM feedback WHERE user_id = $user_id AND order_id = $order_id";
        $check_result = $conn->query($check_sql);
        if ($check_result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Feedback already submitted for this order']);
            exit();
        }
    }
    
    // Insert feedback
    $feedback_text_escaped = $conn->real_escape_string($feedback_text);
    $order_part = $order_id ? ", $order_id" : ", NULL";
    
    $sql = "INSERT INTO feedback (user_id, order_id, rating, feedback_text) 
            VALUES ($user_id$order_part, $rating, '$feedback_text_escaped')";
    
    if ($conn->query($sql)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Feedback submitted successfully',
            'feedback_id' => $conn->insert_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
<?php
require_once 'config.php';
$conn = getDBConnection();

$sql = "CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    appointment_type ENUM('Eye Check-up', 'Frame Selection', 'Lens Consultation', 'Contact Lens Trial', 'Follow-up') NOT NULL DEFAULT 'Eye Check-up',
    patient_name VARCHAR(100) NOT NULL,
    patient_phone VARCHAR(15) NOT NULL,
    patient_age INT DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    admin_notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($sql)) {
    echo '<div style="font-family:Arial;padding:30px;background:#d4edda;color:#155724;border-radius:8px;max-width:500px;margin:50px auto;">';
    echo '<h2>✅ Appointments table created successfully!</h2>';
    echo '<p><a href="book-appointment.php">Go to Book Appointment</a></p>';
    echo '</div>';
} else {
    echo '<div style="font-family:Arial;padding:30px;background:#f8d7da;color:#721c24;border-radius:8px;max-width:500px;margin:50px auto;">';
    echo '<h2>❌ Error: ' . $conn->error . '</h2>';
    echo '</div>';
}
$conn->close();
?>

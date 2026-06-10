-- Add feedback table to database
USE sunray_db;

CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_id INT DEFAULT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    feedback_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

-- Add some sample feedback data
INSERT INTO feedback (user_id, order_id, rating, feedback_text) VALUES
(1, NULL, 5, 'Excellent service and quality products! Very satisfied with my purchase.'),
(1, NULL, 4, 'Good collection of eyewear. Fast delivery and professional packaging.');
-- Applications table for Module 7
CREATE TABLE IF NOT EXISTS applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    announcement_id INT NOT NULL,
    motivation TEXT NOT NULL,
    cv_path VARCHAR(255) DEFAULT NULL,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (announcement_id) REFERENCES annonces(id) ON DELETE CASCADE,
    UNIQUE KEY unique_application (student_id, announcement_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

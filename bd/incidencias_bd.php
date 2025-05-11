<?php

    $incidentsTable = "CREATE TABLE IF NOT EXISTS incidents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        creation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        user_id INT NOT NULL,
        username VARCHAR(255) NOT NULL,
        user_email VARCHAR(255),
        incident_type VARCHAR(100) NOT NULL,
        description TEXT NOT NULL,
        urgency ENUM('low', 'medium', 'high', 'critical') NOT NULL,
        state ENUM('open', 'processing', 'solved', 'closed') DEFAULT 'open',
        update_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        observations TEXT,
        resolution_date DATETIME,
        INDEX idx_urgency (urgency), -- Index para optimizar la ordenación por urgencia
        INDEX idx_incident_type (incident_type), -- Index para optimizar la búsqueda por tipo
        INDEX idx_state (state), -- Index para optimizar la búsqueda por estado
        INDEX idx_creation_date (creation_date), -- Index para optimizar la ordenación por fecha
        FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
    )";

?>
<?php
if ($mysqli = new mysqli("localhost", "foc", "foc")) {
// Seleccionamos BD libros para crear tablas
        $mysqli->select_db("Lingualair");

$createTable9 = "CREATE TABLE IF NOT EXISTS notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL, -- ID del usuario al que va dirigida la notificación
            type VARCHAR(255) NOT NULL, -- Tipo de notificación (follow, booking, level, achievement, etc.)
            content TEXT NOT NULL, -- Contenido de la notificación ( [user] has followed you, etc.)
            read_status BOOLEAN DEFAULT FALSE, -- Estado de la notificación (leída o no)
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Fecha de creación
            related_id INT NULL, -- ID del elemento relacionado (evento, usuario, etc.)
            FOREIGN KEY (user_id) REFERENCES user(id) -- Asumiendo que tienes una tabla de usuarios
            )";

        // Creamos la tabla notifications
        if ($mysqli->query($createTable9) === TRUE) {
            echo "<br>";
            echo "Tabla notifications creada con éxito";
        } else {
            echo "<br>";
            echo "Error creando la tabla notifications: " . $mysqli->error; // Importante mostrar el error
        }
    }
?>
<?php
// Creamos la conexión
if ($mysqli = new mysqli("localhost", "foc", "foc")) {
    //#######################################
    //####### CREACIÓN BD LINGUALAIR ########
    //#######################################

    // Creamos la base de datos Lingualair si no existe
    $createDB = "CREATE DATABASE IF NOT EXISTS `Lingualair` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

    if ($mysqli->query($createDB) === TRUE) {
        echo "<br>";
        echo "BD creada con éxito";

        // Seleccionamos BD libros para crear tablas
        $mysqli->select_db("Lingualair");

        //-------------ENTIDADES----------------

        //#######################################
        //########## ROLES Y PERMISOS ###########
        //#######################################

        $createTable9 = "CREATE TABLE IF NOT EXISTS roles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) UNIQUE NOT NULL, 
            description VARCHAR(255))";

            // name -- 'standard', 'admin', 'premium'

        // Creamos la tabla roles
        if ($mysqli->query($createTable9) === TRUE) {
            echo "<br>";
            echo "Tabla roles creada con éxito";

            $sql14 = "INSERT INTO roles (name, description) VALUES
                ('standard', 'Usuario base con permisos limitados.'),
                ('admin', 'Usuario con permisos administrativos completos.'),
                ('premium', 'Usuario con características y permisos extendidos.')";

            if ($mysqli->query($sql14) === TRUE) {
                echo "<br>";
                echo "Inserción para tabla roles realizada con éxito";
            } else {
                echo "<br>";
                echo "Error insertando datos para tabla roles: " . $mysqli->error; // Importante mostrar el error
            }
        } else {
            echo "<br>";
            echo "Error creando la tabla roles: " . $mysqli->error; // Importante mostrar el error
        }

        $createTable10 = "CREATE TABLE IF NOT EXISTS permissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) UNIQUE NOT NULL, 
            description VARCHAR(255)
            )";

            // name -- 'delete_own_log', 'edit_own_log', 'delete_any_log', 'edit_any_log', 'delete_user', 'ban_user'

        // Creamos la tabla permissions
        if ($mysqli->query($createTable10) === TRUE) {
            echo "<br>";
            echo "Tabla permissions creada con éxito";

            $sql15 = "INSERT INTO permissions (name, description) VALUES
                ('delete_own_log', 'Permite eliminar logs creados por el propio usuario.'),
                ('edit_own_log', 'Permite editar logs creados por el propio usuario.'),
                ('delete_any_log', 'Permite eliminar cualquier log.'),
                ('edit_any_log', 'Permite editar cualquier log.'),
                ('delete_user', 'Permite eliminar usuarios.'),
                ('ban_user', 'Permite banear usuarios.')";

            if ($mysqli->query($sql15) === TRUE) {
                echo "<br>";
                echo "Inserción para tabla permissions realizada con éxito";
            } else {
                echo "<br>";
                echo "Error insertando datos para tabla permissions: " . $mysqli->error; // Importante mostrar el error
            }
        } else {
            echo "<br>";
            echo "Error creando la tabla permissions: " . $mysqli->error; // Importante mostrar el error
        }

        $createTable11 = "CREATE TABLE IF NOT EXISTS role_permissions (
            role_id INT NOT NULL,
            permission_id INT NOT NULL,
            PRIMARY KEY (role_id, permission_id),
            FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
            FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
            )";

            // name -- 'delete_own_log', 'edit_own_log', 'delete_any_log', 'edit_any_log', 'delete_user', 'ban_user'

        // Creamos la tabla role_permissions
        if ($mysqli->query($createTable11) === TRUE) {
            echo "<br>";
            echo "Tabla role_permissions creada con éxito";

            $sql16 = "INSERT INTO role_permissions (role_id, permission_id) VALUES
                ((SELECT id FROM roles WHERE name = 'standard'), (SELECT id FROM permissions WHERE name = 'delete_own_log')),
                ((SELECT id FROM roles WHERE name = 'premium'), (SELECT id FROM permissions WHERE name = 'delete_own_log')),
                ((SELECT id FROM roles WHERE name = 'premium'), (SELECT id FROM permissions WHERE name = 'edit_own_log')),
                ((SELECT id FROM roles WHERE name = 'admin'), (SELECT id FROM permissions WHERE name = 'delete_own_log')),
                ((SELECT id FROM roles WHERE name = 'admin'), (SELECT id FROM permissions WHERE name = 'edit_own_log')),
                ((SELECT id FROM roles WHERE name = 'admin'), (SELECT id FROM permissions WHERE name = 'delete_any_log')),
                ((SELECT id FROM roles WHERE name = 'admin'), (SELECT id FROM permissions WHERE name = 'edit_any_log')),
                ((SELECT id FROM roles WHERE name = 'admin'), (SELECT id FROM permissions WHERE name = 'delete_user')),
                ((SELECT id FROM roles WHERE name = 'admin'), (SELECT id FROM permissions WHERE name = 'ban_user'))";

            if ($mysqli->query($sql16) === TRUE) {
                echo "<br>";
                echo "Inserción para tabla role_permissions realizada con éxito";
            } else {
                echo "<br>";
                echo "Error insertando datos para tabla role_permissions: " . $mysqli->error; // Importante mostrar el error
            }
        } else {
            echo "<br>";
            echo "Error creando la tabla role_permissions: " . $mysqli->error; // Importante mostrar el error
        }

        //#######################################
        //########## 1. TABLA USER ##############
        //#######################################

        // Definimos la tabla User
        $createTable1 = "CREATE TABLE IF NOT EXISTS user (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            nickname VARCHAR(50), -- Se puede dejar vacío, porque al crear el usuario si no añades nickname, 
                                    -- te pone el username que tengas y luego puedes editarlo
            password VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            country VARCHAR(50) NOT NULL,
            role_id INT DEFAULT 1,
            banned BOOLEAN DEFAULT FALSE,
            FOREIGN KEY (role_id) REFERENCES roles(id))";
            
        // Creamos la tabla user
        if ($mysqli->query($createTable1) === TRUE) {
            echo "<br>";
            echo "Tabla user creada con éxito";

            //Insertamos datos
            $sql1 = "INSERT INTO user (id, username, nickname, password,  email, country, role_id) VALUES ('1','chieloveslangs','~Chie~', 'contrasenia','hola@gmail', 'Spain', '2')";
            $sql2 = "INSERT INTO user (id, username, password,  email, country) VALUES ('2','Sauron','bu','hola1@gmail', 'Mordor')";
            $sql3 = "INSERT INTO user (id, username, password,  email, country) VALUES ('3','Kakashi','bu','hola2@gmail', 'Konoha')";
            
            if ($mysqli->query($sql1) && $mysqli->query($sql2) && $mysqli->query($sql3)) {
                echo "<br>";
                echo "Inserción para tabla user realizada con éxito";
            }
            else echo "Error insertando datos para tabla user";
        } 
        else echo "Error al intentar crear tabla user";

        //#######################################
        //####### 2. TABLA ACHIEVEMENTS #########
        //#######################################

        // Definimos la tabla achievements
        $createTable2 = "CREATE TABLE IF NOT EXISTS achievements (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) UNIQUE NOT NULL,
            description TEXT NOT NULL)";

        // Creamos la tabla achievements
        if ($mysqli->query($createTable2) === TRUE) {
            echo "<br>";
            echo "Tabla achievements creada con éxito";

            //Insertamos datos
            $sql1 = "INSERT INTO achievements (id, name, description) VALUES ('1','Consistency is key!','Share your logs for 30 days in a row')";
            $sql2 = "INSERT INTO achievements (id, name, description) VALUES ('2','Loyal follower','React to the logs of the same user you follow 
            more than 20 times')";
            $sql3 = "INSERT INTO achievements (id, name, description) VALUES ('3','Fear means nothing to you!','Study kanji for 5 days straight')";
            
            if ($mysqli->query($sql1) && $mysqli->query($sql2) && $mysqli->query($sql3)) {
                echo "<br>";
                echo "Inserción para tabla achievements realizada con éxito";
            }
            else echo "Error insertando datos para tabla achievements";
        } 
        else echo "Error al intentar crear tabla achievements";

        //#######################################
        //########## 3. TABLA PROFILE ###########
        //#######################################

        // Definimos la tabla Profile
        $createTable3 = "CREATE TABLE IF NOT EXISTS profile (
            user_id INT PRIMARY KEY,
            bio TEXT,
            native_lang VARCHAR(100) NOT NULL,
            languages TEXT NOT NULL,
            fluent TEXT,
            learning TEXT,
            on_hold TEXT,
            dabbling TEXT,
            level BIGINT DEFAULT 0,
            experience INT DEFAULT 0,
            dark_mode BOOLEAN DEFAULT FALSE,
            num_followers INT DEFAULT 0,
            num_following INT DEFAULT 0,
            is_public BOOLEAN DEFAULT TRUE,
            profile_pic TEXT,
            bg_pic TEXT,
            game_roles TEXT,
            FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE)";

            // Primary key is also foreign key because profile is a weak entity of user
            // languages TEXT NOT NULL, --At least one language
            // profile_pic TEXT, --Tendré que poner aquí una de tipo default en general: un placeholder
            // bg_pic TEXT, --Tendré que poner aquí una de tipo default en general: un placeholder
        
        // Creamos la tabla profile
        if ($mysqli->query($createTable3) === TRUE) {
            echo "<br>";
            echo "Tabla profile creada con éxito";

            //Insertamos datos
            $sql1 = "INSERT INTO profile (user_id, bio, native_lang, languages, level, experience, dark_mode, is_public, profile_pic) 
            VALUES ('1', 'Mi bio', 'Spanish', 'Japanese, Chinese', '5', '10', FALSE, TRUE, './img/Qi\'ra avatar.jpg')";
            $sql2 = "INSERT INTO profile (user_id, bio, native_lang, languages, level, experience, dark_mode, is_public) 
            VALUES ('2', 'Mi bio', 'Spanish', 'Japanese, Chinese', '5', '10', FALSE, TRUE)";
            $sql3 = "INSERT INTO profile (user_id, bio, native_lang, languages, level, experience, dark_mode, is_public) 
            VALUES ('3', 'Mi bio', 'Spanish', 'Japanese, Chinese', '5', '10', FALSE, TRUE)";
            
            if ($mysqli->query($sql1) === TRUE && $mysqli->query($sql2) === TRUE && $mysqli->query($sql3) === TRUE) {
                echo "<br>";
                echo "Inserción para tabla profile realizada con éxito";
            }
            else echo "Error insertando datos para tabla profile";
        } 
        else echo "Error al intentar crear tabla profile";

        //#######################################
        //########### 4. TABLA LOGS #############
        //#######################################

        // Definimos la tabla Logs
        $createTable4 = "CREATE TABLE IF NOT EXISTS logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            description TEXT NOT NULL,
            language VARCHAR(50),
            type ENUM('grammar', 'listening', 'vocabulary', 'reading', 'writing', 'speaking', 'mixed') NOT NULL,
            duration INT NOT NULL,
            log_date DATE NOT NULL,
            post_date TIMESTAMP NOT NULL,
            FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE)";
        
        // Creamos la tabla logs
        if ($mysqli->query($createTable4) === TRUE) {
            echo "<br>";
            echo "Tabla logs creada con éxito";
        
            // Insertamos el primer registro (corregido)
            $sql1 = "INSERT INTO logs (user_id, description, language, type, duration, log_date, post_date)
            VALUES ('1', 'Fusce metus elit, dignissim vitae malesuada vehicula, scelerisque viverra ligula. Suspendisse facilisis ultricies lacus eget varius. Fusce lacus nulla, porta vel tempus eu, semper sit amet felis. Aenean vitae nunc eget turpis tristique sodales a at nunc. Suspendisse quis orci nec leo euismod vestibulum. Ut luctus leo.',
            'Chinese', 'mixed', '30', '2025-03-31', '2025-04-01 00:00:00')";
        
            // Insertamos el segundo registro
            $sql2 = "INSERT INTO logs (user_id, description, language, type, duration, log_date, post_date)
            VALUES ('1', 'Fusce metus elit, dignissim vitae malesuada vehicula, scelerisque viverra ligula. Suspendisse facilisis ultricies lacus eget varius.',
            'Japanese', 'kanji', '15', '2025-03-31', '2024-10-27 15:30:00')";
        
            if ($mysqli->query($sql1) === TRUE && $mysqli->query($sql2) === TRUE) {
                echo "<br>";
                echo "Inserción para tabla logs realizada con éxito";
            } else {
                echo "<br>";
                echo "Error insertando datos para tabla logs: " . $mysqli->error; // Importante mostrar el error
            }
        } else {
            echo "<br>";
            echo "Error creando la tabla logs: " . $mysqli->error; // Importante mostrar el error
        }

        //#######################################
        //########## 5. TABLA EVENTS ############
        //#######################################

        // Definimos la tabla events
        $createTable5 = "CREATE TABLE IF NOT EXISTS events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(300) NOT NULL,
            description TEXT,
            creation_date DATE,
            event_date DATE)";
        
        // Creamos la tabla events
        if ($mysqli->query($createTable5) === TRUE) {
            echo "<br>";
            echo "Tabla events creada con éxito";

            // Insertamos el primer registro de evento
            $sql3 = "INSERT INTO events (name, description, creation_date, event_date)
                    VALUES ('Language Exchange Meetup', 'Join us for a casual language exchange session. Practice speaking and meet new people!',
                    '2025-04-05', '2025-04-15')";

            // Insertamos el segundo registro de evento
            $sql4 = "INSERT INTO events (name, description, creation_date, event_date)
                    VALUES ('Online Japanese Conversation Club', 'Practice your Japanese speaking skills in a relaxed online environment.',
                    '2025-04-07', '2025-04-20')";

            if ($mysqli->query($sql3) === TRUE && $mysqli->query($sql4) === TRUE) {
                echo "<br>";
                echo "Inserción para tabla events realizada con éxito";
            } else {
                echo "<br>";
                echo "Error insertando datos para tabla events: " . $mysqli->error; // Importante mostrar el error
            }
        } else {
            echo "<br>";
            echo "Error creando la tabla events: " . $mysqli->error; // Importante mostrar el error
        }

        //---------------------------------------------------------------RELATION TABLES

        //#######################################
        //######## 6. TABLA FOLLOWERS ###########
        //#######################################

        // Definimos la tabla followers
        $createTable6 = "CREATE TABLE IF NOT EXISTS followers (
            follower_id INT NOT NULL,
            followed_id INT NOT NULL,
            PRIMARY KEY (follower_id, followed_id),
            FOREIGN KEY (follower_id) REFERENCES user(id) ON DELETE CASCADE,
            FOREIGN KEY (followed_id) REFERENCES user(id) ON DELETE CASCADE)";

            // follower_id INT NOT NULL, -- id of user following another user
            // followed_id INT NOT NULL, -- id of the followed user

        // Creamos la tabla followers
        if ($mysqli->query($createTable6) === TRUE) {
            echo "<br>";
            echo "Tabla followers creada con éxito";

            // Insertamos la primera relación de seguimiento
            $sql5 = "INSERT INTO followers (follower_id, followed_id)
                    VALUES ('1', '2')"; // El usuario con ID 1 sigue al usuario con ID 2

            // Insertamos la segunda relación de seguimiento
            $sql6 = "INSERT INTO followers (follower_id, followed_id)
                    VALUES ('2', '3')"; // El usuario con ID 2 sigue al usuario con ID 3

            // Insertamos la tercera relación de seguimiento
            $sql7 = "INSERT INTO followers (follower_id, followed_id)
                    VALUES ('3', '1')"; // El usuario con ID 3 sigue al usuario con ID 1

            if ($mysqli->query($sql5) === TRUE && $mysqli->query($sql6) === TRUE && $mysqli->query($sql7) === TRUE) {
                echo "<br>";
                echo "Inserción para tabla followers realizada con éxito";
            } else {
                echo "<br>";
                echo "Error insertando datos para tabla followers: " . $mysqli->error; // Importante mostrar el error
            }
        } else {
            echo "<br>";
            echo "Error creando la tabla followers: " . $mysqli->error; // Importante mostrar el error
        }

        //#######################################
        //######## 7. USER_ACHIEVEMENTS #########
        //#######################################

        $createTable7 = "CREATE TABLE IF NOT EXISTS user_achievements  (
            user_id INT,
            achievement_id INT,
            PRIMARY KEY (user_id, achievement_id),
            FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
            FOREIGN KEY (achievement_id) REFERENCES achievements(id))";
        
        // Creamos la tabla user_achievements
        if ($mysqli->query($createTable7) === TRUE) {
            echo "<br>";
            echo "Tabla user_achievements creada con éxito";

            // Insertamos la primera asignación de logro a usuario
            $sql8 = "INSERT INTO user_achievements (user_id, achievement_id)
                    VALUES ('1', '1')"; // El usuario con ID 1 ha obtenido el logro con ID 101

            // Insertamos la segunda asignación de logro a usuario
            $sql9 = "INSERT INTO user_achievements (user_id, achievement_id)
                    VALUES ('2', '2')"; // El usuario con ID 2 ha obtenido el logro con ID 102

            // Insertamos la tercera asignación de logro a usuario
            $sql10 = "INSERT INTO user_achievements (user_id, achievement_id)
                    VALUES ('1', '3')"; // El usuario con ID 1 ha obtenido el logro con ID 103

            if ($mysqli->query($sql8) === TRUE && $mysqli->query($sql9) === TRUE && $mysqli->query($sql10) === TRUE) {
                echo "<br>";
                echo "Inserción para tabla user_achievements realizada con éxito";
            } else {
                echo "<br>";
                echo "Error insertando datos para tabla user_achievements: " . $mysqli->error; // Importante mostrar el error
            }
        } else {
            echo "<br>";
            echo "Error creando la tabla user_achievements: " . $mysqli->error; // Importante mostrar el error
        }

        //#######################################
        //############ 8. BOOKING ###############
        //#######################################

        $createTable8 = "CREATE TABLE IF NOT EXISTS booking  (
            user_id INT,
            event_id INT,
            PRIMARY KEY (user_id, event_id),
            FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
            FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE)";

        // Creamos la tabla booking
        if ($mysqli->query($createTable8) === TRUE) {
            echo "<br>";
            echo "Tabla booking creada con éxito";

            // Insertamos la primera reserva
            $sql11 = "INSERT INTO booking (user_id, event_id)
                    VALUES ('1', '1')"; // El usuario con ID 1 se ha registrado en el evento con ID 1

            // Insertamos la segunda reserva
            $sql12 = "INSERT INTO booking (user_id, event_id)
                    VALUES ('2', '1')"; // El usuario con ID 2 se ha registrado en el evento con ID 1

            // Insertamos la tercera reserva
            $sql13 = "INSERT INTO booking (user_id, event_id)
                    VALUES ('1', '2')"; // El usuario con ID 1 se ha registrado en el evento con ID 2

            if ($mysqli->query($sql11) === TRUE && $mysqli->query($sql12) === TRUE && $mysqli->query($sql13) === TRUE) {
                echo "<br>";
                echo "Inserción para tabla booking realizada con éxito";
            } else {
                echo "<br>";
                echo "Error insertando datos para tabla booking: " . $mysqli->error; // Importante mostrar el error
            }
        } else {
            echo "<br>";
            echo "Error creando la tabla booking: " . $mysqli->error; // Importante mostrar el error
        }


    } 
    else echo "Error creando la BD";

    // Cerramos la conexión
    $mysqli->close();
}
else echo "Error de conexión a BD";
?>
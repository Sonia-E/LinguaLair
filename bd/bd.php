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
                echo "Error insertando datos para tabla roles: " . $mysqli->error;
            }
        } else {
            echo "<br>";
            echo "Error creando la tabla roles: " . $mysqli->error;
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
                ('ban_user', 'Permite banear usuarios.'),
                ('create_events', 'Permite organizar eventos y publicarlos.'),
                ('attend_in_person_events', 'Permite asistir a eventos en persona')";

            if ($mysqli->query($sql15) === TRUE) {
                echo "<br>";
                echo "Inserción para tabla permissions realizada con éxito";
            } else {
                echo "<br>";
                echo "Error insertando datos para tabla permissions: " . $mysqli->error;
            }
        } else {
            echo "<br>";
            echo "Error creando la tabla permissions: " . $mysqli->error;
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
                ((SELECT id FROM roles WHERE name = 'admin'), (SELECT id FROM permissions WHERE name = 'ban_user')),
                ((SELECT id FROM roles WHERE name = 'admin'), (SELECT id FROM permissions WHERE name = 'create_events')),
                ((SELECT id FROM roles WHERE name = 'admin'), (SELECT id FROM permissions WHERE name = 'attend_in_person_events')),
                ((SELECT id FROM roles WHERE name = 'premium'), (SELECT id FROM permissions WHERE name = 'create_events')),
                ((SELECT id FROM roles WHERE name = 'premium'), (SELECT id FROM permissions WHERE name = 'attend_in_person_events'))";

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
        //############ GAME_ROLES ###############
        //#######################################

        $game_rolesTable = "CREATE TABLE IF NOT EXISTS game_roles (
            role_id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) UNIQUE NOT NULL,
            description VARCHAR(255)
            )";

        // Creamos la tabla roles
        if ($mysqli->query($game_rolesTable) === TRUE) {
            echo "<br>";
            echo "Tabla roles creada con éxito";

            $sql17 = "INSERT INTO game_roles (name, description) VALUES
                ('Novice', 'Rol inicial para nuevos jugadores.'),
                ('Apprentice', 'Jugador que está aprendiendo los fundamentos.'),
                ('Amateur', 'Jugador con cierta experiencia.'),
                ('Journeyman', 'Jugador competente con habilidades sólidas.'),
                ('Adept', 'Jugador muy hábil y con gran dominio.'),
                ('Ace', 'Jugador excepcional con un rendimiento destacado.'),
                ('Expert', 'Jugador con un conocimiento profundo del juego.'),
                ('Exemplar', 'Jugador que sirve de modelo a seguir.'),
                ('Mentor', 'Jugador experimentado que guía a otros.'),
                ('Master', 'Jugador que ha alcanzado un nivel de maestría.'),
                ('Grandmaster', 'Jugador de la más alta categoría.')";

            if ($mysqli->query($sql17) === TRUE) {
                echo "<br>";
                echo "Inserción para tabla roles realizada con éxito";
            } else {
                echo "<br>";
                echo "Error insertando datos para tabla roles: " . $mysqli->error;
            }
        } else {
            echo "<br>";
            echo "Error creando la tabla roles: " . $mysqli->error;
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
            $sql1 = "INSERT INTO user (id, username, nickname, password,  email, country, role_id) VALUES ('chieloveslangs','~Chie~', '$2y$10$at3Nz5ecT.LjkLoGoZLIeuS.rtgJUvDwALY84wlBXpJMStBywItZa','hola@gmail', 'Spain', '2')";
            $sql2 = "INSERT INTO user (id, username, password,  email, country) VALUES ('Sauron','bu','hola1@gmail', 'Mordor')";
            $sql3 = "INSERT INTO user (id, username, password,  email, country) VALUES ('Kakashi','bu','hola2@gmail', 'Konoha')";
            
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
            name VARCHAR(100) NOT NULL,
            description TEXT NOT NULL,
            icon TEXT NOT NULL,
            type ENUM('grammar', 'listening', 'vocabulary', 'reading', 'writing', 'speaking', 'follow', 'logs'),
            level ENUM('bronze', 'silver', 'gold')
            )";

        // Creamos la tabla achievements
        if ($mysqli->query($createTable2) === TRUE) {
            echo "<br>";
            echo "Tabla achievements creada con éxito";

            //Insertamos datos
            $sql1 = "INSERT INTO achievements (name, description, icon, type, level) VALUES ('Fear means nothing to you!','Study grammar for 5 days straight', 'public/img/achievements/1-Book.gif', 'grammar', 'bronze')";
            $sql2 = "INSERT INTO achievements (name, description, icon, type, level) VALUES ('Fear means nothing to you!','Study grammar for 10 days straight', 'public/img/achievements/2-Book.gif', 'grammar', 'silver')";
            $sql3 = "INSERT INTO achievements (name, description, icon, type, level) VALUES ('Fear means nothing to you!','Study grammar for 15 days straight', 'public/img/achievements/3-Book.gif', 'grammar', 'gold')";
            $sql4 = "INSERT INTO achievements (name, description, icon, type, level) VALUES ('Consistency is key!','Post 5 logs', 'public/img/achievements/1-Sword.gif', 'logs', 'bronze')";
            $sql5 = "INSERT INTO achievements (name, description, icon, type, level) VALUES ('Consistency is key!','Post 10 logs', 'public/img/achievements/2-Sword.gif', 'logs', 'silver')";
            $sql6 = "INSERT INTO achievements (name, description, icon, type, level) VALUES ('Consistency is key!','Post 15 logs', 'public/img/achievements/3-Sword.gif', 'logs', 'gold')";
            $sql7 = "INSERT INTO achievements (name, description, icon, type, level) VALUES ('You love your adding ingredients to your!','Study grammar for 5 days straight', 'public/img/achievements/1-Potion.gif', 'vocabulary', 'bronze')";
            $sql8 = "INSERT INTO achievements (name, description, icon, type, level) VALUES ('You love your adding ingredients to your cauldron!','Study grammar for 10 days straight', 'public/img/achievements/2-Potion.gif', 'vocabulary', 'silver')";
            $sql9 = "INSERT INTO achievements (name, description, icon, type, level) VALUES ('You love your adding ingredients to your cauldron!','Study grammar for 15 days straight', 'public/img/achievements/3-Potion.gif', 'vocabulary', 'gold')";
            $sql10 = "INSERT INTO achievements (name, description, icon, type, level) VALUES ('You sure enjoy sharing your knowledge with the world!','Practice writing for 5 days straight', 'public/img/achievements/1-Scroll.gif', 'writing', 'bronze')";
            $sql11 = "INSERT INTO achievements (name, description, icon, type, level) VALUES ('You sure enjoy sharing your knowledge with the world!','Practice writing for 10 days straight', 'public/img/achievements/2-Scroll.gif', 'writing', 'silver')";
            $sql12 = "INSERT INTO achievements (name, description, icon, type, level) VALUES ('You sure enjoy sharing your knowledge with the world!','Practice writing for 15 days straight', 'public/img/achievements/3-Scroll.gif', 'writing', 'gold')";
            
            if ($mysqli->query($sql1) && $mysqli->query($sql2) && $mysqli->query($sql3) && $mysqli->query($sql4) && $mysqli->query($sql5) && $mysqli->query($sql6) && $mysqli->query($sql7)
            && $mysqli->query($sql8) && $mysqli->query($sql9) && $mysqli->query($sql10) && $mysqli->query($sql11) && $mysqli->query($sql12)) {
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
            future TEXT,
            level BIGINT DEFAULT 0,
            experience INT DEFAULT 0,
            dark_mode BOOLEAN DEFAULT FALSE,
            num_followers INT DEFAULT 0,
            num_following INT DEFAULT 0,
            is_public BOOLEAN DEFAULT TRUE,
            profile_pic TEXT,
            bg_pic TEXT,
            game_roles VARCHAR(50) DEFAULT 'Novice',
            FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
            FOREIGN KEY (game_roles) REFERENCES game_roles(name) ON DELETE CASCADE)";

            // Primary key is also foreign key because profile is a weak entity of user
            // languages TEXT NOT NULL, --At least one language
        
        // Creamos la tabla profile
        if ($mysqli->query($createTable3) === TRUE) {
            echo "<br>";
            echo "Tabla profile creada con éxito";

            //Insertamos datos
            $sql1 = "INSERT INTO profile (user_id, bio, native_lang, languages, level, experience, dark_mode, num_followers, num_following, is_public, profile_pic, bg_pic) 
            VALUES ('1', 'Mi bio', 'Spanish', 'Japanese, Chinese', '5', '10', FALSE, '1', '1', TRUE, 'public/img/Qi\'ra avatar.jpg', 'public/img/WWX1.jpg')";
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
            'Japanese', 'grammar', '15', '2025-03-31', '2024-10-27 15:30:00')";
        
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
            event_date DATE,
            type ENUM('Online', 'In person') NOT NULL,
            subtype ENUM('Language Exchange', 'General', 'Mixed') NOT NULL DEFAULT 'General',
            exchange_lang_1 VARCHAR(50),
            exchange_lang_2 VARCHAR(50),
            main_lang VARCHAR(50), -- The organization language
            learning_lang VARCHAR(50),
            city VARCHAR(100),
            country VARCHAR(100),
            event_time TIME,
            long_description TEXT,
            attending INT DEFAULT 0 -- Total of attending people
            )";
        
        // Creamos la tabla events
        if ($mysqli->query($createTable5) === TRUE) {
            echo "<br>";
            echo "Tabla events creada con éxito";

            // Insertamos el primer registro de evento con long_description
            $sql3_extended = "INSERT INTO events (name, description, long_description, creation_date, event_date, type, subtype, exchange_lang_1, exchange_lang_2, city, country)
            VALUES (
                'Language Exchange Meetup',
                'Join us for a casual language exchange session. Practice speaking and meet new people!',
                'We welcome speakers of Japanese and Spanish of all levels. Whether you are a beginner looking to practice basic phrases or an advanced speaker wanting to engage in in-depth conversations, you are welcome to join us.

The session will be held in a relaxed and friendly atmosphere at [Nombre del Lugar]. Come and enjoy practicing your target language while making new friends!

Don\'t forget to bring your enthusiasm and willingness to communicate!',
                '2025-04-05',
                '2025-04-15',
                'In person',
                'Language Exchange',
                'Japanese',
                'Spanish',
                'Madrid',
                'Spain'
            )";

            if ($mysqli->query($sql3_extended) === TRUE) {
            echo "<br>Evento 'Language Exchange Meetup' insertado con éxito.";
            } else {
            echo "<br>Error al insertar el evento 'Language Exchange Meetup': " . $mysqli->error;
            }

            // Insertamos el segundo registro de evento con long_description
            $sql4_extended = "INSERT INTO events (name, description, long_description, creation_date, event_date, type, subtype, main_lang, learning_lang)
                VALUES (
                    'Online Japanese Conversation Club',
                    'Practice your Japanese speaking skills in a relaxed online environment.',
                    'Welcome to our Online Japanese Conversation Club!

This is a fantastic opportunity to improve your Japanese speaking skills from the comfort of your own home. We focus on creating a relaxed and supportive environment where learners of all levels can practice speaking.

Our sessions often include discussions on various topics, casual conversations, and opportunities to ask questions. The main language of organization is English, but the target language is Japanese.

We look forward to seeing you online and practicing Japanese together!',
                    '2025-04-07',
                    '2025-04-20',
                    'Online',
                    'General',
                    'English',
                    'Japanese'
                )";

            if ($mysqli->query($sql4_extended) === TRUE) {
                echo "<br>Evento 'Online Japanese Conversation Club' insertado con éxito.";
            } else {
                echo "<br>Error al insertar el evento 'Online Japanese Conversation Club': " . $mysqli->error;
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
            unlock_date TIMESTAMP NOT NULL,
            PRIMARY KEY (user_id, achievement_id),
            FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
            FOREIGN KEY (achievement_id) REFERENCES achievements(id))";
        
        // Creamos la tabla user_achievements
        if ($mysqli->query($createTable7) === TRUE) {
            echo "<br>";
            echo "Tabla user_achievements creada con éxito";
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
        } else {
            echo "<br>";
            echo "Error creando la tabla booking: " . $mysqli->error; // Importante mostrar el error
        }

        //#######################################
        //########## 9. NOTIFICATIONS ###########
        //#######################################

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
    else echo "Error creando la BD";

    // Cerramos la conexión
    $mysqli->close();
}
else echo "Error de conexión a BD";
?>
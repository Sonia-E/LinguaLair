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
        //########## 1. TABLA USER ##############
        //#######################################

        // Definimos la tabla User
        $createTable1= "CREATE TABLE IF NOT EXISTS user (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            nickname VARCHAR(50), -- Se puede dejar vacío, porque al crear el usuario si no añades nickname, 
                                    -- te pone el username que tengas y luego puedes editarlo
            password VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            country VARCHAR(50) NOT NULL,
            roles ENUM('standard', 'admin', 'premium'))";
            
        // Creamos la tabla user
        if ($mysqli->query($createTable1) === TRUE) {
            echo "<br>";
            echo "Tabla user creada con éxito";

            //Insertamos datos
            $sql1 = "INSERT INTO user (id, username, nickname, password,  email, country, roles) VALUES ('1','chieloveslangs','~Chie~', 'contrasenia','hola@gmail', 'Spain', 'standard')";
            $sql2 = "INSERT INTO user (id, username, password,  email, country, roles) VALUES ('2','Sauron','bu','hola1@gmail', 'Mordor', 'standard')";
            
            if ($mysqli->query($sql1) && $mysqli->query($sql2)) {
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
        $createTable2= "CREATE TABLE IF NOT EXISTS achievements (
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
        $createTable3= "CREATE TABLE IF NOT EXISTS profile (
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
            is_active BOOLEAN DEFAULT TRUE,
            profile_pic TEXT,
            bg_pic TEXT,
            game_roles TEXT,
            FOREIGN KEY (user_id) REFERENCES user(id))";

            // Primary key is also foreign key because profile is a weak entity of user
            // languages TEXT NOT NULL, --At least one language
            // profile_pic TEXT, --Tendré que poner aquí una de tipo default en general: un placeholder
            // bg_pic TEXT, --Tendré que poner aquí una de tipo default en general: un placeholder
        
        // Creamos la tabla profile
        if ($mysqli->query($createTable3) === TRUE) {
            echo "<br>";
            echo "Tabla profile creada con éxito";

            //Insertamos datos
            $sql1 = "INSERT INTO profile (user_id, bio, native_lang, languages, level, experience, dark_mode, is_active, profile_pic) 
            VALUES ('1', 'Mi bio', 'Spanish', 'Japanese, Chinese', '5', '10', FALSE, TRUE, './img/Qi\'ra avatar.jpg')";
            $sql2 = "INSERT INTO profile (user_id, bio, native_lang, languages, level, experience, dark_mode, is_active) 
            VALUES ('2', 'Mi bio', 'Spanish', 'Japanese, Chinese', '5', '10', FALSE, TRUE)";
            
            if ($mysqli->query($sql1) && $mysqli->query($sql2)) {
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
        $createTable4= "CREATE TABLE IF NOT EXISTS logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            description TEXT NOT NULL,
            language VARCHAR(50),
            type ENUM('gramática', 'listening', 'kanji', 'vocabulario', 'lectura', 'escritura', 'mixed') NOT NULL,
            duration INT NOT NULL,
            log_date DATE NOT NULL,
            post_date TIMESTAMP NOT NULL,
            FOREIGN KEY (user_id) REFERENCES user(id))";
        
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
        $createTable6= "CREATE TABLE IF NOT EXISTS events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(300) NOT NULL,
            description TEXT,
            user_id INT,
            creation_date DATE,
            event_date DATE,
            FOREIGN KEY (user_id) REFERENCES user(id))";
        

        //---------------------------------------------------------------RELATION TABLES

        //#######################################
        //######## 6. TABLA FOLLOWERS ###########
        //#######################################

        // Definimos la tabla Profile
        $createTable5= "CREATE TABLE IF NOT EXISTS followers (
            follower_id INT NOT NULL,
            followed_id INT NOT NULL,
            PRIMARY KEY (follower_id, followed_id),
            FOREIGN KEY (follower_id) REFERENCES user(id),
            FOREIGN KEY (followed_id) REFERENCES user(id))";

            // follower_id INT NOT NULL, -- id of user following another user
            // followed_id INT NOT NULL, -- id of the followed user

        //#######################################
        //######## 7. USER_ACHIEVEMENTS #########
        //#######################################

        $createTable2= "CREATE TABLE IF NOT EXISTS user_achievements  (
            user_id INT,
            achievement_id INT,
            PRIMARY KEY (user_id, achievement_id),
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (achievement_id) REFERENCES achievements(id))";

        //#######################################
        //############ 8. BOOKING ###############
        //#######################################

        $createTable2= "CREATE TABLE IF NOT EXISTS booking  (
            user_id INT,
            event_id INT,
            PRIMARY KEY (user_id, event_id),
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (event_id) REFERENCES events(id))";
    } 
    else echo "Error creando la BD";

    // Cerramos la conexión
    $mysqli->close();
}
else echo "Error de conexión a BD";
?>
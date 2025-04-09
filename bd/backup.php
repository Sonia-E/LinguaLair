<?php
// Creamos la conexión
if ($mysqli = new mysqli("localhost", "foc", "foc")) {

    // Comprobar si existe ya la base de datos Libros para que no se intente crear todo de nuevo
    $result = $mysqli->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'Lingualair'");
    if (!$result->num_rows > 0) {
        // Mostramos que la conexión ha sido establecida
        echo "Conexión establecida";

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

            //-------------USUARIOS----------------

            //#######################################
            //############ TABLA USERS ##############
            //#######################################

            // Definimos la tabla Users
            $createTable1= "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                nickname VARCHAR(50), -- Se puede dejar vacío, porque al crear el usuario si no añades nickname, 
                                        -- te pone el username que tengas y luego puedes editarlo
                password VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                country VARCHAR(50) NOT NULL,
                is_active BOOLEAN DEFAULT TRUE)";
                
            // Creamos la tabla Autor
            if ($mysqli->query($createTable1) === TRUE) {
                echo "<br>";
                echo "Tabla Autor creada con éxito";

                //Insertamos datos
                $sql1 = "INSERT INTO users  (id, username, password,  email, country) VALUES ('0','darthshizuka','bu','hola@gmail', 'Spain')";
                $sql2 = "INSERT INTO users  (id, username, password,  email, country) VALUES ('0','Sauron','bu','hola1@gmail', 'Mordor')";
                
                if ($mysqli->query($sql1) && $mysqli->query($sql2)) {
                    echo "<br>";
                    echo "Inserción para tabla Autor realizada con éxito";
                }
                else echo "Error insertando datos para tabla Autor";
            } 
            else echo "Error al intentar crear tabla Autor";

            //#######################################
            //############ TABLA ROLES ##############
            //#######################################

            // Definimos la tabla Roles
            $createTable2= "CREATE TABLE IF NOT EXISTS roles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(20) UNIQUE NOT NULL)";
                
            // Creamos la tabla Autor
            if ($mysqli->query($createTable2) === TRUE) {
                echo "<br>";
                echo "Tabla Libro creada con éxito";

                //Insertamos datos
                $sql1 = "INSERT INTO Libro  (id, titulo,  f_publicacion,  id_autor) VALUES
                    ('0','El Hobbit',STR_TO_DATE('21/09/1937', '%d/%m/%Y'),'0')";
                $sql2 = "INSERT INTO Libro  (id, titulo,  f_publicacion,  id_autor) VALUES
                    ('1','La Comunidad del Anillo',STR_TO_DATE('29/07/1954', '%d/%m/%Y'),'0')";
                $sql3 = "INSERT INTO Libro  (id, titulo,  f_publicacion,  id_autor) VALUES
                    ('2','Las dos torres',STR_TO_DATE('11/11/1954', '%d/%m/%Y'),'0')";
                $sql4 = "INSERT INTO Libro  (id, titulo,  f_publicacion,  id_autor) VALUES
                    ('3','El retorno del Rey',STR_TO_DATE('20/10/1955', '%d/%m/%Y'),'0')";
                $sql5 = "INSERT INTO Libro  (id, titulo,  f_publicacion,  id_autor) VALUES
                    ('4','Un guijarro en el cielo',STR_TO_DATE('19/01/1950', '%d/%m/%Y'),'1')";
                $sql6 = "INSERT INTO Libro  (id, titulo,  f_publicacion,  id_autor) VALUES
                    ('5','Fundación',STR_TO_DATE('01/06/1951', '%d/%m/%Y'),'1')";
                $sql7 = "INSERT INTO Libro  (id, titulo,  f_publicacion,  id_autor) VALUES
                    ('6','Yo, robot',STR_TO_DATE('02/12/1950', '%d/%m/%Y'),'1')";
                
                if ($mysqli->query($sql1) && $mysqli->query($sql2) && $mysqli->query($sql3)
                && $mysqli->query($sql4) && $mysqli->query($sql5) && $mysqli->query($sql6) 
                && $mysqli->query($sql7)) {
                    echo "<br>";
                    echo "Inserción para tabla Libro realizada con éxito";
                }
                else echo "Error insertando datos para tabla Libro";
            } 
            else echo "Error al intentar crear tabla Libro";

            //#######################################
            //########## TABLA USER_ROLES ########### ----Tabla de relación
            //#######################################

            // Definimos la tabla user_roles
            $createTable2= "CREATE TABLE IF NOT EXISTS user_roles  (
                user_id INT,
                role_id INT,
                PRIMARY KEY (user_id, role_id),
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (role_id) REFERENCES roles(id))";

            //#######################################
            //############ TABLA LOGROS #############
            //#######################################

            // Definimos la tabla Logros
            $createTable2= "CREATE TABLE IF NOT EXISTS logros (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) UNIQUE NOT NULL,
                description TEXT)";

            //#######################################
            //########## TABLA USER_LOGROS ########### ----Tabla de relación
            //#######################################

            // Definimos la tabla user_logros
            $createTable2= "CREATE TABLE IF NOT EXISTS user_logros  (
                user_id INT,
                logro_id INT,
                PRIMARY KEY (user_id, logro_id),
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (logro_id) REFERENCES logros(id))";

            //#######################################
            //########### TABLA PROFILE #############
            //#######################################

            // Definimos la tabla Profile
            $createTable1= "CREATE TABLE IF NOT EXISTS profile (
                user_id INT PRIMARY KEY,
                bio TEXT,
                languages TEXT, -- Esto se tendrá q convertir en 1 tabla de languages
                                -- y añadir una tabla de conexión para asignar idiomas al usuario
                languages_in_bio TEXT, -- Esto se tendrá q convertir en 1 tabla de langs_states (fluent, studying, on hold, etc)
                                       -- y añadir una tabla de conexión
                                       -- O MEJOR NO convertir en tabla aparte, sino separar cada apartado (fluent, etc) como otras columnas
                                       -- para así poner ahí q sean tipo TEXT???
                level VARCHAR(50),
                experience INT,
                dark_mode BOOLEAN DEFAULT FALSE,
                following TEXT, -- Convertir en tabla de relación
                followers TEXT, -- Convertir en tabla de relación
                num_followers INT DEFAULT 0,
                num_following INT DEFAULT 0,
                FOREIGN KEY (user_id) REFERENCES users(id))";
        } 
        else echo "Error creando la BD";
    
        // Cerramos la conexión
        $mysqli->close();
    }
}
else echo "Error de conexión a BD";
?>
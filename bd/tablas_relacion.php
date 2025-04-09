<?php
// Creamos la conexión
if ($mysqli = new mysqli("localhost", "foc", "foc")) {
    // Comprobar si existe ya la base de datos Libros para que no se intente crear todo de nuevo
    $result = $mysqli->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'Lingualair'");
    if (!$result->num_rows > 0) {
        // Mostramos que la conexión ha sido establecida
        echo "Conexión establecida";


        // Seleccionamos BD libros para crear tablas
        $mysqli->select_db("Lingualair");

        //-------------TABLAS DE RELACIÓN---------------- 

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
        //########## TABLA USER_LOGROS ########### ----Tabla de relación
        //#######################################

        // Definimos la tabla user_logros
        $createTable2= "CREATE TABLE IF NOT EXISTS user_logros  (
            user_id INT,
            logro_id INT,
            PRIMARY KEY (user_id, logro_id),
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (logro_id) REFERENCES logros(id))";
    }
    
    // Cerramos la conexión
    $mysqli->close();
}
else echo "Error de conexión a BD";
?>
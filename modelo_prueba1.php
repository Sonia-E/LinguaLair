<?php
    // Evitamos que se llame al fichero sin pasar por el controlador
	// if (!defined('CON_CONTROLADOR')) {
    //     // Matamos el proceso php
	// 	die('Error: No se permite el acceso directo a esta ruta');
	// }

    // class Modelo {
    //     private $conexion;
    
        public function __construct($servidor, $usuario, $contrasenia, $base_datos) {
            $this->conexion = new mysqli($servidor, $usuario, $contrasenia, $base_datos);
    
            if ($this->conexion->connect_error) {
                die("Conexión fallida: " . $this->conexion->connect_error);
                $this->conexion = null; // Importante para indicar que la conexión falló
            } else {
                $this->conexion->set_charset("utf8");
            }
        }
    
        public function getConexion() {
            return $this->conexion;
        }

    // /**
    //  * Establece una conexión con una base de datos MySQL utilizando los parámetros proporcionados.
    //  *
    //  * @param string $servidor Nombre del servidor de la base de datos.
    //  * @param string $usuario Nombre de usuario para la conexión.
    //  * @param string $contrasenia Contraseña del usuario.
    //  * @param string $base_datos Nombre de la base de datos.
    //  * @return mysqli|null Devuelve un objeto mysqli $conexion si la conexión es exitosa, null en caso de error.
    //  */
    // function conexion($servidor, $usuario, $contrasenia, $base_datos) {
    //     // Crear una conexión a la base de datos
    //     $conexion = new mysqli($servidor, $usuario, $contrasenia, $base_datos);
    
    //     // Verificar si hay algún error en la conexión
    //     if ($conexion->connect_error) {
    //         die("Conexión fallida: " . $conexion->connect_error);
    //         return null;
    //     } else {
    //         return $conexion;
    //     }
    // }

    //#######################################
	//############### SELECTS ###############
	//#######################################

    /**
	 * Función que accede a la base de datos para obtener los datos de todos los usuarios
     * 
	 * @return array $articulos Un array asociativo donde cada elemento es un array asociativo que representa un artículo
     *              Cada artículo tiene al menos las claves 'id', 'titulo', 'genero' y 'precio'
	 */
    function cargar_usuarios($conexion) {
        // Preparar la consulta
        $consulta = "SELECT * FROM user";


        // Comprobamos caracteres
        if (!$conexion->set_charset("utf8")) {
            printf("Error cargando el conjunto de caracteres requerido", $conexion->error);
            return null;
        }

        $resultado = $conexion->query($consulta);

        if ($resultado) {
            $usuarios = []; // Creamos un array vacío para almacenar los autores

            while ($usuario = $resultado->fetch_object()) {
                $usuarios[] = $usuario; // Agregamos todos los usuarios al array
            }
            // Cerramos la conexión
            $conexion->close();
            return $usuarios;
        } else {
            echo "Error al consultar BD" . $conexion->error;
            return null;
        } 
    }

	/**
	 * Función que accede a la base de datos para obtener los datos del usuario y de su perfil
     * 
	 * @return array $articulos Un array asociativo donde cada elemento es un array asociativo que representa un artículo
     *              Cada artículo tiene al menos las claves 'id', 'titulo', 'genero' y 'precio'
	 */
    function cargar_datos_usuario($conexion, $id) {
        // Preparar la consulta
        $consulta = "SELECT profile.*, user.*
                FROM profile INNER JOIN user
                ON profile.user_id = user.id
                WHERE user.id = $id";


        // Comprobamos caracteres
        if (!$conexion->set_charset("utf8")) {
            printf("Error cargando el conjunto de caracteres requerido", $conexion->error);
            return null;
        }

        $resultado = $conexion->query($consulta);

        if ($resultado) {
            $datos_usuario = []; // Creamos un array vacío para almacenar el autor y sus datos
      
            $usuario = $resultado->fetch_object();
            // Conseguimos los libros del autor
            $logsUsuario = get_logs($conexion, $id);
            // Combinamos ambos arrays
            $datos_usuario[] = array($usuario, $logsUsuario);
            // Cerramos la conexión
            $conexion->close();
            // Devuelve el autor, sus datos y libros asociados
            return $datos_usuario;
        } else {
            echo "Error al consultar BD" . $conexion->error;
            return null;
        }
    }

    function get_user($conexion, $id) {
        // Cargamos los datos para poder listarlos
        $usuario = cargar_datos_usuario($conexion, $id);
        return $usuario;
    }

    function get_logs($conexion, $user_id = null) {
        // Preparar la consulta
        $consulta = "SELECT
            logs.*,
            user.username,
            user.nickname
            FROM logs
            INNER JOIN user ON logs.user_id = user.id";
        if (!is_null($user_id)) {
        $consulta .= " WHERE logs.user_id = $user_id";
        }
    
        $resultado = $conexion->query($consulta);
    
        if ($resultado) {
        $logs = [];
        while ($log = $resultado->fetch_object()) {
            $logs[] = $log; // Cada fila contiene datos tanto de Libro como de Autor
            }
            if (is_null($user_id)) {
            // Cerramos la conexión
            $conexion->close();
            }
            
            return $logs;
        } else {
        echo "Error al consultar BD: " . $conexion->error;
        return null;
        }
    }

    //#######################################
	//############### INSERTS ###############
	//#######################################

	function add_log($conexion, $user_id, $description, $language, $type, $duration, $log_date) {
        // Preparar la consulta para la inserción
        $consulta = "INSERT INTO logs (user_id, description, language, type, duration, log_date, post_date)
                     VALUES (?, ?, ?, ?, ?, ?, NOW())";
    
        // Preparar la sentencia
        $stmt = $conexion->prepare($consulta);
    
        if ($stmt) {
            // Vincular los parámetros
            $stmt->bind_param("isssis", $user_id, $description, $language, $type, $duration, $log_date);
    
            // Ejecutar la sentencia
            if ($stmt->execute()) {
                // La inserción fue exitosa
                $stmt->close();
                return true;
            } else {
                // Error al ejecutar la inserción
                echo "Error al insertar log: " . $stmt->error;
                $stmt->close();
                return false;
            }
        } else {
            // Error al preparar la consulta
            echo "Error al preparar la consulta de inserción: " . $conexion->error;
            return false;
        }
    }

    public function __destruct() {
        if ($this->conexion) {
            $this->conexion->close();
        }
    }
    }
?>
<?php
    // Evitamos que se llame al fichero sin pasar por el controlador
	// if (!defined('CON_CONTROLADOR')) {
    //     // Matamos el proceso php
	// 	die('Error: No se permite el acceso directo a esta ruta');
	// }

    class Modelo {
        private $conexion;
    
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
    
        public function cargarUsuarios() {
            if (!$this->conexion) return null;
    
            $consulta = "SELECT * FROM user";
            $resultado = $this->conexion->query($consulta);
    
            if ($resultado) {
                $usuarios = [];
                while ($usuario = $resultado->fetch_object()) {
                    $usuarios[] = $usuario;
                }
                return $usuarios;
            } else {
                echo "Error al consultar BD: " . $this->conexion->error;
                return null;
            }
        }
    
        public function cargarDatosUsuario($id) {
            if (!$this->conexion) return null;
    
            $consulta = "SELECT profile.*, user.*
                         FROM profile INNER JOIN user
                         ON profile.user_id = user.id
                         WHERE user.id = ?";
    
            $stmt = $this->conexion->prepare($consulta);
            if ($stmt) {
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $stmt->close();
    
                if ($resultado && $resultado->num_rows > 0) {
                    $usuario = $resultado->fetch_object();
                    $logsUsuario = $this->getLogs($id); // Usamos el método de la clase
                    $datos_usuario[] = array($usuario, $logsUsuario);
                    return $datos_usuario;
                    // return [$usuario, $logsUsuario]; // Devolvemos un array con usuario y logs
                } else {
                    return null;
                }
            } else {
                echo "Error al preparar la consulta: " . $this->conexion->error;
                return null;
            }
        }
    
        public function getUser($id) {
            return $this->cargarDatosUsuario($id);
        }
    
        public function getLogs($user_id = null) {
            if (!$this->conexion) return null;
    
            $consulta = "SELECT
                            logs.*,
                            user.username,
                            user.nickname
                         FROM logs
                         INNER JOIN user ON logs.user_id = user.id";
            if (!is_null($user_id)) {
                $consulta .= " WHERE logs.user_id = ?";
                $stmt = $this->conexion->prepare($consulta);
                if ($stmt) {
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $resultado = $stmt->get_result();
                    $stmt->close();
                } else {
                    echo "Error al preparar la consulta: " . $this->conexion->error;
                    return null;
                }
            } else {
                $resultado = $this->conexion->query($consulta);
            }
    
            if ($resultado) {
                $logs = [];
                while ($log = $resultado->fetch_object()) {
                    $logs[] = $log;
                }
                return $logs;
            } else {
                echo "Error al consultar BD: " . $this->conexion->error;
                return null;
            }
        }
    
        public function addLog($user_id, $description, $language, $type, $duration, $log_date) {
            if (!$this->conexion) return false;
    
            $consulta = "INSERT INTO logs (user_id, description, language, type, duration, log_date, post_date)
                         VALUES (?, ?, ?, ?, ?, ?, NOW())";
    
            $stmt = $this->conexion->prepare($consulta);
    
            if ($stmt) {
                $stmt->bind_param("isssis", $user_id, $description, $language, $type, $duration, $log_date);
                if ($stmt->execute()) {
                    $stmt->close();
                    return true;
                } else {
                    echo "Error al insertar log: " . $stmt->error;
                    $stmt->close();
                    return false;
                }
            } else {
                echo "Error al preparar la consulta de inserción: " . $this->conexion->error;
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
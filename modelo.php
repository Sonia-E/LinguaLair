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

        public function searchUser($username = null, $email = null) {
            if (!$this->conexion) return null;
    
            $consulta = "SELECT * FROM user
                         WHERE username = ?";
    
            $stmt = $this->conexion->prepare($consulta);
            if ($stmt) {
                $stmt->bind_param("i", $username);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $stmt->close();
    
                if ($resultado && $resultado->num_rows > 0) {
                    $usuario = $resultado->fetch_object();
                    return $usuario;
                } else {
                    return null;
                }
            } else {
                echo "Error al preparar la consulta: " . $this->conexion->error;
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
    
        /**
         * Retrieves logs, optionally for a specific user, ordered by post_date descending.
         *
         * @param int|null $user_id The ID of the user to filter logs for (optional).
         * @return array|null An array of log objects, or null on error.
         */
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
            }

            $consulta .= " ORDER BY logs.post_date DESC"; // Add the ORDER BY clause

            $stmt = $this->conexion->prepare($consulta);

            if ($stmt) {
                if (!is_null($user_id)) {
                    $stmt->bind_param("i", $user_id);
                }
                $stmt->execute();
                $resultado = $stmt->get_result();
                $logs = [];
                while ($log = $resultado->fetch_object()) {
                    $logs[] = $log;
                }
                $stmt->close();
                return $logs;
            } else {
                echo "Error al preparar la consulta para obtener logs: " . $this->conexion->error;
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

        /**
         * Counts the total number of logs for a specific user.
         *
         * @param int $user_id The ID of the user.
         * @return int|null The total number of logs for the user, or null on error.
         */
        public function contarLogsUsuario($user_id) {
            if (!$this->conexion) return null;

            $consulta = "SELECT COUNT(*) AS total_logs FROM logs WHERE user_id = ?";
            $stmt = $this->conexion->prepare($consulta);

            if ($stmt) {
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $stmt->close();

                if ($resultado && $resultado->num_rows > 0) {
                    $fila = $resultado->fetch_assoc();
                    return (int) $fila['total_logs'];
                } else {
                    return 0; // User might not have any logs yet
                }
            } else {
                echo "Error al preparar la consulta para contar logs: " . $this->conexion->error;
                return null;
            }
        }

        /**
         * Gets the total duration of all logs for a specific user in hours.
         *
         * @param int $user_id The ID of the user.
         * @return float|null The total duration in hours, or null on error.
         */
        public function obtenerTotalHorasUsuario($user_id) {
            if (!$this->conexion) return null;

            $consulta = "SELECT SUM(duration) AS total_minutes FROM logs WHERE user_id = ?";
            $stmt = $this->conexion->prepare($consulta);

            if ($stmt) {
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $stmt->close();
    
                if ($resultado && $resultado->num_rows > 0) {
                    $fila = $resultado->fetch_assoc();
                    $totalMinutes = (int) $fila['total_minutes'];
    
                    if ($totalMinutes < 60) {
                        return $totalMinutes;
                    } else {
                        $totalHours = round($totalMinutes / 60, 2);
                        return $totalHours;
                    }
                } else {
                    return "0"; // User might not have any logs yet
                }
            } else {
                echo "Error al preparar la consulta para obtener la duración total: " . $this->conexion->error;
                return null;
            }
        }

        /**
         * Gets the total duration of all logs for a specific user in minutes.
         *
         * @param int $user_id The ID of the user.
         * @return int|null The total duration in minutes, or null on error.
         */
        public function obtenerTotalMinutosUsuario($user_id) {
            if (!$this->conexion) return null;

            $consulta = "SELECT SUM(duration) AS total_minutes FROM logs WHERE user_id = ?";
            $stmt = $this->conexion->prepare($consulta);

            if ($stmt) {
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $stmt->close();

                if ($resultado && $resultado->num_rows > 0) {
                    $fila = $resultado->fetch_assoc();
                    return (int) $fila['total_minutes'];
                } else {
                    return 0;
                }
            } else {
                echo "Error al preparar la consulta para obtener la duración total en minutos: " . $this->conexion->error;
                return null;
            }
        }
    
        public function __destruct() {
            if ($this->conexion) {
                $this->conexion->close();
            }
        }
    }
?>
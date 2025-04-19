<?php
    // Evitamos que se llame al fichero sin pasar por el controlador
	// if (!defined('CON_CONTROLADOR')) {
    //     // Matamos el proceso php
	// 	die('Error: No se permite el acceso directo a esta ruta');
	// }

    class SocialModel {
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
    
        public function followUser($followerId, $followedId) {
            if (!$this->conexion) return false;
    
            // Prevent a user from following themselves
            if ($followerId == $followedId) {
                return false;
            }
    
            // Check if the follow relationship already exists
            $checkSql = "SELECT * FROM followers WHERE follower_id = ? AND followed_id = ?";
            $checkStmt = $this->conexion->prepare($checkSql);
    
            if ($checkStmt) {
                $checkStmt->bind_param("ii", $followerId, $followedId);
                $checkStmt->execute();
                $result = $checkStmt->get_result();
                if ($result->num_rows > 0) {
                    $checkStmt->close();
                    return true; // Already following
                }
                $checkStmt->close();
            } else {
                echo "Error al preparar la consulta para verificar el seguimiento: " . $this->conexion->error;
                return false;
            }
    
            // Insert the follow relationship
            $insertSql = "INSERT INTO followers (follower_id, followed_id) VALUES (?, ?)";
            $insertStmt = $this->conexion->prepare($insertSql);
    
            if ($insertStmt) {
                $insertStmt->bind_param("ii", $followerId, $followedId);
                $insertResult = $insertStmt->execute();
                $insertStmt->close();
    
                if ($insertResult) {
                    // Optionally update the num_following count for the follower
                    $this->updateFollowingCount($followerId, 1);
                    // Optionally update the num_followers count for the followed user
                    $this->updateFollowersCount($followedId, 1);
                    return true;
                } else {
                    echo "Error al ejecutar la consulta para seguir al usuario: " . $this->conexion->error;
                    return false;
                }
            } else {
                echo "Error al preparar la consulta para seguir al usuario: " . $this->conexion->error;
                return false;
            }
        }
    
        private function updateFollowingCount($userId, $increment = 1) {
            if (!$this->conexion) return false;
            $sql = "UPDATE profile SET num_following = num_following + ? WHERE user_id = ?";
            $stmt = $this->conexion->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ii", $increment, $userId);
                $stmt->execute();
                $stmt->close();
                return true;
            } else {
                error_log("Error al actualizar el contador de 'siguiendo': " . $this->conexion->error);
                return false;
            }
        }
    
        private function updateFollowersCount($userId, $increment = 1) {
            if (!$this->conexion) return false;
            $sql = "UPDATE profile SET num_followers = num_followers + ? WHERE user_id = ?";
            $stmt = $this->conexion->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ii", $increment, $userId);
                $stmt->execute();
                $stmt->close();
                return true;
            } else {
                error_log("Error al actualizar el contador de 'seguidores': " . $this->conexion->error);
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
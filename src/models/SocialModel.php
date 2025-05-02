<?php
    namespace Sonia\LinguaLair\Models;

    class SocialModel {
        private $conexion;
    
        public function __construct($servidor, $usuario, $contrasenia, $base_datos) {
            $this->conexion = new  \mysqli($servidor, $usuario, $contrasenia, $base_datos);
    
            if ($this->conexion->connect_error) {
                die("Conexión fallida: " . $this->conexion->connect_error);
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

        public function unfollowUser($followerId, $followedId) {
            if (!$this->conexion) return false;
    
            $sql = "DELETE FROM followers WHERE follower_id = ? AND followed_id = ?";
            $stmt = $this->conexion->prepare($sql);
    
            if ($stmt) {
                $stmt->bind_param("ii", $followerId, $followedId);
                $result = $stmt->execute();
                $affectedRows = $stmt->affected_rows;
                $stmt->close();
    
                if ($result) {
                    // Opcionalmente actualizar los contadores
                    $this->updateFollowingCount($followerId, -1);
                    $this->updateFollowersCount($followedId, -1);
                    return $affectedRows > 0; // Devuelve true si se eliminó alguna fila
                } else {
                    error_log("Error al ejecutar la consulta para dejar de seguir al usuario: " . $this->conexion->error);
                    return false;
                }
            } else {
                error_log("Error al preparar la consulta para dejar de seguir al usuario: " . $this->conexion->error);
                return false;
            }
        }
    
        private function updateFollowingCount($userId, $increment) {
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
    
        private function updateFollowersCount($userId, $increment) {
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

        public function isFollowing($followerId, $followedId) {
            if (!$this->conexion) return false;
    
            $sql = "SELECT * FROM followers WHERE follower_id = ? AND followed_id = ?";
            $stmt = $this->conexion->prepare($sql);
    
            if ($stmt) {
                $stmt->bind_param("ii", $followerId, $followedId);
                $stmt->execute();
                $result = $stmt->get_result();
                $isFollowing = $result->num_rows > 0;
                $stmt->close();
                return $isFollowing;
            } else {
                echo "Error al preparar la consulta para verificar el seguimiento: " . $this->conexion->error;
                return false;
            }
        }

        public function getFollowingUsers($userId) {
            if (!$this->conexion) return false;
    
            $sql = "SELECT followed_id FROM followers WHERE follower_id = ?";
            $stmt = $this->conexion->prepare($sql);
    
            if ($stmt) {
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                $followingUsersIds = [];
                while ($row = $result->fetch_assoc()) {
                    $followingUsersIds[] = $row['followed_id'];
                }
                $stmt->close();
                return $followingUsersIds;
            } else {
                error_log("Error al preparar la consulta para obtener los usuarios seguidos: " . $this->conexion->error);
                return false;
            }
        }

        public function getLogsForUsers($userIds, $limit, $offset = 0) {
            if (!$this->conexion || empty($userIds)) {
                return [];
            }
    
            // Sanitize user IDs to prevent SQL injection
            $safeUserIds = array_map('intval', $userIds);
            $userIdsString = implode(',', $safeUserIds);
    
            $sql = "SELECT l.*,
                       u.nickname,
                       u.username,
                       p.profile_pic
                FROM logs l
                JOIN user u ON l.user_id = u.id
                JOIN profile p ON l.user_id = p.user_id
                WHERE l.user_id IN ($userIdsString)
                ORDER BY l.post_date DESC
                LIMIT ? OFFSET ?";
    
            $stmt = $this->conexion->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ii", $limit, $offset);
                $stmt->execute();
                $result = $stmt->get_result();
                $logs = [];
                while ($row = $result->fetch_assoc()) {
                    $logs[] = $row;
                }
                $stmt->close();
                return $logs;
            } else {
                error_log("Error al preparar la consulta de logs combinados: " . $this->conexion->error);
                return [];
            }
        }
    
        public function getTotalLogCountForUsers($userIds) {
            if (!$this->conexion || empty($userIds)) {
                return 0;
            }
    
            // Sanitize user IDs
            $safeUserIds = array_map('intval', $userIds);
            $userIdsString = implode(',', $safeUserIds);
    
            $sql = "SELECT COUNT(*) AS total
                    FROM logs
                    WHERE user_id IN ($userIdsString)";
    
            $result = $this->conexion->query($sql);
            if ($result && $row = $result->fetch_assoc()) {
                return $row['total'];
            } else {
                error_log("Error al obtener el total de logs combinados: " . $this->conexion->error);
                return 0;
            }
        }

        public function getLogsForUser($userId, $limit, $offset = 0) {
            if (!$this->conexion) return [];
            $sql = "SELECT l.*, u.nickname, u.username, p.profile_pic
                    FROM logs l
                    JOIN user u ON l.user_id = u.id
                    JOIN profile p ON l.user_id = p.user_id
                    WHERE l.user_id = ?
                    ORDER BY l.post_date DESC
                    LIMIT ? OFFSET ?";
            $stmt = $this->conexion->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("iii", $userId, $limit, $offset);
                $stmt->execute();
                $result = $stmt->get_result();
                $logs = [];
                while ($row = $result->fetch_assoc()) {
                    $logs[] = $row;
                }
                $stmt->close();
                return $logs;
            } else {
                error_log("Error al preparar la consulta de logs de usuario: " . $this->conexion->error);
                return [];
            }
        }
    
        public function getTotalLogCountForUser($userId) {
            if (!$this->conexion) return 0;
            $sql = "SELECT COUNT(*) AS total FROM logs WHERE user_id = ?";
            $stmt = $this->conexion->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    return $row['total'];
                }
                $stmt->close();
            } else {
                error_log("Error al obtener el total de logs de usuario: " . $this->conexion->error);
            }
            return 0;
        }

        public function buscarLogsPorDescripcion($texto) {
            if (!$this->conexion) return false;
    
            $texto = "%" . $this->conexion->real_escape_string($texto) . "%";
            $sql = "SELECT l.*, u.nickname, u.username, p.profile_pic
                    FROM logs l
                    JOIN user u ON l.user_id = u.id
                    JOIN profile p ON l.user_id = p.user_id
                    WHERE LOWER(l.description) LIKE ?";
            $stmt = $this->conexion->prepare($sql);
    
            if ($stmt) {
                $stmt->bind_param("s", $texto);
                $stmt->execute();
                $result = $stmt->get_result();
                $logsEncontrados = [];
                while ($row = $result->fetch_assoc()) {
                    $logsEncontrados[] = $row;
                }
                $stmt->close();
                return $logsEncontrados;
            } else {
                error_log("Error al preparar la consulta para buscar logs: " . $this->conexion->error);
                return false;
            }
        }

        // Para usar este método tendría que añadir algún filtro en la búsqueda de explore, 
        // porque este método busca los logs del usuario que escribas en @

        // public function exploreUsersLogs($texto) {
        //     if (!$this->conexion) return false;
    
        //     $texto = "%" . $this->conexion->real_escape_string($texto) . "%";
        //     $sql = "SELECT l.*, u.nickname, u.username, p.profile_pic
        //             FROM logs l
        //             JOIN user u ON l.user_id = u.id
        //             JOIN profile p ON l.user_id = p.user_id
        //             WHERE LOWER(u.username) LIKE ?";
        //     $stmt = $this->conexion->prepare($sql);
    
        //     if ($stmt) {
        //         $stmt->bind_param("s", $texto);
        //         $stmt->execute();
        //         $result = $stmt->get_result();
        //         $foundUsers = [];
        //         while ($row = $result->fetch_object()) {
        //             $foundUsers[] = $row;
        //         }
        //         $stmt->close();
        //         return $foundUsers;
        //     } else {
        //         error_log("Error al preparar la consulta para buscar logs: " . $this->conexion->error);
        //         return false;
        //     }
        // }

        public function exploreUsers($texto) {
            if (!$this->conexion) return false;
    
            $texto = "%" . $this->conexion->real_escape_string($texto) . "%";
            $sql = "SELECT u.*, p.profile_pic, p.bio
                    FROM user u
                    JOIN profile p ON u.id = p.user_id
                    WHERE LOWER(u.username) LIKE ?";
            $stmt = $this->conexion->prepare($sql);
    
            if ($stmt) {
                $stmt->bind_param("s", $texto);
                $stmt->execute();
                $result = $stmt->get_result();
                $foundUsers = [];
                while ($row = $result->fetch_object()) {
                    $foundUsers[] = $row;
                }
                $stmt->close();
                return $foundUsers;
            } else {
                error_log("Error al preparar la consulta para buscar logs: " . $this->conexion->error);
                return false;
            }
        }

        public function getEvents() {
            if (!$this->conexion) return null;
        
            $consulta = "SELECT * FROM events ORDER BY event_date DESC";
        
            $stmt = $this->conexion->prepare($consulta);
        
            if ($stmt) {
                $stmt->execute();
                $resultado = $stmt->get_result();
                $events = [];
                while ($event = $resultado->fetch_object()) {
                    $events[] = $event;
                }
                $stmt->close();
                return $events;
            } else {
                echo "Error al preparar la consulta para obtener eventos: " . $this->conexion->error;
                return null;
            }
        }

        public function getEvent($event_id) {
            if (!$this->conexion) return null;
        
            $consulta = "SELECT * FROM events WHERE id = ?";
        
            $stmt = $this->conexion->prepare($consulta);
        
            if ($stmt) {
                $stmt->bind_param("i", $event_id);
                $stmt->execute();
                $resultado = $stmt->get_result();
        
                if ($resultado->num_rows == 1) {
                    $event = $resultado->fetch_object();
                    $stmt->close();
                    return $event;
                } else {
                    $stmt->close();
                    return null; // No se encontró ningún evento con ese ID
                }
            } else {
                echo "Error al preparar la consulta para obtener el evento: " . $this->conexion->error;
                return null;
            }
        }

        private function updateAttendingEvent($event_id, $increment) {
            if (!$this->conexion) return false;
            $sql = "UPDATE events SET attending = attending + ? WHERE id = ?";
            $stmt = $this->conexion->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ii", $increment, $event_id);
                $stmt->execute();
                $stmt->close();
                return true;
            } else {
                error_log("Error al actualizar el contador de 'attending': " . $this->conexion->error);
                return false;
            }
        }

        public function isAttending($user_id, $event_id) {
            if (!$this->conexion) return false;
    
            $sql = "SELECT * FROM booking WHERE user_id = ? AND event_id = ?";
            $stmt = $this->conexion->prepare($sql);
    
            if ($stmt) {
                $stmt->bind_param("ii", $user_id, $event_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $isAttending = $result->num_rows > 0;
                $stmt->close();
                return $isAttending;
            } else {
                echo "Error al preparar la consulta para verificar el booking: " . $this->conexion->error;
                return false;
            }
        }

        public function bookEvent($user_id, $event_id) {
            if (!$this->conexion) return false;
    
            // Check if the booking relationship already exists
            $checkSql = "SELECT * FROM booking WHERE user_id = ? AND event_id = ?";
            $checkStmt = $this->conexion->prepare($checkSql);
    
            if ($checkStmt) {
                $checkStmt->bind_param("ii", $user_id, $event_id);
                $checkStmt->execute();
                $result = $checkStmt->get_result();
                if ($result->num_rows > 0) {
                    $checkStmt->close();
                    return true; // Already booked
                }
                $checkStmt->close();
            } else {
                echo "Error al preparar la consulta para verificar el booking: " . $this->conexion->error;
                return false;
            }
    
            // Insert the booking relationship
            $insertSql = "INSERT INTO booking (user_id, event_id) VALUES (?, ?)";
            $insertStmt = $this->conexion->prepare($insertSql);
    
            if ($insertStmt) {
                $insertStmt->bind_param("ii", $user_id, $event_id);
                $insertResult = $insertStmt->execute();
                $insertStmt->close();
    
                if ($insertResult) {
                    // Update the num_following count for the follower
                    $this->updateAttendingEvent($event_id, 1);
                    return true;
                } else {
                    echo "Error al ejecutar la consulta para inscribirse al evento: " . $this->conexion->error;
                    return false;
                }
            } else {
                echo "Error al preparar la consulta para inscribirse al evento: " . $this->conexion->error;
                return false;
            }
        }

        public function unbookEvent($user_id, $event_id) {
            if (!$this->conexion) return false;
    
            $sql = "DELETE FROM booking WHERE user_id = ? AND event_id = ?";
            $stmt = $this->conexion->prepare($sql);
    
            if ($stmt) {
                $stmt->bind_param("ii", $user_id, $event_id);
                $result = $stmt->execute();
                $affectedRows = $stmt->affected_rows;
                $stmt->close();
    
                if ($result) {
                    $this->updateAttendingEvent($event_id, -1);
                    return $affectedRows > 0; // Devuelve true si se eliminó alguna fila
                } else {
                    error_log("Error al ejecutar la consulta para eliminar inscripción al evento: " . $this->conexion->error);
                    return false;
                }
            } else {
                error_log("Error al preparar la consulta para eliminar inscripción al evento: " . $this->conexion->error);
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
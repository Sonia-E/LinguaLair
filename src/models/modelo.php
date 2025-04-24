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

        public function getUserByUsernameOrEmail($login_identifier) {
            if (!$this->conexion) return null;
    
            $consulta = "SELECT * FROM user
                         WHERE username = ? OR email = ?";
    
            $stmt = $this->conexion->prepare($consulta);
            if ($stmt) {
                $stmt->bind_param("ss", $login_identifier, $login_identifier);
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

        public function getUserLanguages($user_id) {
            if (!$this->conexion) return [];
    
            $consulta = "SELECT languages FROM profile WHERE user_id = ?";
            $stmt = $this->conexion->prepare($consulta);
    
            if ($stmt) {
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $resultado = $stmt->get_result();
                if ($row = $resultado->fetch_assoc()) {
                    $languagesString = $row['languages'];
                    // Dividir la cadena de idiomas por la coma y eliminar espacios en blanco
                    $languagesArray = array_map('trim', explode(',', $languagesString));
                    // Filtrar para eliminar cadenas vacías que puedan resultar de comas múltiples
                    return array_filter(array_unique($languagesArray));
                }
                $stmt->close();
            } else {
                echo "Error al preparar la consulta para obtener idiomas del perfil: " . $this->conexion->error;
            }
            return [];
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

        public function getLogsByLanguage(array $logs, $language) {
            return array_filter($logs, function ($log) use ($language) {
                return $log->language === $language;
            });
        }

        public function calculateLanguagePercentagesByDuration(array $logs) {
            $languageDurations = [];
            $totalDuration = 0;
    
            if (empty($logs)) {
                return [];
            }
    
            // Calcular la duración total y la duración por idioma
            foreach ($logs as $log) {
                $language = $log->language;
                $duration = intval($log->duration); // Asegurarse de que la duración sea un entero
                $totalDuration += $duration;
    
                if (isset($languageDurations[$language])) {
                    $languageDurations[$language] += $duration;
                } else {
                    $languageDurations[$language] = $duration;
                }
            }
    
            $languagePercentages = [];
            if ($totalDuration > 0) {
                foreach ($languageDurations as $language => $duration) {
                    $percentage = ($duration / $totalDuration) * 100;
                    $languagePercentages[$language] = round($percentage, 2);
                }
            }
    
            return $languagePercentages;
        }
    
        // Método combinado para obtener porcentajes de duración por idioma para un usuario
        public function getLanguagePercentagesByDurationForUser($user_id) {
            $userLogs = $this->getLogs($user_id);
            return $this->calculateLanguagePercentagesByDuration($userLogs);
        }

        // Calculo de porcentajes por número de logs totates: borrar si al final no hago nada con esto:
        // a lo mejor puedo reutilizarlo para sacar los logs totales de ese idioma para su pestaña específica
        // public function calculateLanguagePercentages(array $logs) {
        //     $languageCounts = [];
        //     $totalLogs = count($logs);
    
        //     if ($totalLogs === 0) {
        //         return $languageCounts; // Return empty array if no logs
        //     }
    
        //     foreach ($logs as $log) {
        //         $language = $log->language;
        //         if (isset($languageCounts[$language])) {
        //             $languageCounts[$language]++;
        //         } else {
        //             $languageCounts[$language] = 1;
        //         }
        //     }
    
        //     $languagePercentages = [];
        //     foreach ($languageCounts as $language => $count) {
        //         $percentage = ($count / $totalLogs) * 100;
        //         $languagePercentages[$language] = round($percentage, 2); // Round to 2 decimal places
        //     }
    
        //     return $languagePercentages;
        // }
    
        // public function getLanguagePercentagesForUser($user_id) {
        //     $userLogs = $this->getLogs($user_id);
        //     return $this->calculateLanguagePercentages($userLogs);
        // }
    
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

        public function addExperience($user_id, $experience_gain) {
            if (!$this->conexion) return false;
    
            $consulta = "UPDATE profile SET experience = experience + ? WHERE user_id = ?";
            $stmt = $this->conexion->prepare($consulta);
    
            if ($stmt) {
                $stmt->bind_param("ii", $experience_gain, $user_id);
                $stmt->execute();
                $stmt->close();
                return true;
            } else {
                echo "Error al preparar la consulta para añadir experiencia: " . $this->conexion->error;
                return false;
            }
        }
    
        public function getProfileData($user_id) {
            if (!$this->conexion) return null;
    
            $consulta = "SELECT level, experience FROM profile WHERE user_id = ?";
            $stmt = $this->conexion->prepare($consulta);
    
            if ($stmt) {
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $resultado = $stmt->get_result();
                if ($perfil = $resultado->fetch_object()) {
                    $stmt->close();
                    return $perfil;
                }
                $stmt->close();
            } else {
                echo "Error al preparar la consulta para obtener datos del perfil: " . $this->conexion->error;
            }
            return null;
        }
    
        public function levelUp($user_id) {
            if (!$this->conexion) return false;
    
            $consulta = "UPDATE profile SET level = level + 1, experience = 0 WHERE user_id = ?";
            $stmt = $this->conexion->prepare($consulta);
    
            if ($stmt) {
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $stmt->close();
                return true;
            } else {
                echo "Error al preparar la consulta para subir de nivel: " . $this->conexion->error;
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

        public function addNewUser($username, $nickname, $password, $email, $country) {
            if (!$this->conexion) return false;
    
            $consulta = "INSERT INTO user (username, nickname, password, email, country) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conexion->prepare($consulta);
    
            if ($stmt) {
                $stmt->bind_param("sssss", $username, $nickname, $password, $email, $country);
                $resultado = $stmt->execute();
                $stmt->close();
                return $resultado;
            } else {
                echo "Error al preparar la consulta para insertar nuevo usuario: " . $this->conexion->error;
                return false;
            }
        }

        // ------------Profile

        public function addNewProfile($user_id, $bio, $native_lang, $languages, $is_public, $profile_pic, $bg_pic, $game_roles = 'Novice') {
            if (!$this->conexion) return false;
    
            $consulta = "INSERT INTO profile (user_id, bio, native_lang, languages, is_public, profile_pic, bg_pic, game_roles) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"; //cambiar active a public
            $stmt = $this->conexion->prepare($consulta);
    
            if ($stmt) {
                $stmt->bind_param("isssisss", $user_id, $bio, $native_lang, $languages, $is_public, $profile_pic, $bg_pic, $game_roles);
                $resultado = $stmt->execute();
                $stmt->close();
                return $resultado;
            } else {
                echo "Error al preparar la consulta para insertar nuevo perfil: " . $this->conexion->error;
                return false;
            }
        }

        public function updateNickname($user_id, $nickname) {
            if (!$this->conexion) return false;
    
            $consulta = "UPDATE user SET nickname = ? WHERE id = ?";
            $stmt = $this->conexion->prepare($consulta);
    
            if ($stmt) {
                $stmt->bind_param("si", $nickname, $user_id);
                $stmt->execute();
                $stmt->close();
                return true;
            } else {
                echo "Error al preparar la consulta para cambiar el nickname: " . $this->conexion->error;
                return false;
            }
        }

        public function updateProfileNO($user_id, $bio = null, $native_lang = null, $languages = null, $fluent = null, $learning = null, $on_hold = null, $dabbling = null, 
        $dark_mode = null, $is_public = null, $profile_pic = null, $bg_pic = null) {
            //
        }

        public function updateProfile($userId, $bio = null, $nativeLang = null, $languages = null, $fluent = null, $learning = null, $onHold = null, $dabbling = null, 
        $level = null, $experience = null, $darkMode = null, $numFollowers = null, $numFollowing = null, $isPublic = null, $profilePic = null, 
        $bg_pic = null, $gameRoles = null) {
            if (!$this->conexion) return false;
    
            $sql = "UPDATE profile SET ";
            $params = [];
            $types = "";
            $conditions = [];
    
            if ($bio !== null) {
                $conditions[] = "bio = ?";
                $params[] = $bio;
                $types .= "s";
            }
            if ($nativeLang !== null) {
                $conditions[] = "native_lang = ?";
                $params[] = $nativeLang;
                $types .= "s";
            }
            if ($languages !== null) {
                $conditions[] = "languages = ?";
                $params[] = $languages;
                $types .= "s";
            }
            if ($fluent !== null) {
                $conditions[] = "fluent = ?";
                $params[] = $fluent;
                $types .= "s";
            }
            if ($learning !== null) {
                $conditions[] = "learning = ?";
                $params[] = $learning;
                $types .= "s";
            }
            if ($onHold !== null) {
                $conditions[] = "on_hold = ?";
                $params[] = $onHold;
                $types .= "s";
            }
            if ($dabbling !== null) {
                $conditions[] = "dabbling = ?";
                $params[] = $dabbling;
                $types .= "s";
            }
            if ($level !== null) {
                $conditions[] = "level = ?";
                $params[] = $level;
                $types .= "i";
            }
            if ($experience !== null) {
                $conditions[] = "experience = ?";
                $params[] = $experience;
                $types .= "i";
            }
            if ($darkMode !== null) {
                $conditions[] = "dark_mode = ?";
                $params[] = $darkMode;
                $types .= "i";
            }
            if ($numFollowers !== null) {
                $conditions[] = "num_followers = ?";
                $params[] = $numFollowers;
                $types .= "i";
            }
            if ($numFollowing !== null) {
                $conditions[] = "num_following = ?";
                $params[] = $numFollowing;
                $types .= "i";
            }
            if ($isPublic !== null) {
                $conditions[] = "is_public = ?";
                $params[] = $isPublic;
                $types .= "i";
            }
            if ($profilePic !== null) {
                $conditions[] = "profile_pic = ?";
                $params[] = $profilePic;
                $types .= "s";
            }
            if ($bg_pic !== null) {
                $conditions[] = "bg_pic = ?";
                $params[] = $bg_pic;
                $types .= "s";
            }
            if ($gameRoles !== null) {
                $conditions[] = "game_roles = ?";
                $params[] = $gameRoles;
                $types .= "s";
            }
    
            if (empty($conditions)) {
                return true; // No data to update
            }
    
            $sql .= implode(", ", $conditions);
            $sql .= " WHERE user_id = ?";
            $params[] = $userId;
            $types .= "i";
    
            $stmt = $this->conexion->prepare($sql);
    
            if ($stmt) {
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $affectedRows = $stmt->affected_rows;
                $stmt->close();
                return $affectedRows > 0;
            } else {
                echo "Error al preparar la consulta para actualizar el perfil: " . $this->conexion->error;
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
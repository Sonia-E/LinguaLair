<?php
    namespace Sonia\LinguaLair\Models;

    class Modelo {
        private $conexion;
    
        public function __construct($servidor, $usuario, $contrasenia, $base_datos) {
            $this->conexion = new \mysqli($servidor, $usuario, $contrasenia, $base_datos);
    
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

        public function getLanguageTypes() {
            if (!$this->conexion) return null;
        
            $consulta = "SHOW COLUMNS FROM logs LIKE 'type'";
        
            $stmt = $this->conexion->prepare($consulta);
        
            if ($stmt) {
                $stmt->execute();
                $resultado = $stmt->get_result();
                $stmt->close();
        
                if ($resultado && $resultado->num_rows > 0) {
                    $fila = $resultado->fetch_assoc();
                    $enum_definition = $fila['Type'];
        
                    // Extraer los valores del ENUM
                    preg_match('/enum\((.*?)\)/i', $enum_definition, $matches);
        
                    if (isset($matches[1])) {
                        $values = str_getcsv($matches[1], ',', "'");
                        return $values;
                    }
                }
            } else {
                echo "Error al preparar la consulta para obtener la definición del ENUM de 'type': " . $this->conexion->error;
                return null;
            }
        
            return []; // Devolver un array vacío si no se encuentra la definición del ENUM
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

                if ($stmt->affected_rows > 0) {
                    $stmt->close();
                    $this->updateGameRole($user_id);
                    //Obtener el nivel actualizado para retornarlo
                    $query = "SELECT level FROM profile WHERE user_id = ?";
                    $stmt = $this->conexion->prepare($query);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();
                    $stmt->close();
                    return $row["level"];
                } else {
                    $stmt->close();
                    return false;
                }
            } else {
                echo "Error al preparar la consulta para subir de nivel: " . $this->conexion->error;
                return false;
            }
        }

        public function getExcessExperience($user_id, $experiencia_ganada) {
            if (!$this->conexion) return false;

            // Obtener la experiencia actual del usuario
            $query = "SELECT experience FROM profile WHERE user_id = ?";
            $stmt = $this->conexion->prepare($query);
            if (!$stmt) {
                echo "Error al preparar la consulta: " . $this->conexion->error;
                return false;
            }
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();

            if (!$row) {
                echo "Error: No se encontró el usuario con ID " . $user_id;
                return false;
            }

            $experiencia_actual = $row['experience'];
            $nueva_experiencia = $experiencia_actual + $experiencia_ganada;

            // Calcular el excedente de experiencia
            if ($nueva_experiencia >= 100) {
                $exceso_experiencia = $nueva_experiencia - 100;
            } else {
                $exceso_experiencia = 0; // Importante: manejar el caso de no excedente
            }

            return $exceso_experiencia;
        }

        public function obtenerRolUsuario($user_id) {
            if (!$this->conexion) return false;
            
            $query = "SELECT game_roles FROM profile WHERE user_id = ?";
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            if ($row) {
                return $row['game_roles'];
            } else {
                return null; // O un valor por defecto, como 'No Role'
            }
        }

        public function getCurrentGameRole($user_id) {
            if (!$this->conexion) {
                echo "Error: No hay conexión a la base de datos.";
                return null;
            }

            $query = "SELECT game_roles FROM profile WHERE user_id = ?";
            $stmt = $this->conexion->prepare($query);

            if (!$stmt) {
                echo "Error al preparar la consulta: " . $this->conexion->error;
                return null;
            }

            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();

            if ($row) {
                return $row['game_roles']; // Retorna el rol actual de la base de datos
            } else {
                return null; // Retorna null si no se encuentra el usuario o no tiene rol
            }
        }

        private function updateGameRole($user_id) {
            if (!$this->conexion) return false;

            // Primero, obtener el nivel actual del usuario
            $query = "SELECT level FROM profile WHERE user_id = ?";
            $stmt = $this->conexion->prepare($query);

            if (!$stmt) {
                echo "Error al preparar la consulta para obtener el nivel: " . $this->conexion->error;
                return false;
            }

            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();

            if (!$row) {
                echo "Error: No se encontró el usuario con ID " . $user_id;
                return false;
            }
            $nivel_actual = $row['level'];


            // Calcular el nuevo rol basado en el nivel
            $nuevo_rol = $this->calcularRolPorNivel($nivel_actual);

            if ($nuevo_rol) {
                // Actualizar el rol del usuario en la tabla profile
                $update_query = "UPDATE profile SET game_roles = ? WHERE user_id = ?";
                $update_stmt = $this->conexion->prepare($update_query);

                if (!$update_stmt) {
                    echo "Error al preparar la consulta para actualizar el rol: " . $this->conexion->error;
                    return false;
                }

                $update_stmt->bind_param("si", $nuevo_rol, $user_id);
                if ($update_stmt->execute()) {
                    // echo "Rol de usuario actualizado a: " . $nuevo_rol . "<br>";
                    $update_stmt->close();
                    return true;
                } else {
                    // echo "Error al actualizar el rol del usuario: " . $update_stmt->error . "<br>";
                    $update_stmt->close();
                    return false;
                }
            }
            return true; //Si no hay nuevo rol, retorna true para no detener el proceso.
        }

        private function calcularRolPorNivel($nivel) {
            // Mapeo de niveles a roles (ajusta esto según tus necesidades)
            $roles = [
                1 => 'Novice',
                6 => 'Apprentice',     // Cambia de 5 a 6 para que sea *después* de los primeros 5 niveles
                11 => 'Amateur',
                16 => 'Journeyman',
                21 => 'Adept',
                26 => 'Ace',
                31 => 'Expert',
                36 => 'Exemplar',
                41 => 'Mentor',
                46 => 'Master',
                51 => 'Grandmaster',
            ];

            // Encuentra el rol más alto que el usuario ha alcanzado
            $rol_asignado = null;
            foreach ($roles as $nivel_rol => $nombre_rol) {
                if ($nivel >= $nivel_rol) {
                    $rol_asignado = $nombre_rol;
                } else {
                    break; // Importante: deja de buscar cuando encuentres un nivel más alto
                }
            }
            return $rol_asignado;
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

        public function getUserAchievements($userId) {
            if (!$this->conexion) return false;
    
            $sql = "SELECT a.*
                    FROM user_achievements ua
                    JOIN achievements a ON ua.achievement_id = a.id
                    WHERE ua.user_id = ?";
    
            $stmt = $this->conexion->prepare($sql);
    
            if ($stmt) {
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                $achievements = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                return $achievements;
            } else {
                echo "Error al preparar la consulta: " . $this->conexion->error;
                return false;
            }
        }

        public function getUserLogPostDates($userId) {
            if (!$this->conexion) return false;
    
            $sql = "SELECT DISTINCT DATE(post_date) AS post_day
                    FROM logs
                    WHERE user_id = ?
                    ORDER BY post_date ASC";
    
            $stmt = $this->conexion->prepare($sql);
    
            if ($stmt) {
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                $postDates = [];
                while ($row = $result->fetch_assoc()) {
                    $postDates[] = $row['post_day'];
                }
                $stmt->close();
                return $postDates;
            } else {
                return false;
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

        public function updateProfile(
            $userId,
            $bio = null,
            $nativeLang = null,
            $languages = null,
            $fluent = null,
            $learning = null,
            $onHold = null,
            $dabbling = null,
            $future = null,
            $level = null,
            $experience = null,
            $darkMode = null,
            $numFollowers = null,
            $numFollowing = null,
            $isPublic = null,
            $profilePic = null,
            $bg_pic = null,
            $gameRoles = null
        ) {
            if (!$this->conexion) {
                return false;
            }

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
            if ($future !== null) {
                $conditions[] = "future = ?";
                $params[] = $future;
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

            $sql .= implode(", ", $conditions);
            $sql .= " WHERE user_id = ?";
            $params[] = $userId;
            $types .= "i";

            $stmt = $this->conexion->prepare($sql);

            if ($stmt) {
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $error = $stmt->error; // Capturar el error antes de cerrar la sentencia
                $stmt->close();
                if ($error) { // Comprobar si hubo un error
                    echo "Error al actualizar el perfil: " . $error;
                    return false; // Devolver false en caso de error
                }
                return true; // Devolver true si la ejecución fue exitosa, incluso si no se modificaron filas
            } else {
                echo "Error al preparar la consulta para actualizar el perfil: " . $this->conexion->error;
                return false;
            }
        }




        public function findLogByUsernameAndId($username, $logId) {
            if (!$this->conexion) {
                return false;
            }
    
            $sql = "SELECT *
                    FROM logs l
                    JOIN user u ON l.user_id = u.id
                    WHERE u.username = ? AND l.id = ?";
    
            $stmt = $this->conexion->prepare($sql);
    
            if ($stmt) {
                $stmt->bind_param("si", $username, $logId);
                $stmt->execute();
                $result = $stmt->get_result();
                $log = $result->fetch_assoc();
                $stmt->close();
                return $log;
            } else {
                return false; // Error al preparar la consulta
            }
        }

        public function deleteLogById($logId) {
            if (!$this->conexion) {
                return false;
            }
    
            $sql = "DELETE FROM logs WHERE id = ?";
            $stmt = $this->conexion->prepare($sql);
    
            if ($stmt) {
                $stmt->bind_param("i", $logId);
                $result = $stmt->execute();
                $stmt->close();
                return $result; // Retorna true en caso de éxito, false en caso de error
            } else {
                return false; // Error al preparar la consulta
            }
        }
    
        public function __destruct() {
            if ($this->conexion) {
                $this->conexion->close();
            }
        }
    }
?>
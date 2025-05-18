<?php
    namespace Sonia\LinguaLair\Models;

    class StatsModel {
        private $conexion;
        private $modelo;
    
        public function __construct($servidor, $usuario, $contrasenia, $base_datos, $modelo = null) {
            $this->conexion = new \mysqli($servidor, $usuario, $contrasenia, $base_datos);
            $this->modelo = $modelo;
    
            if ($this->conexion->connect_error) {
                die("Conexión fallida: " . $this->conexion->connect_error);
            } else {
                $this->conexion->set_charset("utf8");
            }
        }
    
        public function obtenerTotalHorasPorIdioma($user_id, $language) {
            if (!$this->conexion) return null;
    
            $consulta = "SELECT SUM(duration) AS total_minutes
                         FROM logs
                         WHERE user_id = ? AND language = ?";
    
            $stmt = $this->conexion->prepare($consulta);
    
            if ($stmt) {
                $stmt->bind_param("is", $user_id, $language);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $stmt->close();
    
                if ($resultado && $resultado->num_rows > 0) {
                    $fila = $resultado->fetch_assoc();
                    $totalMinutes = (int) $fila['total_minutes'];
    
                    if ($totalMinutes < 60) {
                        $totalMinutes = $fila['total_minutes'] . " minutes";
                        return $totalMinutes;
                    } else {
                        $totalHours = round($totalMinutes / 60, 2);
                        return $totalHours;
                    }
                } else {
                    return "0"; // El usuario no tiene logs en este idioma
                }
            } else {
                echo "Error al preparar la consulta para obtener la duración por idioma: " . $this->conexion->error;
                return null;
            }
        }

        public function obtenerSoloHorasPorIdioma($user_id, $language) {
            if (!$this->conexion) return null;
    
            $consulta = "SELECT DATE(log_date) AS study_date,
                                SUM(duration) AS total_minutes
                         FROM logs
                         WHERE user_id = ? AND language = ?
                         GROUP BY DATE(log_date)
                         ORDER BY DATE(log_date) ASC";
    
            $stmt = $this->conexion->prepare($consulta);
    
            if ($stmt) {
                $stmt->bind_param("is", $user_id, $language);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $stmt->close();
    
                $horasPorDia = [];
                while ($fila = $resultado->fetch_assoc()) {
                    $studyDate = $fila['study_date'];
                    $totalMinutes = (int) $fila['total_minutes'];
    
                    $horasPorDia[$studyDate] = round($totalMinutes / 60, 2); // Almacenamos en horas
                }
                return $horasPorDia;
            } else {
                echo "Error al preparar la consulta para obtener las horas por día por idioma: " . $this->conexion->error;
                return null;
            }
        }

        public function obtenerMediaHorasPorDiaPorIdioma($user_id, $language) {
            if (!$this->conexion) return null;
    
            $consulta = "SELECT AVG(duration / 60) AS average_hours_per_day
                         FROM logs
                         WHERE user_id = ? AND language = ?";
    
            $stmt = $this->conexion->prepare($consulta);
    
            if ($stmt) {
                $stmt->bind_param("is", $user_id, $language);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $stmt->close();
    
                if ($resultado && $resultado->num_rows > 0) {
                    $fila = $resultado->fetch_assoc();
                    return round((float) $fila['average_hours_per_day'], 2);
                } else {
                    return 0; // El usuario no tiene logs en este idioma
                }
            } else {
                echo "Error al preparar la consulta para obtener la media de horas por día por idioma: " . $this->conexion->error;
                return null;
            }
        }

        public function obtenerTotalLogsPorIdioma($user_id, $language) {
            if (!$this->conexion) return null;
    
            $consulta = "SELECT COUNT(*) AS total_logs
                         FROM logs
                         WHERE user_id = ? AND language = ?";
    
            $stmt = $this->conexion->prepare($consulta);
    
            if ($stmt) {
                $stmt->bind_param("is", $user_id, $language);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $stmt->close();
    
                if ($resultado && $resultado->num_rows > 0) {
                    $fila = $resultado->fetch_assoc();
                    return (int) $fila['total_logs'];
                } else {
                    return 0; // El usuario no tiene logs en este idioma
                }
            } else {
                echo "Error al preparar la consulta para obtener el total de logs por idioma: " . $this->conexion->error;
                return null;
            }
        }

        public function calculateLanguageStreak($userId, $language) {
            $postDates = $this->modelo->getUserLogPostDates($userId, $language);
    
            if (!$postDates) {
                return 0; // El usuario no tiene logs en este idioma
            }
    
            $streak = 0;
            $maxStreak = 0;
            $previousDate = null;
            $today = new \DateTime();
            $yesterday = (new \DateTime())->modify('-1 day')->format('Y-m-d');
    
            foreach ($postDates as $date) {
                $currentDate = new \DateTime($date);
                $currentDateFormatted = $currentDate->format('Y-m-d');
    
                if ($previousDate === null) {
                    // Primer día
                    if ($currentDateFormatted === $yesterday || $currentDateFormatted === $today->format('Y-m-d')) {
                        $streak = 1;
                    } else {
                        $streak = 0; // Si el primer log no es de ayer o hoy, la racha actual es 0
                    }
                } else {
                    $diff = $previousDate->diff($currentDate);
                    if ($diff->days === 1) {
                        // Día consecutivo
                        $streak++;
                    } else if ($diff->days > 1) {
                        // Hubo una interrupción
                        $maxStreak = max($maxStreak, $streak);
                        $streak = ($currentDateFormatted === $yesterday || $currentDateFormatted === $today->format('Y-m-d')) ? 1 : 0;
                    }
                }
                $previousDate = $currentDate;
                $maxStreak = max($maxStreak, $streak);
            }
    
            return $maxStreak;
        }

        public function obtenerHorasPorDiaPorIdioma($user_id, $language) {
            if (!$this->conexion) return null;
    
            $consulta = "SELECT DATE(post_date) AS study_date,
                                SUM(duration) AS total_minutes
                         FROM logs
                         WHERE user_id = ? AND language = ?
                         GROUP BY DATE(post_date)
                         ORDER BY DATE(post_date) ASC";
    
            $stmt = $this->conexion->prepare($consulta);
    
            if ($stmt) {
                $stmt->bind_param("is", $user_id, $language);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $stmt->close();
    
                $horasPorDia = [];
                while ($fila = $resultado->fetch_assoc()) {
                    $studyDate = $fila['study_date'];
                    $totalMinutes = (int) $fila['total_minutes'];
    
                    if ($totalMinutes < 60) {
                        $horasPorDia[$studyDate] = $totalMinutes; // Almacenamos en minutos si es menos de una hora
                    } else {
                        $horasPorDia[$studyDate] = round($totalMinutes / 60, 2); // Almacenamos en horas
                    }
                }
                return $horasPorDia;
            } else {
                echo "Error al preparar la consulta para obtener las horas por día por idioma: " . $this->conexion->error;
                return null;
            }
        }

        public function obtenerHorasPorTipoPorIdioma($user_id, $language) {
            if (!$this->conexion) return null;
        
            $consulta = "SELECT DATE(log_date) AS study_date,
                                    type,
                                    SUM(duration) AS total_minutes
                             FROM logs
                             WHERE user_id = ? AND language = ?
                             GROUP BY DATE(log_date), type
                             ORDER BY DATE(log_date) ASC, type ASC";
        
            $stmt = $this->conexion->prepare($consulta);
        
            if ($stmt) {
                $stmt->bind_param("is", $user_id, $language);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $stmt->close();
        
                $horasPorTipoPorDia = [];
                while ($fila = $resultado->fetch_assoc()) {
                    $studyDate = $fila['study_date'];
                    $type = $fila['type'];
                    $totalMinutes = (int) $fila['total_minutes'];
                    $totalHours = round($totalMinutes / 60, 2); // Convertimos a horas
        
                    if (!isset($horasPorTipoPorDia[$type])) {
                        $horasPorTipoPorDia[$type] = [];
                    }
                    $horasPorTipoPorDia[$type][$studyDate] = $totalHours;
                }
                return $horasPorTipoPorDia;
            } else {
                echo "Error al preparar la consulta para obtener las horas por tipo por idioma: " . $this->conexion->error;
                return null;
            }
        }

        public function obtenerEstadisticasPorIdioma($user_id, $language) {
            $estadisticas = [];
    
            // All tab
            $languageTotalHours = $this->obtenerTotalHorasPorIdioma($user_id, $language);
            $languageTotalHoursDay = $this->obtenerMediaHorasPorDiaPorIdioma($user_id, $language);
            $languageTotalLogs = $this->obtenerTotalLogsPorIdioma($user_id, $language);
            $languageDayStreak = $this->calculateLanguageStreak($user_id, $language);
            $languageDayStreak = $this->calculateLanguageStreak($user_id, $language);
            $languageTotalHoursInDay = $this->obtenerHorasPorDiaPorIdioma($user_id, $language);
            $languageSoloHoras = $this->obtenerSoloHorasPorIdioma($user_id, $language);

            // Language tab
            $languageTypePercentages = $this->getTypePercentages($user_id, $language);
            $typeHours = $this->obtenerHorasPorTipoPorIdioma($user_id, $language);
    
            $estadisticas['total_horas'] = $languageTotalHours;
            $estadisticas['horas_por_dia'] = $languageTotalHoursDay;
            $estadisticas['total_logs'] = $languageTotalLogs;
            $estadisticas['day_streak'] = $languageDayStreak;
            $estadisticas['horas_al_dia'] = $languageTotalHoursInDay;
            $estadisticas['solo_horas'] = $languageSoloHoras;
            $estadisticas['type_percentages'] = $languageTypePercentages;
            $estadisticas['types_hours'] = $typeHours;
    
            return $estadisticas;
        }
    
        public function __destruct() {
            if ($this->conexion) {
                $this->conexion->close();
            }
        }

        public function calculateLogTypePercentagesByLanguage(array $logs, string $language): array
        {
            $typeHours = [];
            $totalHours = 0;

            if (empty($logs)) {
                return [];
            }

            // Filtramos los logs por el idioma especificado
            $languageLogs = array_filter($logs, function ($log) use ($language) {
                return $log->language === $language;
            });

            // Sumamos las horas (calculadas desde minutos) por tipo para el idioma especificado
            foreach ($languageLogs as $log) {
                $type = $log->type;
                $durationInMinutes = (int) $log->duration;
                $hours = $durationInMinutes / 60; // Convertimos minutos a horas

                $totalHours += $hours;

                if (isset($typeHours[$type])) {
                    $typeHours[$type] += $hours;
                } else {
                    $typeHours[$type] = $hours;
                }
            }

            $typePercentages = [];
            if ($totalHours > 0) {
                foreach ($typeHours as $type => $hours) {
                    $percentage = ($hours / $totalHours) * 100;
                    $typePercentages[$type] = round($percentage, 2);
                }
            }

            return $typePercentages;
        }

        public function getTypePercentages($user_id, $language) {
            $userLogs = $this->modelo->getLogs($user_id);
            return $this->calculateLogTypePercentagesByLanguage($userLogs, $language);
        }

        public function getUserAchievements($user_id) {
            if (!$this->conexion) return null;
        
            $consulta = "SELECT
                                a.id,
                                a.name,
                                a.description,
                                a.icon,
                                a.type,
                                a.level,
                                ua.unlock_date
                            FROM achievements a
                            INNER JOIN user_achievements ua ON a.id = ua.achievement_id
                            WHERE ua.user_id = ?";
        
            $stmt = $this->conexion->prepare($consulta);
        
            if ($stmt) {
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $achievements = [];
                while ($achievement = $resultado->fetch_object()) {
                    $achievements[] = $achievement;
                }
                $stmt->close();
                return $achievements;
            } else {
                echo "Error al preparar la consulta para obtener los achievements del usuario: " . $this->conexion->error;
                return null;
            }
        }

        public function getUnlockedAchievements($user_id) {
            if (!$this->conexion) return null;
        
            $consulta = "SELECT
                                a.id,
                                a.name,
                                a.description,
                                a.icon,
                                a.type,
                                a.level
                            FROM achievements a
                            WHERE a.id NOT IN (
                                SELECT achievement_id
                                FROM user_achievements
                                WHERE user_id = ?
                            )";
        
            $stmt = $this->conexion->prepare($consulta);
        
            if ($stmt) {
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $unlockedAchievements = [];
                while ($achievement = $resultado->fetch_object()) {
                    $unlockedAchievements[] = $achievement;
                }
                $stmt->close();
                return $unlockedAchievements;
            } else {
                echo "Error al preparar la consulta para obtener los achievements no desbloqueados: " . $this->conexion->error;
                return null;
            }
        }

        public function checkIfUserHasAchievement($user_id, $achievement_id) {
            if (!$this->conexion) return null;
        
            $consulta = "SELECT unlock_date
                            FROM user_achievements
                            WHERE user_id = ? AND achievement_id = ?";
        
            $stmt = $this->conexion->prepare($consulta);
        
            if ($stmt) {
                $stmt->bind_param("ii", $user_id, $achievement_id);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $row = $resultado->fetch_object();
                $stmt->close();
                return $row ? $row->unlock_date : null;
            } else {
                echo "Error al preparar la consulta para verificar si el usuario tiene el achievement: " . $this->conexion->error;
                return null;
            }
        }

        public function getAchievementId($type, $level): ?int
        {
            if (!$this->conexion) {
                return null;
            }

            $consulta = "SELECT id
                            FROM achievements
                            WHERE type = ? AND level = ?";

            $stmt = $this->conexion->prepare($consulta);

            if ($stmt) {
                $stmt->bind_param("ss", $type, $level);
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($resultado && $resultado->num_rows > 0) {
                    $row = $resultado->fetch_object();
                    $stmt->close();
                    return (int) $row->id;
                } else {
                    $stmt->close();
                    return null; // No se encontró ningún achievement con ese tipo y nivel
                }
            } else {
                echo "Error al preparar la consulta para obtener el ID del achievement: " . $this->conexion->error;
                return null;
            }
        }
        
        public function unlockAchievement($user_id, $achievement_id) {
            if (!$this->conexion) return false;
        
            $consulta = "INSERT INTO user_achievements (user_id, achievement_id, unlock_date)
                            VALUES (?, ?, NOW())";
        
            $stmt = $this->conexion->prepare($consulta);
        
            if ($stmt) {
                $stmt->bind_param("ii", $user_id, $achievement_id);
                $stmt->execute();
                $affectedRows = $stmt->affected_rows;
                $stmt->close();
                return $affectedRows > 0;
            } else {
                echo "Error al preparar la consulta para desbloquear el achievement: " . $this->conexion->error;
                return false;
            }
        }

        public function getAchievementById(int $achievementId): ?object
        {
            if (!$this->conexion) {
                return null;
            }

            $consulta = "SELECT
                                id,
                                name,
                                description,
                                icon,
                                type,
                                level
                            FROM achievements
                            WHERE id = ?";

            $stmt = $this->conexion->prepare($consulta);

            if ($stmt) {
                $stmt->bind_param("i", $achievementId);
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($resultado && $resultado->num_rows > 0) {
                    $achievement = $resultado->fetch_object();
                    $stmt->close();
                    return $achievement;
                } else {
                    $stmt->close();
                    return null; // No se encontró ningún achievement con ese ID
                }
            } else {
                echo "Error al preparar la consulta para obtener el achievement por ID: " . $this->conexion->error;
                return null;
            }
        }

        public function geDatesLogsByType($user_id, $log_type) {
            if (!$this->conexion) return null;
        
            $consulta = "SELECT DATE(log_date) as log_date
                            FROM logs 
                            WHERE user_id = ? AND type = ?
                            ORDER BY log_date DESC";
        
            $stmt = $this->conexion->prepare($consulta);
        
            if ($stmt) {
                $stmt->bind_param("is", $user_id, $log_type);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $dates = [];
                while ($fila = $resultado->fetch_object()) {
                    $dates[] = $fila->log_date;
                }
                $stmt->close();
                return $dates;
            } else {
                echo "Error al preparar la consulta para obtener fechas de logs por tipo: " . $this->conexion->error;
                return null;
            }
        }

    }
?>
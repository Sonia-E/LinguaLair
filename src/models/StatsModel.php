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
    
                    $horasPorDia[$studyDate] = round($totalMinutes / 60, 2); // Almacenar en horas
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
                        $horasPorDia[$studyDate] = $totalMinutes; // Almacenar en minutos si es menos de una hora
                    } else {
                        $horasPorDia[$studyDate] = round($totalMinutes / 60, 2); // Almacenar en horas
                    }
                }
                return $horasPorDia;
            } else {
                echo "Error al preparar la consulta para obtener las horas por día por idioma: " . $this->conexion->error;
                return null;
            }
        }


        public function obtenerEstadisticasPorIdioma($user_id, $language) {
            $estadisticas = [];
    
            $languageTotalHours = $this->obtenerTotalHorasPorIdioma($user_id, $language);
            $languageTotalHoursDay = $this->obtenerMediaHorasPorDiaPorIdioma($user_id, $language);
            $languageTotalLogs = $this->obtenerTotalLogsPorIdioma($user_id, $language);
            $languageDayStreak = $this->calculateLanguageStreak($user_id, $language);
            $languageDayStreak = $this->calculateLanguageStreak($user_id, $language);
            $languageTotalHoursInDay = $this->obtenerHorasPorDiaPorIdioma($user_id, $language);
            $languageSoloHoras = $this->obtenerSoloHorasPorIdioma($user_id, $language);
            $languageTypePercentages = $this->getTypePercentages($user_id, $language);
    
            $estadisticas['total_horas'] = $languageTotalHours;
            $estadisticas['horas_por_dia'] = $languageTotalHoursDay;
            $estadisticas['total_logs'] = $languageTotalLogs;
            $estadisticas['day_streak'] = $languageDayStreak;
            $estadisticas['horas_al_dia'] = $languageTotalHoursInDay;
            $estadisticas['solo_horas'] = $languageSoloHoras;
            $estadisticas['type_percentages'] = $languageTypePercentages;
    
            return $estadisticas;
        }
    
        public function __destruct() {
            if ($this->conexion) {
                $this->conexion->close();
            }
        }

        public function calculateLogTypePercentagesByLanguage(array $logs, string $language): array
        {
            $typeCounts = [];
            $totalLogs = 0;

            if (empty($logs)) {
                return [];
            }

            // Filtrar los logs por el idioma especificado
            $languageLogs = array_filter($logs, function ($log) use ($language) {
                return $log->language === $language;
            });

            // Contar la cantidad de logs por tipo para el idioma especificado
            foreach ($languageLogs as $log) {
                $type = $log->type;
                $totalLogs++;

                if (isset($typeCounts[$type])) {
                    $typeCounts[$type]++;
                } else {
                    $typeCounts[$type] = 1;
                }
            }

            $typePercentages = [];
            if ($totalLogs > 0) {
                foreach ($typeCounts as $type => $count) {
                    $percentage = ($count / $totalLogs) * 100;
                    $typePercentages[$type] = round($percentage, 2);
                }
            }

            return $typePercentages;
        }

        public function getTypePercentages($user_id, $language) {
            $userLogs = $this->modelo->getLogs($user_id);
            return $this->calculateLogTypePercentagesByLanguage($userLogs, $language);
        }

    }
?>
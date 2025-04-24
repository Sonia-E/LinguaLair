<?php
    // // Iniciar una nueva sesión o reanudar la existente 
    // session_start();

    class StatsController {
        private $modelo;
        private $BaseController;

        public function __construct($modelo, $BaseController) { // Accept the $modelo instance
            $this->modelo = $modelo; // Assign the passed $modelo to the class property
            $this->BaseController = $BaseController;
        }
    
        public function open_page($modelo) {
            $user_id = $_SESSION['user_id'];
            $this->BaseController->get_profile_data($user_id);
            // Obtener los datos del usuario
            $array_usuario = $this->modelo->getUser($user_id);
            $usuario = $array_usuario[0][0];
            $logs = $array_usuario[0][1];
    
            // Contar los logs del usuario
            $totalLogs = $this->modelo->contarLogsUsuario($user_id);
    
            // Obtener el total de horas de estudio
            $totalHoras = $this->modelo->obtenerTotalHorasUsuario($user_id);
    
            // Obtener el total de minutos para the title control
            $totalMinutosRaw = $this->modelo->obtenerTotalMinutosUsuario($user_id);
            $languagePercentages = $modelo->getLanguagePercentagesByDurationForUser($user_id);
            $userLanguages = $modelo->getUserLanguages($user_id);
            // $dataParaVista['userLanguages'] = $userLanguages;
            // $dataParaVista['languagePercentages'] = $languagePercentages;
            $totalAchievements = $this->getTotalUserAchievementsCount($user_id);
            $dayStreak = $this->calculatePostingStreak($user_id);
            require 'src/views/stats.php';
        }

        public function getTotalUserAchievementsCount($userId) {
            $userAchievements = $this->modelo->getUserAchievements($userId);
    
            if ($userAchievements !== false) {
                return count($userAchievements);
            } else {
                return 0; // O algún otro valor que indique un error al obtener los logros
            }
        }

        public function calculatePostingStreak($userId) {
            $postDates = $this->modelo->getUserLogPostDates($userId);
    
            if (!$postDates) {
                return 0; // El usuario no tiene logs
            }
    
            $streak = 0;
            $maxStreak = 0;
            $previousDate = null;
            $today = new DateTime();
            $yesterday = (new DateTime())->modify('-1 day')->format('Y-m-d');
    
            foreach ($postDates as $date) {
                $currentDate = new DateTime($date);
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
    }
?>
<?php
    namespace Sonia\LinguaLair\controllers;

    class StatsController {
        private $modelo;
        private $BaseController;
        private $StatsModel;

        public function __construct($modelo, $BaseController, $StatsModel) {
            $this->modelo = $modelo;
            $this->BaseController = $BaseController;
            $this->StatsModel = $StatsModel;
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
            $datosIdioma = $this->mostrarEstadisticasUsuario($user_id);
            // Get logs types
            $logTypes = $this->modelo->getLanguageTypes();
            
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

        public function mostrarEstadisticasUsuario($userId) {
            $userData = $this->modelo->getUser($userId);
    
            if ($userData) {
                $userLanguages = $this->modelo->getUserLanguages($userId); // Obtener los idiomas del usuario
    
                $estadisticasPorIdioma = [];
            if ($userLanguages) {
                foreach ($userLanguages as $language) {
                    $estadisticas = $this->StatsModel->obtenerEstadisticasPorIdioma($userId, $language);
                    $estadisticas['idioma'] = $language; // Añadimos el idioma al subarray
                    $estadisticasPorIdioma[] = $estadisticas; // Añadimos el subarray al array principal
                }
            }
    
                $datosParaLaVista = [
                    'estadisticas_por_idioma' => $estadisticasPorIdioma,
                ];
                return $datosParaLaVista;
            } else {
                echo "No se pudo obtener la información del usuario.";
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
    }
?>
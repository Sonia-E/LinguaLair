<?php
    namespace Sonia\LinguaLair\controllers;
    
    class EventsController {
        private $modelo;
        private $BaseController;
        private $SocialModel;

        public function __construct($modelo, $BaseController, $SocialModel) {
            $this->modelo = $modelo;
            $this->BaseController = $BaseController;
            $this->SocialModel = $SocialModel;
        }

        public function open_page() {
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
            $events = $this->SocialModel->getEvents();
            
            require 'src/views/events.php';
        }

        public function book() {
            if (isset($_POST['user_id']) && isset($_POST['event_od'])) {
                $user_id = intval($_POST['user_id']);
                $event_id = intval($_POST['event_od']);
            
                // Basic security check: Ensure IDs are positive integers
                if ($user_id > 0 && $event_id > 0) {
                    if ($this->SocialModel->bookEvent($user_id, $event_id)) {
                        $response = ['success' => true];
                    } else {
                        $response = ['success' => false, 'message' => 'Failed to book event.'];
                    }
                } else {
                    $response = ['success' => false, 'message' => 'Invalid IDs.'];
                }
            } else {
                $response = ['success' => false, 'message' => 'Missing ids.'];
            }
            
            header('Content-Type: application/json');
            echo json_encode($response);
        }
}
?>
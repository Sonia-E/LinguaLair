<?php
    namespace Sonia\LinguaLair\controllers;
    
    class EventsController {
        private $SocialModel;

        public function __construct($SocialModel) {
            $this->SocialModel = $SocialModel;
        }

        public function open_page() {
            global $usuario, $logs, $totalLogs, $totalHoras, $totalMinutosRaw, $following, $logTypes, $totalAchievements, $dayStreak;
            $events = $this->SocialModel->getEvents();
            require 'src/views/events.php';
        }

        public function book() {
            if (isset($_POST['user_id']) && isset($_POST['event_id'])) {
                $user_id = intval($_POST['user_id']);
                $event_id = intval($_POST['event_id']);
            
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
            exit();
        }

        public function unbook() {
            if (isset($_POST['user_id']) && isset($_POST['event_id'])) {
                $user_id = intval($_POST['user_id']);
                $event_id = intval($_POST['event_id']);
            
                if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $user_id) {
                    if ($user_id > 0 && $event_id > 0) {
                        if ($this->SocialModel->unbookEvent($user_id, $event_id)) {
                            $response = ['success' => true];
                        } else {
                            $response = ['success' => false, 'message' => 'Error al hacer unbooking.'];
                        }
                    } else {
                        $response = ['success' => false, 'message' => 'IDs inválidos.'];
                    }
                } else {
                    $response = ['success' => false, 'message' => 'El ID del usuario no coincide con la sesión actual.'];
                }
            } else {
                $response = ['success' => false, 'message' => 'Faltan user_id o event_id.'];
            }
            
            header('Content-Type: application/json');
            echo json_encode($response);
        }

        public function getEventDetails() {
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $eventId = $_GET['id'];
                $event = $this->SocialModel->getEvent($eventId);
    
                if ($event) {
                    $isAttending = $this->SocialModel->isAttending($_SESSION['user_id'], $eventId);
    
                    $response = [
                        'id' => $event->id,
                        'name' => $event->name,
                        'description' => $event->description,
                        'creation_date' => $event->creation_date,
                        'event_date' => $event->event_date,
                        'type' => $event->type,
                        'subtype' => $event->subtype,
                        'exchange_lang_1' => $event->exchange_lang_1,
                        'exchange_lang_2' => $event->exchange_lang_2,
                        'main_lang' => $event->main_lang,
                        'learning_lang' => $event->learning_lang,
                        'city' => $event->city,
                        'country' => $event->country,
                        'event_time' => $event->event_time,
                        'long_description' => $event->long_description,
                        'attending' => $isAttending // Añade el estado de asistencia a la respuesta
                    ];
    
                    header('Content-Type: application/json');
                    echo json_encode($response);
                } else {
                    // Manejar el caso en que no se encuentra el evento
                    http_response_code(404);
                    echo json_encode(['error' => 'Evento no encontrado']);
                }
            } else {
                // Manejar el caso en que no se proporciona un ID válido
                http_response_code(400);
                echo json_encode(['error' => 'ID de evento no válido']);
            }
        }
}
?>
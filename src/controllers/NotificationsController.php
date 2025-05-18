<?php
namespace Sonia\LinguaLair\controllers;

class NotificationsController {
    private $notificationModel;

    public function __construct($notificationModel) {
        $this->notificationModel = $notificationModel;
    }

    public function open_page() {
        global $usuario, $logs, $totalLogs, $totalHoras, $totalMinutosRaw, $following, $logTypes, $totalAchievements, $dayStreak;
        $notifications = $this->getNotifications();
        require 'src/views/notifications.php';
    }

    public function getNotifications() {
        $userId = $_SESSION['user_id'];

        // Obtenemos las notificaciones del usuario.
        $notifications = $this->notificationModel->getNotificationsByUserId($userId);

        // Marcamos las notificaciones como leídas.
        $this->notificationModel->markAsReadByUserId($userId);

        return $notifications;
    }

    public function saveNotification() {
        // Verificamos si es una petición POST y si se enviaron los datos necesarios.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); // Método no permitido
            echo json_encode(['error' => 'Method Not Allowed']);
            return;
        }

        if (!isset($_POST['user_id']) || !isset($_POST['type']) || !isset($_POST['content'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Missing parameters']);
            return;
        }

        $userId = intval($_POST['user_id']);
        $type = $_POST['type'];
        $content = $_POST['content'];
        $relatedId = isset($_POST['related_id']) ? intval($_POST['related_id']) : null;

        // Validamos los datos 
        if ($userId <= 0) {
             http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Invalid user ID']);
            return;
        }

        // Guardamos la notificación.
        $result = $this->notificationModel->saveNotification($userId, $type, $content, $relatedId);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Notification saved']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'error' => 'Failed to save notification']);
        }
    }
}

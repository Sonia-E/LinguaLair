<?php
namespace Sonia\LinguaLair\controllers;

use Sonia\LinguaLair\Models\NotificationModel; // Importar la clase del modelo

class NotificationsController {
    private $notificationModel;

    public function __construct(NotificationModel $notificationModel) {
        $this->notificationModel = $notificationModel;
    }

    public function open_page() {
        global $usuario, $logs, $totalLogs, $totalHoras, $totalMinutosRaw, $following, $logTypes, $totalAchievements, $dayStreak;
        $notifications = $this->getNotifications();
        require 'src/views/notifications.php';
    }

    public function getNotifications() {
        $userId = $_SESSION['user_id'];

        // Obtener las notificaciones del usuario.
        $notifications = $this->notificationModel->getNotificationsByUserId($userId);

        // Marcar las notificaciones como leídas.
        $this->notificationModel->markAsReadByUserId($userId);

        return $notifications;
    }

    public function saveNotification() {
        // Verificar si es una petición POST y si se enviaron los datos necesarios.
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

        // Validar los datos (por ejemplo, que el user_id sea un entero positivo).
        if ($userId <= 0) {
             http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Invalid user ID']);
            return;
        }

        // Guardar la notificación.
        $result = $this->notificationModel->saveNotification($userId, $type, $content, $relatedId);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Notification saved']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'error' => 'Failed to save notification']);
        }
    }
}

// Ejemplo de uso (en tu archivo de rutas o donde instancies los controladores):
// Suponiendo que ya tienes una instancia de NotificationModel:
// $notificationModel = new NotificationModel($servidor, $usuario, $contrasenia, $base_datos);
// $notificationController = new NotificationController($notificationModel);

// Para obtener las notificaciones de un usuario (ejemplo de ruta: /api/notifications/get)
// if ($uri == '/api/notifications/get') {
//     $notificationController->getNotifications();
// }

//Para guardar una notificación (ejemplo de ruta: /api/notifications/save)
//if ($uri == '/api/notifications/save') {
//  $notificationController->saveNotification();
//}
?>

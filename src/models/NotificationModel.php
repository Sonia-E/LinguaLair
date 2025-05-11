<?php
namespace Sonia\LinguaLair\Models;

class NotificationModel {
    private $conexion;
    private $tabla = 'notifications';

    public function __construct($servidor, $usuario, $contrasenia, $base_datos) {
        $this->conexion = new \mysqli($servidor, $usuario, $contrasenia, $base_datos);

        if ($this->conexion->connect_error) {
            die("Conexión fallida: " . $this->conexion->connect_error);
        } else {
            $this->conexion->set_charset("utf8");
        }
    }

    public function saveNotification($userId, $type, $content, $relatedId = null) {
        if (!$this->conexion) return false;

        $sql = "INSERT INTO " . $this->tabla . " (user_id, type, content, related_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssi", $userId, $type, $content, $relatedId);
            $result = $stmt->execute();
            $stmt->close();
            return $result; // Devuelve true en caso de éxito, false en caso de error
        } else {
            error_log("Error al preparar la consulta para guardar la notificación: " . $this->conexion->error);
            return false;
        }
    }

    public function getNotificationsByUserId($userId) {
        if (!$this->conexion) return [];

        $sql = "SELECT * FROM " . $this->tabla . " WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $notifications = [];
            while ($row = $result->fetch_assoc()) {
                $notifications[] = $row;
            }
            $stmt->close();
            return $notifications;
        } else {
            error_log("Error al preparar la consulta para obtener notificaciones: " . $this->conexion->error);
            return [];
        }
    }

    public function markAsReadByUserId($userId) {
        if (!$this->conexion) return false;

        $sql = "UPDATE " . $this->tabla . " SET read_status = 1 WHERE user_id = ?";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $userId);
            $result = $stmt->execute();
            $stmt->close();
            return $result; // Devuelve true en caso de éxito, false en caso de error
        } else {
            error_log("Error al preparar la consulta para marcar notificaciones como leídas: " . $this->conexion->error);
            return false;
        }
    }

    public function __destruct() {
        if ($this->conexion) {
            $this->conexion->close();
        }
    }
}
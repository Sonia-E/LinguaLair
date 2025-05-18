<?php
namespace Sonia\LinguaLair\Models;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'SMTP_password.php';

class IncidentsModel {
    private $conexion;

    public function __construct($servidor, $usuario, $contrasenia, $base_datos) {
        $this->conexion = new \mysqli($servidor, $usuario, $contrasenia, $base_datos);

        if ($this->conexion->connect_error) {
            die("Conexión fallida: " . $this->conexion->connect_error);
        } else {
            $this->conexion->set_charset("utf8");
        }
    }

    public function insertarIncidencia(int $userId, string $username, ?string $userEmail, string $incidentType, string $description, string $urgency = 'medium'): ?array {
        if (!$this->conexion) {
            error_log("No hay conexión a la base de datos.");
            return null;
        }

        try {
            // Preparamos la consulta SQL para la inserción.
            $sql = "INSERT INTO incidents (user_id, username, user_email, incident_type, description, urgency)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->conexion->prepare($sql);

            if (!$stmt) {
                error_log("Error al preparar la consulta: " . $this->conexion->error);
                return null;
            }

            // Vinculamos los parámetros a la consulta preparada.
            $stmt->bind_param("isssss", $userId, $username, $userEmail, $incidentType, $description, $urgency);

            // Ejecutamos la consulta.
            $stmt->execute();

            // Verificamos si la inserción fue exitosa.
            if ($stmt->affected_rows <= 0) {
                error_log("No se pudo insertar la incidencia. Detalles: " . $stmt->error);
                $stmt->close();
                return null;
            }

            // Obtenemos el ID de la incidencia recién insertada.
            $incidentId = $this->conexion->insert_id;

            // Recuperamos los datos de la incidencia insertada para retornar.
            $sql_select = "SELECT id, user_id, username, user_email, incident_type, description, urgency, creation_date, state FROM incidents WHERE id = ?";
            $stmt_select = $this->conexion->prepare($sql_select);

            if (!$stmt_select) {
                error_log("Error al preparar la consulta de selección: " . $this->conexion->error);
                $stmt->close();
                return null;
            }
            $stmt_select->bind_param("i", $incidentId);
            $stmt_select->execute();
            $result = $stmt_select->get_result();
            $newIncident = $result->fetch_assoc();

            if (!$newIncident) {
                error_log("No se pudo obtener la incidencia insertada.");
                $stmt->close();
                $stmt_select->close();
                return null;
            }

            // Cerramos las declaraciones preparadas.
            $stmt->close();
            $stmt_select->close();

            // Enviamos correo electrónico
            $this->enviarCorreoNotificacion($newIncident);

            return $newIncident;

        } catch (\Exception $e) {
            // Registramos el error en el log para su posterior análisis.
            error_log("Error al insertar la incidencia: " . $e->getMessage());
            return null;
        }
    }

    private function enviarCorreoNotificacion(array $incidencia): void {
        global $smtp_password;
        $mail = new PHPMailer(true);
        try {
            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->Port       = 587;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->SMTPAuth   = true;
            $mail->Username   = 'soniaenjutom94@gmail.com';
            $mail->Password   =  $smtp_password; // Contraseña de aplicación

            // Configuración del correo
            $mail->setFrom('soniaenjutom94@gmail.com', 'Incidents Management System'); // Remitente
            $mail->addAddress('soniaenjutom94@gmail.com'); // Destinatario - ¡Cambiar!
            $mail->addReplyTo('soniaenjutom94@gmail.com', 'Incidents Management System');

            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Asunto
            $subject = 'New Incident Registered - ID: ' . $incidencia['id'];

            // Cuerpo del correo (texto plano)
            $message = "A new incident has been registered with the following details:\n\n";
            $message .= "ID: " . $incidencia['id'] . "\n";
            $message .= "Creation date: " . $incidencia['creation_date'] . "\n";
            $message .= "Username: " . $incidencia['username'] . "\n";
            if (!empty($incidencia['user_email'])) {
                $message .= "User's email: " . $incidencia['user_email'] . "\n";
            }
            $message .= "Incident type: " . $incidencia['incident_type'] . "\n";
            $message .= "Urgency: " . $incidencia['urgency'] . "\n";
            $message .= "Description:\n" . $incidencia['description'] . "\n";
            $message .= "State: " . $incidencia['state'] . "\n";
            $message .= "\n";
            $message .= "Sincerely,\n";
            $message .= "Incidents Management System";

            // Cuerpo del correo (HTML)
            $htmlMessage = "<h1>A new incident has been registered with the following details:</h1>" .
                "<p><strong>ID:</strong> " . $incidencia['id'] . "</p>" .
                "<p><strong>Creation date:</strong> " . $incidencia['creation_date'] . "</p>" .
                "<p><strong>Username:</strong> " . $incidencia['username'] . "</p>" .
                (!empty($incidencia['user_email']) ? "<p><strong>User's email:</strong> " . $incidencia['user_email'] . "</p>" : "") .
                "<p><strong>Incident type:</strong> " . $incidencia['incident_type'] . "</p>" .
                "<p><strong>Urgency:</strong> " . $incidencia['urgency'] . "</p>" .
                "<p><strong>Description:</strong><br>" . $incidencia['description'] . "</p>" .
                "<p><strong>State:</strong> " . $incidencia['state'] . "</p>" .
                "<p>Sincerely,<br>Incidents Management System</p>";

            $mail->Subject = $subject;
            $mail->Body    = $htmlMessage;
            $mail->AltBody = $message;

            $mail->send();

        } catch (\Exception $e) {
            error_log("Error al enviar el correo: {$mail->ErrorInfo}");
            echo "Error al enviar el correo: {$mail->ErrorInfo}"; // Importante: Muestra el error
        }
    }

    public function __destruct() {
        if ($this->conexion) {
            $this->conexion->close();
        }
    }
}
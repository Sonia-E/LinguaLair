<?php
namespace Sonia\LinguaLair\controllers;

class IncidentsController {
    private $IncidentsModel;

    public function __construct($IncidentsModel) {
         $this->IncidentsModel = $IncidentsModel;
    }

    public function procesarFormulario() {
        echo "entramos<br>";
        // 1. Recibir datos del formulario
        $username = $_POST['username'] ?? '';
        $user_email = $_POST['user_email'] ?? null;
        $incident_type = $_POST['incident_type'] ?? '';
        $description = $_POST['description'] ?? '';
        $urgency = $_POST['urgency'] ?? 'medium'; // Valor por defecto si no se selecciona
        $userId = $_SESSION['user_id'] ?? 0; // Asegúrate de obtener el ID del usuario desde el formulario.

        echo $description;

        // 2. Validar los datos (¡Importante!)
        if (empty($username) || empty($incident_type) || empty($description) || empty($userId)) {
            $error = "Por favor, complete todos los campos obligatorios.";
            // Aquí deberías incluir la lógica para mostrar el error al usuario en la vista.
            // Por ejemplo: include 'vista_formulario_incidencia.php'; // Pasa $error a la vista
            return; // Detiene la ejecución después de mostrar el error.
        }

        echo "tras validaciones<br>";

        // 3. Insertar la incidencia usando el modelo
        $nuevaIncidencia = $this->IncidentsModel->insertarIncidencia($userId, $username, $user_email, $incident_type, $description, $urgency);

        echo $nuevaIncidencia;

        if ($nuevaIncidencia) {
            // 4. Definir el destinatario del correo
            $destinatarioEmail = 'soniaenjutom94@gmail.com'; // Reemplazar
            $destinatarioNombre = 'LinguaLair\'s Support Team'; // Reemplazar

            $correoEnviado = $this->enviarCorreoIncidencia($destinatarioEmail, $destinatarioNombre, $nuevaIncidencia);

            // 5. Enviar el correo electrónico de notificación
            if ($correoEnviado) {
                $mensajeExito = "Incidencia registrada correctamente. Se ha enviado una notificación al equipo de soporte.";
            } else {
                $mensajeExito = "Incidencia registrada correctamente, pero hubo un error al enviar la notificación.";
            }

            echo "tras enviar correo<br>";
                // Aquí deberías incluir la lógica para mostrar el mensaje de éxito al usuario en la vista.
            // Por ejemplo: include 'vista_confirmacion.php'; // Pasa $mensajeExito a la vista
                return;

        } else {
            $mensajeError = "Error al registrar la incidencia.";
            // Aquí deberías incluir la lógica para mostrar el error al usuario en la vista.
            // Por ejemplo: include 'vista_formulario_incidencia.php'; // Pasa $mensajeError a la vista
            return;
        }
    }

    public function enviarCorreoIncidencia($destinatarioEmail, $destinatarioNombre, $incidencia) {
        ini_set('SMTP', 'smtp.gmail.com');
        ini_set('smtp_port', 587);
        ini_set('sendmail_from', 'soniaenjutom94@gmail.com');
        $to = $destinatarioEmail;
        $subject = 'New Incident Registered - ID: ' . $incidencia['id'];

        $message = "A new incident has been registered with the following details:\n\n";
        $message .= "ID: " . $incidencia['id'] . "\n";
        $message .= "Creation date: " . $incidencia['fecha_creacion'] . "\n";
        $message .= "Username: " . $incidencia['nombre_usuario'] . "\n";
        if (!empty($incidencia['email_usuario'])) {
            $message .= "User's email: " . $incidencia['email_usuario'] . "\n";
        }
        $message .= "Incidet type: " . $incidencia['tipo_incidencia'] . "\n";
        $message .= "Urgency: " . $incidencia['urgencia'] . "\n";
        $message .= "Description:\n" . $incidencia['descripcion'] . "\n";
        $message .= "State: " . $incidencia['estado'] . "\n";
        $message .= "\n";
        $message .= "Sincerelly,\n";
        $message .= "Incidents Management System";

        $headers = 'From: tu_email_remitente@tu_dominio.com' . "\r\n" .
                'Reply-To: tu_email_remitente@tu_dominio.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

        if (mail($to, $subject, $message, $headers)) {
            return true; // El correo se envió correctamente
        } else {
            error_log("Error al enviar el correo de incidencia con mail()");
            return false; // Hubo un error al enviar el correo
        }
    }

    public function open_page() {
        global $usuario, $logs, $totalLogs, $totalHoras, $totalMinutosRaw, $following, $logTypes, $totalAchievements, $dayStreak;
        require 'src/views/contact.php';
    }
}

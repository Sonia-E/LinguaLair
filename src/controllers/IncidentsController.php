<?php
namespace Sonia\LinguaLair\controllers;

class IncidentsController {
    private $IncidentsModel;

    public function __construct($IncidentsModel) {
         $this->IncidentsModel = $IncidentsModel;
    }

    public function procesarFormulario() {
        // 1. Recibir datos del formulario
        $username = $_POST['username'] ?? '';
        $user_email = $_POST['user_email'] ?? null;
        $incident_type = $_POST['incident_type'] ?? '';
        $description = $_POST['description'] ?? '';
        $urgency = $_POST['urgency'] ?? 'medium'; // Valor por defecto si no se selecciona
        $userId = $_SESSION['user_id'] ?? 0; // Asegúrate de obtener el ID del usuario desde el formulario.

        // 2. Validar los datos (¡Importante!)
        if (empty($username) || empty($incident_type) || empty($description) || empty($userId)) {
            $error = "Por favor, complete todos los campos obligatorios.";
            // Aquí deberías incluir la lógica para mostrar el error al usuario en la vista.
            // Por ejemplo: include 'vista_formulario_incidencia.php'; // Pasa $error a la vista
            return; // Detiene la ejecución después de mostrar el error.
        }

        // 3. Insertar la incidencia usando el modelo
        $nuevaIncidencia = $this->IncidentsModel->insertarIncidencia($userId, $username, $user_email, $incident_type, $description, $urgency);

        global $mensajeExito, $mensajeError;

        if ($nuevaIncidencia) {
            $mensajeExito = "Incident registered correctly. A notification has been sent to the support team.";
            $this->open_page();
            return;

        } else {
            $mensajeError = "Error registering the incident.";
            $this->open_page();
            return;
        }
    }

    public function open_page() {
        global $usuario, $logs, $totalLogs, $totalHoras, $totalMinutosRaw, $following, $logTypes, $totalAchievements, $dayStreak, $mensajeExito, $mensajeError;
        require 'src/views/contact.php';
    }
}

<?php

// Asegúrate de que CON_CONTROLADOR esté definido si lo usas en otros archivos
// if (!defined('CON_CONTROLADOR')) die('Acceso no permitido.');

class LogFormController {
    private $modelo;

    public function __construct($modelo) { // Accept the $modelo instance
        $this->modelo = $modelo; // Assign the passed $modelo to the class property
    }

    public function procesarFormulario() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Recoger los datos del formulario
            $description = $_POST["description"];
            $language = $_POST["language"];
            $type = $_POST["type"];
            $duration = $_POST["duration"];
            $log_date = $_POST["date"];
            $user_id = $_SESSION["user_id"];

            // Guardar el nuevo log en la base de datos (asumiendo que tienes una función para esto en tu modelo)
            $log_guardado = $this->modelo->addLog($user_id, $description, $language, $type, $duration, $log_date);

            if ($log_guardado) {
                $experience_gain = $duration;
                $this->modelo->addExperience($user_id, $experience_gain);

                $profile_data = $this->modelo->getProfileData($user_id);
                $nuevo_nivel = $profile_data->level;
                $nueva_experiencia = $profile_data->experience;

                if ($profile_data->experience >= 100) {
                    $this->modelo->levelUp($user_id);
                    $profile_data_updated = $this->modelo->getProfileData($user_id);
                    $nuevo_nivel = $profile_data_updated->level;
                    $nueva_experiencia = $profile_data_updated->experience;
                }

                // Responder con JSON
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'nuevaExperiencia' => $nueva_experiencia, 'nuevoNivel' => $nuevo_nivel]);
                exit();
            } else {
                // Responder con error en JSON
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Error al guardar el log.']);
                exit();
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Acceso no permitido.']);
            exit();
        }
    }
}

?>
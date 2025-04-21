<?php
    class ExploreController {
        private $modelo;
        private $BaseController;
        private $SocialModel;

        public function __construct($modelo, $BaseController = null, $SocialModel) {
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
            
            require './views/explore.php';
        }


        public function procesarFormulario() {
            // Recogemos el valor introducido en el input
            $texto = isset($_GET["texto"]) ? $_GET["texto"] : "";
            $logsEncontrados = [];

            if ($texto !== "") {
                // Pasamos el valor del input a minúsculas para una búsqueda no sensible a mayúsculas
                $texto = strtolower($texto);

                // Buscamos a un usuario con @ o no
                if (preg_match('/^@/', $texto)) {
                    // Meter método que busque en la tabla usuarios
                    $texto_usuario = ltrim($texto, '@');
                    $usuario = $this->SocialModel->exploreUsers($texto_usuario);

                    // Devolvemos el resultado en formato JSON
                    echo json_encode($usuario);
                    exit();
                } else {
                // Llamamos al método del modelo para buscar logs por descripción
                $logsEncontrados = $this->SocialModel->buscarLogsPorDescripcion($texto);

                // Devolvemos el resultado en formato JSON
                echo json_encode($logsEncontrados);
                exit();
                }
            } else {
                // Si no hay texto de búsqueda, devolvemos un array vacío
                echo json_encode([]);
                exit();
            }
        }
}
?>
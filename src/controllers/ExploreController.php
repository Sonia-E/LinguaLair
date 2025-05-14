<?php
    namespace Sonia\LinguaLair\controllers;
    
    class ExploreController {
        private $modelo;
        private $SocialModel;

        public function __construct($modelo, $SocialModel) {
            $this->modelo = $modelo;
            $this->SocialModel = $SocialModel;
        }

        public function open_page() {
            global $usuario, $logs, $totalLogs, $totalHoras, $totalMinutosRaw, $following, $logTypes, $totalAchievements, $dayStreak;
            require 'src/views/explore.php';
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
                } else {
                // Llamamos al método del modelo para buscar logs por descripción
                $logsEncontrados = $this->SocialModel->buscarLogsPorDescripcion($texto);

                // Devolvemos el resultado en formato JSON
                echo json_encode($logsEncontrados);
                }
            } else {
                // Si no hay texto de búsqueda, devolvemos un array vacío
                echo json_encode([]);
            }
        }
    }
?>
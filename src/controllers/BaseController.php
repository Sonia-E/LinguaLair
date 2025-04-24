<?php
    class BaseController {
        private $modelo;
        private $SocialModel;

        public function __construct($modelo, $SocialModel = null) { // Accept the $modelo instance
            $this->modelo = $modelo; // Assign the passed $modelo to the class property
            $this->SocialModel = $SocialModel;
        }

    
        public function open_page() {
            // require './views/setProfile.php';
            require 'src/views/setProfile.php';
        }

        public function get_profile_data($id) {
            global $usuario, $logs, $totalLogs, $totalHoras, $totalMinutosRaw, $following;
            // Obtener los datos del usuario
            $array_usuario = $this->modelo->getUser($id);
            $usuario = $array_usuario[0][0];
            $logs = $array_usuario[0][1];
    
            // Contar los logs del usuario
            $totalLogs = $this->modelo->contarLogsUsuario($id);
    
            // Obtener el total de horas de estudio
            $totalHoras = $this->modelo->obtenerTotalHorasUsuario($id);
    
            // Obtener el total de minutos para the title control
            $totalMinutosRaw = $this->modelo->obtenerTotalMinutosUsuario($id);

            // Get ids following users
            $following = $this->SocialModel->getFollowingUsers($id);
        }
}
?>
<?php
    // Importamos el modelo
    use Sonia\LinguaLair\Models\modelo;
    $modelo = new Modelo("localhost", "foc", "foc", 'LinguaLair');

    //#######################################
	//############## PROFILE ################
	//#######################################

    /**
     * Función que muestra una lista de artículos y carga la vista correspondiente
     *
     * @return void
     */
    function get_profile_data($modelo, $id) {
        // Obtener los datos del usuario
        $array_usuario = $modelo->getUser($id);
        $usuario = $array_usuario[0][0];
        $logs = $array_usuario[0][1];

        // Contar los logs del usuario
        $totalLogs = $modelo->contarLogsUsuario($id);

        // Obtener el total de horas de estudio
        $totalHoras = $modelo->obtenerTotalHorasUsuario($id);

        // Obtener el total de minutos para the title control
        $totalMinutosRaw = $modelo->obtenerTotalMinutosUsuario($id);

        require 'views/home.php';
        // require 'src/views/home.php';
    }
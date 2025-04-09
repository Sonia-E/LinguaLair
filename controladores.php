<?php
	// Evitamos que se llame al fichero sin pasar por el controlador
	// if (!defined('CON_CONTROLADOR')) {
    //     // Matamos el proceso php
	// 	die('Error: No se permite el acceso directo a esta ruta');
	// }
	
    // Importamos el modelo
    require_once './modelo.php';
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
        // Obtener los artículos desde el modelo
        $array_usuario = $modelo->getUser($id);
        $usuario = $array_usuario[0][0];
        $logs = $array_usuario[0][1];
        // Cargar la vista para mostrar la lista de artículos
        require './vistas/home.php';
    }

    
?>
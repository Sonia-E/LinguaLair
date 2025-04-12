<?php
    // Evitamos que se llame al fichero sin pasar por el controlador
    if (!defined('CON_CONTROLADOR')) {
        // Matamos el proceso php
        die('Error: No se permite el acceso directo a esta ruta');
    }
?>
<!-- Iniciamos la estructura -->
<?php $s = ob_get_clean(); startblock('contenido') ?>
<link rel="stylesheet" type="text/css" href="css/stats.css"/>
<div class="dashboard">
    <div class="stats">

    </div>
<!-- Terminamos la estructura -->
<?php endblock() ?>
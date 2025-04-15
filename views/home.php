<?php
    // Evitamos que se llame al fichero sin pasar por el controlador
    if (!defined('CON_CONTROLADOR')) {
        // Matamos el proceso php
        die('Error: No se permite el acceso directo a esta ruta');
    }
?>
<!-- Importamos la estructura de la base -->
<?php 
ob_start();
include './base.php';
 ?>
<!-- Iniciamos la estructura -->
<?php startblock('contenido') ?>
    <div class="dashboard">
        <div class="feed">
            <div class="button-group">
                <button class="following">Following</button>
                <div class="divider"></div>
                <button class="logs">My logs</button>
            </div>
            <div class="following show">
                <?php include './views/feed.php' ?>
            </div>
            <div class="my-logs hidden">
                <div class="log"></div>
                <div class="log"></div>
                <div class="log"></div>
            </div> 
        </div>

        <div class="right-feed">
            <div class="feed-data top">
                <div class="notifications"></div>
            </div>
            <div class="feed-data">
                <div class="notifications"></div>
            </div>
        </div>
    </div>
<!-- Terminamos la estructura -->
<?php endblock(); 
 ?>
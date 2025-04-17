<?php
    // Evitamos que se llame al fichero sin pasar por el controlador
    if (!defined('CON_CONTROLADOR')) {
        // Matamos el proceso php
        die('Error: No se permite el acceso directo a esta ruta');
    }
?>
<?php include 'base.php' ?>
<!-- Iniciamos la estructura -->
<?php startblock('contenido') ?>
<link rel="stylesheet" type="text/css" href="css/profile.css"/>
<div class="dashboard">
    <div class="profile">
        <div class="header">
            <div class="avatar">
            <img src="<?php echo $usuario->profile_pic ?>" alt="profile picture" width="100%" height="100%">
            </div>
        </div>
    </div>
    <div class="feed">
            <div class="button-group">
                <h2>@<?php echo $usuario->username ?>'s Logs</h2>
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
</div>

<?php endblock() ?>
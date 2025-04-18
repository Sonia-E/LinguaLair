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
                <div class="info-usuario">
                    <div class="nick-user">
                        <span class="nickname"><?php echo $usuario->nickname ?><span class="role"><?php echo $usuario->game_roles ?></span></span>
                        <span class="username">@<?php echo $usuario->username ?></span>
                    </div>
                    
                    <div class="progress">
                        <span class="level"><span id="level-value"><?php echo $usuario->level ?></span></span>
                        <div class="life-bar-container">
                            <div class="life-bar" id="experience-bar" style="width: <?php echo $usuario->experience ?>%;">
                                <span id="experience-value"><span id="experience-text"><?php echo $usuario->experience ?>%</span></span>
                            </div>
                        </div>
                    </div>
                </div>
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
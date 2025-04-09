<?php
    // Evitamos que se llame al fichero sin pasar por el controlador
    if (!defined('CON_CONTROLADOR')) {
        // Matamos el proceso php
        die('Error: No se permite el acceso directo a esta ruta');
    }
?>
<!-- Importamos la estructura de la base -->
<?php include './base.php' ?>
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
                        <?php include './vistas/feed.php' ?>
                        <div class="log">
                            <div class="usuario">
                                <div class="log-user">
                                    <img src="<?php echo $usuario->profile_pic ?>" alt="profile picture">
                                    <div class="info-usuario">
                                        <div class="nick-user">
                                            <span class="nickname"><?php echo $usuario->nickname ?></span>
                                            <span class="username">@<?php echo $usuario->username ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="log-column">
                                    <div class="post-date"><span>31/03/25</span></div>
                                    <div class="duration">
                                        <span>30</span>
                                        <span>minutes</span>
                                    </div>
                                </div>
                            </div>
                            <div class="log-data">
                                <div class="log-row">
                                    <div class="description">
                                        <span>Fusce metus elit, dignissim vitae malesuada vehicula, scelerisque viverra ligula. Suspendisse facilisis ultricies lacus eget varius. Fusce lacus nulla, porta vel tempus eu, semper sit amet felis. Aenean vitae nunc eget turpis tristique sodales a at nunc. Suspendisse quis orci nec leo euismod vestibulum. Ut luctus leo.
                                        </span>
                                    </div>
                                    <div class="log-column">
                                        <div class="language">
                                            <span>Chinese</span>
                                        </div>
                                        <div class="type">
                                            <span>Grammar</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="log"></div>
                        <div class="log"></div>
                        <div class="log"></div>
                        <div class="log"></div>
                        <div class="log"></div>
                    </div>
                    <div class="my-logs hidden">
                        <div class="log"></div>
                        <div class="log"></div>
                        <div class="log"></div>
                    </div>
                    
                </div>
                <div class="feed-data"></div>
            </div>
        </main>
<!-- Terminamos la estructura -->
<?php endblock() ?>
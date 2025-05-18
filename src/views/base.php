<?php
    namespace Sonia\LinguaLair\Controllers;
    
?>
<?php require_once './libreria/ti.php' ?>
<?php blockbase(); ?>
<?php include 'src/views/logForm.php' ?>

<?php
    if (isset($_SESSION['user_id'])) {
        global $usuario;
    };
    
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LinguaLair</title>
        <link rel="preload" href="public/css/estilo.css" as="style">
        <link rel="stylesheet" type="text/css" href="public/css/estilo.css"/>
        <link rel="stylesheet" type="text/css" href="public/css/popup.css"/>
    </head>

    <body>
    <div id="overlay"></div>
        <div class="menu">
            <ion-icon name="menu-outline"></ion-icon>
            <ion-icon name="close-outline"></ion-icon>
        </div>

        <div class="barra-lateral">
            <div>
                <div class="nombre-pagina">
                    <ion-icon id="cloud" name="earth-outline"></ion-icon>
                    <span id="lingualair">LinguaLair</span>
                </div>
                <button class="boton">
                    <ion-icon name="add-outline"></ion-icon>
                    <span>New Log</span>
                </button>
            </div>

            <?php
                $current_uri = $_SERVER['REQUEST_URI'];
                $base_path = '/LinguaLair/';

                // Función para verificar si la URI actual coincide con el enlace
                function is_current_page($uri, $current_uri, $base_path = '') {
                    $full_uri = $base_path . $uri;
                    // Comprobamos si la URI actual coincide exactamente o comienza con la URI del enlace (para la página de inicio)
                    return ($current_uri === $full_uri) || ($uri === '/' && $current_uri === $base_path);
                }
            ?>

            <nav class="navegacion">
                <ul>
                    <li>
                        <a id="<?php if (is_current_page('/', $current_uri, $base_path)): ?>current<?php endif; ?>" href="<?php echo $base_path; ?>">
                            <ion-icon name="home-outline"></ion-icon>
                            <span>Home</span>
                        </a>
                    </li>
                    <li>
                        <a id="<?php if (is_current_page('explore', $current_uri, $base_path)): ?>current<?php endif; ?>" href="<?php echo $base_path; ?>explore">
                            <ion-icon name="people-outline"></ion-icon>
                            <span>Explore</span>
                        </a>
                    </li>
                    <li>
                        <a id="<?php if (is_current_page('my_profile', $current_uri, $base_path)): ?>current<?php endif; ?>" href="<?php echo $base_path; ?>my_profile">
                            <ion-icon name="person-outline"></ion-icon>
                            <span>Profile</span>
                        </a>
                    </li>
                    <li>
                        <a id="<?php if (is_current_page('stats', $current_uri, $base_path)): ?>current<?php endif; ?>" href="<?php echo $base_path; ?>stats">
                            <ion-icon name="bar-chart-outline"></ion-icon>
                            <span>Stats</span>
                        </a>
                    </li>
                    <li>
                        <a id="<?php if (is_current_page('notifications', $current_uri, $base_path)): ?>current<?php endif; ?>" href="<?php echo $base_path; ?>notifications">
                            <ion-icon name="notifications-outline"></ion-icon>
                            <span>Notifications</span>
                        </a>
                    </li>
                    <li>
                        <a id="<?php if (is_current_page('achievements', $current_uri, $base_path)): ?>current<?php endif; ?>" href="<?php echo $base_path; ?>achievements">
                            <ion-icon name="diamond-outline"></ion-icon>
                            <span>Achievements</span>
                        </a>
                    </li>
                    <li>
                        <a id="<?php if (is_current_page('events', $current_uri, $base_path)): ?>current<?php endif; ?>" href="<?php echo $base_path; ?>events">
                            <ion-icon name="chatbubbles-outline"></ion-icon>
                            <span>Events</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div>
                <div class="linea"></div>

                <div class="modo-oscuro">
                    <div class="info">
                        <ion-icon name="moon-outline"></ion-icon>
                        <span>Dark Mode</span>
                    </div>
                    <div class="switch">
                        <div class="base">
                            <div class="circulo"></div>
                        </div>
                    </div>
                </div>

                <div class="usuario">
                    <img src="<?php echo $usuario->profile_pic ?>" alt="profile picture">
                    <div class="info-usuario">
                        <div class="nick-user">
                            <span class="nickname"><?php echo $usuario->nickname ?></span>
                            <span class="username">@<?php echo $usuario->username ?></span>
                        </div>
                        <ion-icon name="ellipsis-vertical-outline"></ion-icon>
                    </div>
                </div>
            </div>

        </div>

        <main>
            <header>
                <div class="usuario">
                    <div class="info-usuario">
                        <img src="<?php echo $usuario->profile_pic ?>" alt="profile picture">
                        <div class="nick-user">
                            <span class="nickname"><?php echo $usuario->nickname ?><span class="role"><?php echo $usuario->game_roles ?></span></span>
                            <span class="username">@<?php echo $usuario->username ?></span>
                        </div>
                        
                        <div class="progress">
                            <span class="level"><span class="level-value"><?php echo $usuario->level ?></span></span>
                            <div class="life-bar-container">
                                <div class="life-bar experience-bar" style="width: <?php echo $usuario->experience ?>%;">
                                    <span class="experience-value"><span class="experience-text"><?php echo $usuario->experience ?>%</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="boton">
                        <ion-icon name="add-outline"></ion-icon>
                    </button>
                    <div class="follow">
                        <span id="logged-following-count" class="following"><?php echo $usuario->num_following ?> following</span>
                        <span class="divider">|</span>
                        <span id="logged-followers-count" class="followers"><?php echo $usuario->num_followers ?> followers</span>
                    </div>
                </div>
                <div class="linea"></div>
                <div class="stats">
                    <div class="languages">
                        <span class="native <?php echo $usuario->native_lang ? '' : 'hidden' ?>">Native: <?php echo $usuario->native_lang ?></span>
                        <span class="fluent <?php echo $usuario->fluent ? '' : 'hidden' ?>">Fluent: <?php echo $usuario->fluent ?></span>
                        <span class="learning">Learning: <?php echo $usuario->learning ? $usuario->learning : $usuario->languages; ?></span>
                    </div>
                    <?php
                        $column2 = [];

                        // Llenar el array $column2 con los valores de los idiomas, si existen
                        if (!empty($usuario->future)) {
                            $column2[] = ['type' => 'future', 'value' => $usuario->future];
                        }
                        if (!empty($usuario->on_hold)) {
                            $column2[] = ['type' => 'fluent', 'value' => $usuario->on_hold];
                        }
                        if (!empty($usuario->dabbling)) {
                            $column2[] = ['type' => 'learning', 'value' => $usuario->dabbling];
                        }
                    ?>
                    <div class="<?php echo $column2 ? 'languages' : 'hidden' ?>">
                        <span class="future <?php echo $usuario->future ? '' : 'hidden' ?>">Future: <?php echo $usuario->future ?></span>
                        <span class="on-hold <?php echo $usuario->on_hold ? '' : 'hidden' ?>">On hold: <?php echo $usuario->on_hold ?></span>
                        <span class="dabbling <?php echo $usuario->dabbling ? '' : 'hidden' ?>">Dabbling: <?php echo $usuario->dabbling ?></span>
                    </div>
                    <div class="logs">
                        <span class="nb"><?php echo $totalLogs ?? 0; ?></span>
                        <span class="title">Logs</span>
                    </div>
                    <div class="logs">
                        <span class="nb"><?php echo $totalHoras ?? 0; ?></span>
                        <span class="title"><?php echo ($totalMinutosRaw >= 60) ? "Study Hours" : "Minutes"; ?></span>
                    </div>
                    <div class="logs">
                        <span class="nb"><?php echo $totalAchievements ?? 0; ?></span>
                        <span class="title">Achievements</span>
                    </div>
                    <div class="logs">
                        <span class="nb"><?php echo $dayStreak ?? 0; ?></span>
                        <span class="title">Day Streak</span>
                    </div>
                </div>
            </header>
        

            <section>
                <!-- Aquí se cargará el contenido de cada página -->
                <?php emptyblock('contenido'); ?>
            </section>
            
        </main>

        <script type="text/javascript" src="public/js/game.js"></script>
        <script type="text/javascript" src="public/js/base.js"></script>
    </body>
</html>
<?php // include 'views/base.php' ?>
<?php include 'src/views/base.php'; ?>
<!-- Iniciamos la estructura -->
<?php startblock('contenido') ?>
<link rel="stylesheet" type="text/css" href="public/css/profile.css"/>
<link rel="stylesheet" type="text/css" href="public/css/explore.css"/>
<link rel="stylesheet" type="text/css" href="public/css/events.css"/>
<div class="dashboard">
    <div class="feed">
        <div class="button-group">
            <h2>Events Feed</h2>
        </div>
        <form id="explore-form">
            <input type="text" id="texto" onkeyup="explore(this.value)" placeholder="Search for events!" autofocus>
        </form>
        <div class="following show">
            <?php // include 'src/views/events_feed.php' ?>
            <!-- si no hay search div no hay transición de aparición -->
            <div id="resultados"></div>
            <?php foreach ($events as $event) { ?>
                <div class="event">
                    <div class="event-header usuario">
                        <div class="event-column">
                            <h3 class="event-name"><?php echo $event->name ?></h3>
                            
                            <div class="event-subtype">
                                <div class="<?php echo $event->subtype == 'Language Exchange' ? 'exchange-type' : 'hidden' ?>">
                                    <span class="exchange-langs"><?php echo $event->exchange_lang_1 ?>-<?php echo $event->exchange_lang_2 ?></span>
                                </div>
                                <div class="main-lang <?php echo $event->main_lang ? '' : 'hidden' ?>">Event language: <?php echo $event->main_lang ?></div>
                                <div class="learning-lang <?php echo $event->learning_lang ? '' : 'hidden' ?>">Target language: <?php echo $event->learning_lang ?></div>
                            </div>
                        </div>
                        <div class="event-right">
                            <div class="event-date"><span><?php echo $event->event_date ?></span></div>
                            <span class="event-type"><?php echo $event->type ?></span>
                        </div>
                    </div>
                    <div class="event-info">
                        <div class="log-row">
                            <div class="description">
                                <!-- Poner un límite de mostrar la descripción: poner botón de show more y ahí se muestra el evento a la derecha -->
                                <span><?php echo $event->description ?></span>
                            </div>
                        </div>
                        <div class="post-date">
                            <span><strong>Creation Date:</strong> <?php echo $event->creation_date ?></span>
                            <!-- Location if it's in person -->
                            <div class="location <?php echo $event->city ? '' : 'hidden' ?>">
                                <span>
                                    <span><?php echo $event->city ?></span>, <span><?php echo $event->country ?></span></div>
                                </span>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <!-- <div class="profile">
        <div class="search">
            
            <div class="following show">
                
            </div>
        </div>
        
    </div> -->
    <!-- <div class="feed"> -->
    <div class="profile hidden">
        <div class="header">
        <!-- <div class="header" style="background-image: url('<?php echo $usuario->bg_pic; ?>')"> -->
            <button class="edit">Attend</button>
            <h1 class="event-name"><?php echo $event->name ?></h1>
            
        </div>
        <div class="user-details">
            <div class="bio">
                <div class="languages">
                    <span class="native <?php echo $usuario->native_lang ? '' : 'hidden' ?>">Native: <?php echo $usuario->native_lang ?></span>
                    <span class="fluent <?php echo $usuario->fluent ? '' : 'hidden' ?>">Fluent: <?php echo $usuario->fluent ?></span>
                    <span class="learning">Learning: <?php echo $usuario->learning ? $usuario->learning : $usuario->languages; ?></span>
                    <span class="future <?php echo $usuario->future ? '' : 'hidden' ?>">Future: <?php echo $usuario->future ?></span>
                    <span class="on-hold <?php echo $usuario->on_hold ? '' : 'hidden' ?>">On hold: <?php echo $usuario->on_hold ?></span>
                    <span class="dabbling <?php echo $usuario->dabbling ? '' : 'hidden' ?>">Dabbling: <?php echo $usuario->dabbling ?></span>
                </div>
                <hr class="v">
                <div class="text"><span><?php echo $usuario->bio ?></span></div>
            </div>

            <hr class="separator">
            
            <div class="stats">
                <div class="logs">
                    <span class="nb"><?php echo $totalLogs ?? 0; ?></span>
                    <span class="title">Logs</span>
                </div>
                <div class="logs">
                    <span class="nb"><?php echo $totalHoras ?? 0; ?></span>
                    <span class="title"><?php echo ($totalMinutosRaw >= 60) ? "Study Hours" : "Minutes"; ?></span>
                </div>
                <div class="logs">
                    <span class="nb">5</span>
                    <span class="title">Achievements</span>
                </div>
                <div class="logs">
                    <span class="nb">3</span>
                    <span class="title">Day Streak</span>
                </div>
            </div>

            <hr class="separator">

            <div class="follow">
                <span class="following"><?php echo $usuario->num_following ?> following</span>
                <span class="divider">|</span>
                <span class="followers"><?php echo $usuario->num_followers ?> followers</span>
            </div>
        </div>
    </div>
    <!-- </div> -->
</div>
<?php endblock() ?>

<script type="text/javascript" src="public/js/explore.js"></script>
<script type="text/javascript" src="public/js/events.js"></script>
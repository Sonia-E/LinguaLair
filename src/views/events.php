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
                <div class="event" data-event-identifier="<?php echo $event->id ?>">
                    <div class="event-header usuario">
                        <div class="event-column">
                            <h3 class="event-name"><?php echo $event->name ?></h3>
                            
                            <div class="event-subtype">
                                <div class="<?php echo $event->subtype == 'Language Exchange' ? 'exchange-type' : 'hidden' ?>">
                                    <span class="exchange-langs"><?php echo $event->exchange_lang_1 ?> - <?php echo $event->exchange_lang_2 ?></span>
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
                                    <span><?php echo $event->city ?></span>, <span><?php echo $event->country ?></span>
                                </span>
                            </div>
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
            <div class="event-column">
            <div class="attend">
                <button
                    <?php $isAttending = false ?>
                    class="<?php echo $isAttending ? 'unbookButton' : 'bookButton'; ?>"
                    data-user-id="<?php echo $usuario->id; ?>"
                >
                    <span><?php echo $isAttending ? 'Attending' : 'Attend'; ?></span>
                </button>
            </div>
                <h1 class="event-name"></h1>
                <div class="event-subtype">
                    <div class="exchange-type">
                        <span class="exchange-langs"></span>
                    </div>
                    <div class="main-lang hidden">Event language: </div>
                    <div class="learning-lang hidden">Target language: </div>
                </div>
            </div>
        </div>
        <div class="user-details">
            <div class="bio">
                <div class="languages">
                    <div class="event-date"><span>Event Date: </span></div>
                    <span class="event-type"></span>
                    <span><strong>Creation Date:</strong> <span></span></span>
                    <div class="location hidden">
                        <span>
                            <span></span>, <span></span>
                        </span>
                    </div>
                </div>
                <hr class="v">
                <div class="text"><span></span></div>
            </div>
            <hr class="separator">
            <div class="stats">
                <div class="long-description">
                    <p></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endblock() ?>

<script type="text/javascript" src="public/js/explore.js"></script>
<script type="text/javascript" src="public/js/events.js"></script>
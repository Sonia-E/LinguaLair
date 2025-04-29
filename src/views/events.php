<?php // include 'views/base.php' ?>
<?php include 'src/views/base.php'; ?>
<!-- Iniciamos la estructura -->
<?php startblock('contenido') ?>
<link rel="stylesheet" type="text/css" href="public/css/profile.css"/>
<link rel="stylesheet" type="text/css" href="public/css/explore.css"/>
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
                <div class="event">
                    <div class="event-header">
                        nombre de evento: Intercambio Japonés-Español en Madrid!
                    </div>
                    <div class="event-info">
                        Main language: idioma en que se realiza el evento --- Solo mostrar cuando no es un evento de intercambios
                        Exchange languages: [language1]-[language2] --- Solo mostrar cuando es un evento de intercambios
                        Event Date:
                        Post Date:
                        Inscriptions: opened/closed
                    </div>
                </div>
            </div>
        </div>
    <!-- <div class="profile">
        <div class="search">
            
            <div class="following show">
                
            </div>
        </div>
        
    </div> -->
    <div class="feed"></div>
</div>
<?php endblock() ?>

<script type="text/javascript" src="public/js/explore.js"></script>
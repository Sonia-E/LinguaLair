<?php // include 'views/base.php' ?>
<?php include 'src/views/base.php'; ?>
<!-- Iniciamos la estructura -->
<?php startblock('contenido') ?>
<link rel="stylesheet" type="text/css" href="public/css/explore.css"/>
<div class="dashboard">
    <div class="search">
        <form id="explore-form">
            <input type="text" id="texto" onkeyup="explore(this.value)" placeholder="Search for logs or add '@' to search for users" autofocus>
        </form>
        <div class="following show">
            <div id="resultados"></div>
        </div>
    </div>
</div>
<?php endblock() ?>

<script type="text/javascript" src="public/js/explore.js"></script>
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
        <div class="button-group">
            <button class="allStats active-tab" data-language="all">All</button>
            <div class="divider"></div>
            <?php
            if (!empty($userLanguages)) {
                $lastLanguage = end($userLanguages);
                foreach ($userLanguages as $language) {
                    $buttonClass = 'tab';
                    if ($language === $lastLanguage) {
                        $buttonClass .= ' last'; // Añade una clase para el último botón
                    }
                    echo '<button class="' . $buttonClass . '" data-language="' . htmlspecialchars($language) . '">' . htmlspecialchars(ucfirst($language)) . '</button>';
                    if ($language !== $lastLanguage) {
                        echo '<div class="divider"></div>';
                    }
                }
            }
            ?>
        </div>
    <div class="all">
        <div class="general">
            <?php require 'views/charts/pieChart.php' ?>
            <div class="area">
                <!-- Hacer que sean grid para que se vean bien sin importar cuántos idiomas -->
                <div class="language-area"> 
                    <h3>LANGUAGE</h3><br>
                    <h3>Study hours: <span></span></h3>
                    <h3>Logs: <span></span></h3>
                    <h3>Day Streak: <span></span></h3>
                    <h3>Hours per day: <span></span></h3><br><br>
                </div>
                <div class="language-area">
                    <h3>LANGUAGE</h3><br>
                    <h3>Study hours: <span></span></h3>
                    <h3>Logs: <span></span></h3>
                    <h3>Day Streak: <span></span></h3>
                    <h3>Hours per day: <span></span></h3><br><br>
                </div>
                <div class="language-area">
                    <h3>LANGUAGE</h3><br>
                    <h3>Study hours: <span></span></h3>
                    <h3>Logs: <span></span></h3>
                    <h3>Day Streak: <span></span></h3>
                    <h3>Hours per day: <span></span></h3><br><br>
                </div>
                <div class="language-area">
                    <h3>LANGUAGE</h3><br>
                    <h3>Study hours: <span></span></h3>
                    <h3>Logs: <span></span></h3>
                    <h3>Day Streak: <span></span></h3>
                    <h3>Hours per day: <span></span></h3><br><br>
                </div>
                <div class="language-area">
                    <h3>LANGUAGE</h3><br>
                    <h3>Study hours: <span></span></h3>
                    <h3>Logs: <span></span></h3>
                    <h3>Day Streak: <span></span></h3>
                    <h3>Hours per day: <span></span></h3><br><br>
                </div>
            </div>
        </div>
    </div>
    <div class="language-tabs">
        <?php
        if (!empty($userLanguages)) {
            foreach ($userLanguages as $language) {
                echo '<div class="language-tab-content" id="' . htmlspecialchars($language) . '-tab">';
                echo '';
                echo '</div>';
            }
        }
        ?>
    </div>
</div>

<script>
    const tabButtons = document.querySelectorAll('.button-group button');
    const tabContents = document.querySelectorAll('.language-tabs .language-tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const language = button.dataset.language;

            // Desactivar todos los botones y ocultar todos los contenidos
            tabButtons.forEach(btn => btn.classList.remove('active-tab'));
            tabContents.forEach(content => content.style.display = 'none');

            // Activar el botón clicado y mostrar el contenido correspondiente
            button.classList.add('active-tab');
            if (language === 'all') {
                document.querySelector('.all').style.display = 'block'; // Mostrar el gráfico general
            } else {
                const targetContent = document.getElementById(language + '-tab');
                if (targetContent) {
                    targetContent.style.display = 'block';
                }
                document.querySelector('.all').style.display = 'none'; // Ocultar el gráfico general al seleccionar un idioma
            }
        });
    });

    // Mostrar el contenido "All" por defecto
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelector('.all').style.display = 'block';
    });
</script>
<!-- Terminamos la estructura -->
<?php endblock() ?>
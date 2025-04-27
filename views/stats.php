<?php //include 'views/base.php' ?>
<?php include 'src/views/base.php'; ?>
<!-- Iniciamos la estructura -->
<?php startblock('contenido') ?>
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
                <div class="all-langs">
                    <div class="pie-area">
                        <div class="chart">
                            <canvas id="all-pie-chart"></canvas>
                        </div>
                        <div class="area">
                            <?php if (!empty($userLanguages)): ?>
                                <div class="languages-grid">
                                    <?php foreach ($userLanguages as $index => $language): ?>
                                        <div class="language-area">
                                            <h2><?php echo htmlspecialchars($language); ?></h2><br>
                                            <h3>Study hours: <span><?php echo htmlspecialchars($datosIdioma['estadisticas_por_idioma'][$index]['total_horas'] ?? '0'); ?></span></h3>
                                            <h3>Logs: <span><?php echo htmlspecialchars($datosIdioma['estadisticas_por_idioma'][$index]['total_logs'] ?? '0'); ?></span></h3>
                                            <h3>Day Streak: <span><?php echo htmlspecialchars($datosIdioma['estadisticas_por_idioma'][$index]['day_streak'] ?? '0') ?></span></h3>
                                            <h3>Hours per day: <span><?php echo is_array($datosIdioma['estadisticas_por_idioma'][$index]['horas_por_dia'] ?? '') ? htmlspecialchars(implode(', ', $datosIdioma['estadisticas_por_idioma'][$index]['horas_por_dia'])) : htmlspecialchars($datosIdioma['estadisticas_por_idioma'][$index]['horas_por_dia'] ?? '0') ?></span></h3><br><br>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="hueco"></div>
                <div class="charts">
                    <div class="chart">
                        <h2>This week</h2>
                        <canvas id="all-line-chart" width="100px"></canvas>
                    </div>
                    <div class="chart">
                        <h2>This month</h2>
                        <canvas id="monthly-line-chart"></canvas>
                    </div>
                    <div class="chart">
                        <h2>This year</h2>
                        <canvas id="monthly-total-line-chart"></canvas>
                    </div>
                </div>
                
            </div>
        </div>
        <div class="language-tabs">
                    <?php
                    if (!empty($userLanguages)) {
                        foreach ($userLanguages as $language) {
                            $tabId = htmlspecialchars($language) . '-tab';
                            echo '<div class="language-tab-content" id="' . htmlspecialchars($language) . '-tab">';
                            ?>
                            <div class="general">
                                <div class="align-tabs">
                                    <div class="pie-area">
                                        <div class="area">
                                            <?php if (!empty($userLanguages)): ?>
                                                <div class="languages-grid">
                                                    <?php foreach ($userLanguages as $index => $lang): ?>
                                                        <?php
                                                        $currentLanguage = htmlspecialchars($lang);
                                                        $currentTabId = $currentLanguage . '-tab';
                                                        if ($tabId === $currentTabId) {
                                                        ?>
                                                            <div class="language-area">
                                                                <h2><?php echo $currentLanguage; ?></h2><br>
                                                                <h3>Study hours: <span><?php echo htmlspecialchars($datosIdioma['estadisticas_por_idioma'][$index]['total_horas'] ?? '0'); ?></span></h3>
                                                                <h3>Logs: <span><?php echo htmlspecialchars($datosIdioma['estadisticas_por_idioma'][$index]['total_logs'] ?? '0'); ?></span></h3>
                                                                <h3>Day Streak: <span><?php echo htmlspecialchars($datosIdioma['estadisticas_por_idioma'][$index]['day_streak'] ?? '0') ?></span></h3>
                                                                <h3>Hours per day: <span><?php echo is_array($datosIdioma['estadisticas_por_idioma'][$index]['horas_por_dia'] ?? '') ? htmlspecialchars(implode(', ', $datosIdioma['estadisticas_por_idioma'][$index]['horas_por_dia'])) : htmlspecialchars($datosIdioma['estadisticas_por_idioma'][$index]['horas_por_dia'] ?? '0') ?></span></h3><br><br>
                                                            </div>
                                                        <?php } ?>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="hueco"></div>
                                <div class="charts">
                                    <div class="chart">
                                        <h2>This week</h2>
                                        <canvas id="<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $language))); ?>-week-chart"></canvas>
                                    </div>
                                    <div class="chart">
                                        <h2>This month</h2>
                                        <canvas id="<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $language))); ?>-month-chart"></canvas>
                                    </div>
                                    <div class="chart">
                                        <h2>This year</h2>
                                        <canvas id="<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $language))); ?>-year-chart"></canvas>
                                    </div>
                                </div>
                                
                            </div>

                            <?php
                            echo '</div>';
                        }
                    }
                    ?>
                </div>
    </div>
</div>

<script>
    // Datos de PHP que pasaste a la vista
    const languagePercentages = <?php echo json_encode($languagePercentages); ?>;
    const tabButtons = document.querySelectorAll('.button-group button');
    const tabContents = document.querySelectorAll('.language-tabs .language-tab-content');
    const estadisticasPorIdioma = <?php echo json_encode($datosIdioma['estadisticas_por_idioma']); ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript" src="./js/stats.js"></script>
<!-- Terminamos la estructura -->
<?php endblock() ?>
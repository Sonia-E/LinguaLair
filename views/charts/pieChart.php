<head>
    <style>
        .chart {
            max-width: 900px;
            height: 450px;
        }
    </style>
</head>

<div class="chart">
    <canvas  id="all-pie-chart"></canvas>
</div>
  
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <script>
    const ctx = document.getElementById('all-pie-chart');
  
    // Datos de PHP que pasaste a la vista
    const languagePercentages = <?php echo json_encode($languagePercentages); ?>;

    // Arrays para almacenar las etiquetas (idiomas) y los datos (porcentajes)
    const labels = [];
    const dataValues = [];
    const backgroundColors = []; // Opcional: para personalizar los colores

    // Función para generar un color RGB aleatorio
    function randomRgb() {
        const r = Math.floor(Math.random() * 256);
        const g = Math.floor(Math.random() * 256);
        const b = Math.floor(Math.random() * 256);
        return `rgb(${r}, ${g}, ${b})`;
    }

    // Iterar sobre el array de porcentajes para llenar los arrays de Chart.js
    for (const language in languagePercentages) {
        if (languagePercentages.hasOwnProperty(language)) {
            labels.push(language);
            dataValues.push(languagePercentages[language]);
            backgroundColors.push(randomRgb()); // Generar un color aleatorio para cada idioma
        }
    }

    const data = {
        labels: labels,
        datasets: [{
            label: 'Porcentaje de Estudio por Idioma',
            data: dataValues,
            backgroundColor: backgroundColors,
            hoverOffset: 4
        }]
    };

    new Chart(ctx, {
        type: 'pie',
        data: data,
        options: {
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed !== null) {
                                label += context.parsed.toFixed(2) + '%';
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });

    
  </script>
let originalDefaultColor = Chart.defaults.color; // Guardamos el color por defecto original
let chartInstances = {}; // Objeto para almacenar las instancias de los gráficos

        function applyDarkMode(darkModeEnabled) {
            if (darkModeEnabled) {
                Chart.defaults.color = 'rgb(221, 221, 221)';
                localStorage.setItem('darkMode', 'enabled');
            } else {
                Chart.defaults.color = originalDefaultColor; // Restauramos el color original
                localStorage.setItem('darkMode', 'disabled');
            }
            updateAllChartColors();
        }

        function updateChartColors(chartInstance) {
            if (!chartInstance) return;

            const isDarkMode = localStorage.getItem('darkMode') === 'enabled';
            const textColor = isDarkMode ? 'rgb(221, 221, 221)' : originalDefaultColor;
            const gridlineColor = isDarkMode ? 'rgb(82, 82, 82)' : 'rgba(0, 0, 0, 0.1)';

            // Actualizamos colores comunes a todos los tipos de gráficos (si aplica)
            if (chartInstance.options.scales) {
                for (const scaleId in chartInstance.options.scales) {
                    chartInstance.options.scales[scaleId].ticks.color = textColor;
                    chartInstance.options.scales[scaleId].grid.color = gridlineColor;
                }
            }
            if (chartInstance.options.plugins?.legend?.labels) {
                chartInstance.options.plugins.legend.labels.color = textColor;
            }

            // Actualizamos colores específicos del tipo de gráfico
            chartInstance.data.datasets.forEach(dataset => {
                if (chartInstance.config.type !== 'pie') {
                    dataset.borderColor = isDarkMode ? 'rgba(255,255,255,1)' : 'transparent';
                    dataset.borderWidth = isDarkMode ? 2 : 0;
                }
            });
            chartInstance.update();
        }

        function updateAllChartColors() {
            for (const chartId in chartInstances) {
                updateChartColors(chartInstances[chartId]);
            }
        }

        window.addEventListener("load", () => {
            if (localStorage.getItem("darkMode") === "enabled") {
                applyDarkMode(true);
            } else {
                applyDarkMode(false);
            }
        });


document.querySelector('.switch').addEventListener('click', () => {
    
    if (localStorage.getItem("darkMode") !== "enabled") {
        applyDarkMode(true);
    } else {
        applyDarkMode(false);
    }
    
});

// ###########################################
// ############### TYPE PIE CHART ############
// ###########################################

// Iterar sobre el array de estadísticas por idioma
estadisticasPorIdioma.forEach(idiomaStats => {
    if (idiomaStats.hasOwnProperty('idioma') && idiomaStats.hasOwnProperty('type_percentages')) {
        const language = idiomaStats.idioma;
        const typePercentages = idiomaStats.type_percentages;
        const typeColors = {};

        // Seleccionamos el contenedor de gráficos *dentro* de la pestaña del idioma actual
        const tabId = `${language}-tab`; // ID de la pestaña (ej: japanese-tab)
        const languageTab = document.getElementById(tabId); // Obtenemos el elemento de la pestaña
        const typeChartsContainer = languageTab.querySelector('.pie-area'); // Seleccionamos el .chart dentro de la pestaña
        const area = languageTab.querySelector(".area");

        if (typeChartsContainer) { // Si se encuentra el contenedor de gráficos
            // Creamos un nuevo div para contener el gráfico de este idioma
            const chartContainer = document.createElement('div');
            chartContainer.classList.add('chart');

            // Creamos un nuevo canvas para el gráfico
            const canvas = document.createElement('canvas');
            canvas.id = `type-pie-chart-${language.replace(/\s+/g, '-').toLowerCase()}`; // ID único
            chartContainer.appendChild(canvas);

            // Añadimos el contenedor del gráfico al contenedor de la pestaña
            typeChartsContainer.insertBefore(chartContainer, area);

            const ctxType = canvas.getContext('2d');

            const typeLabels = Object.keys(typePercentages);
            const typeDataValues = Object.values(typePercentages);
            const typeBackgroundColors = []; // Array para almacenar los colores

            // Generamos colores y almacenarlos en el objeto typeColors
            typeLabels.forEach(typeLabel => {
                const color = randomRgb();
                typeColors[typeLabel] = color; // Almacenamos el color usando el type como clave
                typeBackgroundColors.push(color);
            });

            const typeData = {
                labels: typeLabels,
                datasets: [{
                    label: 'Percentage of Study by Type',
                    data: typeDataValues,
                    backgroundColor: typeBackgroundColors,
                    hoverOffset: 4
                }]
            };

            const languagePieChart = new Chart(ctxType, {
                type: 'pie',
                data: typeData,
                options: {
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function (context) {
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
            chartInstances[`type-pie-chart-${language.replace(/\s+/g, '-').toLowerCase()}`] = languagePieChart;

            // Almacenamos el objeto de colores específico del idioma en el elemento de la pestaña
            languageTab.dataset.typeColors = JSON.stringify(typeColors);
        } else {
            console.error(`No se encontró el contenedor .chart dentro de la pestaña ${tabId}`);
        }
    }
});

// ---------------------------------- Tabs system
tabButtons.forEach(button => {
    button.addEventListener('click', () => {
        const language = button.dataset.language;

        // Desactivamos todos los botones y ocultar todos los contenidos
        tabButtons.forEach(btn => btn.classList.remove('active-tab'));
        tabContents.forEach(content => content.style.display = 'none');
        

        // Activamos el botón clicado y mostrar el contenido correspondiente
        button.classList.add('active-tab');
        if (language === 'all') {
            document.querySelector('.all').style.display = 'block';
        } else {
            const targetContent = document.getElementById(language + '-tab');
            if (targetContent) {
                targetContent.style.display = 'block';

                // Obtenemos el objeto de colores específico del idioma desde el dataset
                const languageTypeColors = JSON.parse(targetContent.dataset.typeColors || '{}');

                // --- Gráfico de barras de la semana actual ----
                //------------------------------------------------
                // Obtenemos el canvas del gráfico del idioma
                const weekChartCanvas = targetContent.querySelector('canvas[id$="-week-chart"]');
                if (weekChartCanvas) {
                    const newId = `${language.toLowerCase().replace(' ', '-')}-week-chart`;
                    weekChartCanvas.id = newId;

                    const fetchedCanvas = document.getElementById(newId);

                    // Destruimos el gráfico existente si lo hay
                    if (fetchedCanvas && fetchedCanvas.__chart) {
                        fetchedCanvas.__chart.destroy();
                    }

                    // Creamos el gráfico de barras para el idioma activo
                    const languageStats = estadisticasPorIdioma.find(stats => stats.idioma === language);
                    const barDataLanguage = {
                        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], // Etiquetas de los días de la semana
                        datasets: [] // Array para almacenar los datasets de cada idioma
                    };
                    
                    if (languageStats && languageStats.hasOwnProperty('types_hours')) {
                        const typeStats = languageStats.types_hours;
                        const daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                        const barDataLanguage = {
                            labels: daysOfWeek,
                            datasets: []
                        };
                    
                        const allTypesForLanguage = Object.keys(typeStats);
                    
                        allTypesForLanguage.forEach(type => {
                            const typeDataForWeek = [0, 0, 0, 0, 0, 0, 0];
                    
                            for (const typeName in typeStats) { // Iteramos sobre los types en typeStats
                                if (typeName === type) { // Si el type actual coincide con el del bucle exterior
                                    for (const date in typeStats[typeName]) {
                                        if (typeStats[typeName].hasOwnProperty(date) && isDateInCurrentWeek(date)) {
                                            const dayOfWeek = new Date(date).toLocaleDateString('en-US', { weekday: 'short' });
                                            const dayIndex = daysOfWeek.indexOf(dayOfWeek);
                                            if (dayIndex !== -1) {
                                                typeDataForWeek[dayIndex] += typeStats[typeName][date];
                                            }
                                        }
                                    }
                                }
                            }
                    
                            barDataLanguage.datasets.push({
                                label: type,
                                data: typeDataForWeek,
                                backgroundColor: languageTypeColors [type] || randomRgb(),
                                borderWidth: 1
                            });
                        });

                        const chartAreaBorder = {
                            id: 'chartAreaBorder',
                            beforeDraw(chart, args, options) {
                                const { ctx, chartArea: { left, top, width, height } } = chart;
                                ctx.save();
                                ctx.strokeStyle = options.borderColor;
                                ctx.lineWidth = options.borderWidth;
                                ctx.setLineDash(options.borderDash || []);
                                ctx.lineDashOffset = options.borderDashOffset;
                                ctx.strokeRect(left, top, width, height);
                                ctx.restore();
                            }
                        };

                        if (fetchedCanvas) {
                            const ctxLanguageBar = fetchedCanvas.getContext('2d');
                            const myBarChartLanguage = new Chart(ctxLanguageBar, {
                                type: 'bar',
                                data: barDataLanguage,
                                options: {
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: 'Hours Studied'
                                            },
                                        }
                                    },
                                    plugins: {
                                        chartAreaBorder: {
                                            borderColor: 'gray',
                                            borderWidth: 1,
                                            borderDash: [5, 5],
                                            borderDashOffset: 1,
                                        }
                                    }
                                },
                                plugins: [chartAreaBorder]
                            });
                            chartInstances[`${language.toLowerCase().replace(' ', '-')}-week-chart`] = myBarChartLanguage;
                            // Almacenamos la instancia del gráfico en el canvas para futuras destrucciones
                            fetchedCanvas.__chart = myBarChartLanguage;
                        }
                    }
                }

                // --- Gráfico de barras del mes actual ----
                //-------------------------------------------
                const monthChartCanvas = targetContent.querySelector('canvas[id$="-month-chart"]');
                if (monthChartCanvas) {
                    const newIdMonth = `${language.toLowerCase().replace(' ', '-')}-month-chart`;
                    monthChartCanvas.id = newIdMonth;

                    const fetchedCanvasMonth = document.getElementById(newIdMonth);
                    if (fetchedCanvasMonth && fetchedCanvasMonth.__chart) {
                        fetchedCanvasMonth.__chart.destroy();
                    }

                    const languageStats = estadisticasPorIdioma.find(stats => stats.idioma === language);
                    if (languageStats && languageStats.hasOwnProperty('types_hours')) {
                        const typeStats = languageStats.types_hours;
                        const monthlyBarDataForLanguage = {
                            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'], // Etiquetas de las semanas
                            datasets: []
                        };

                        const allTypesInLanguage = Object.keys(typeStats);

                        allTypesInLanguage.forEach(type => {
                            const languageMonthlyTypeData = [0, 0, 0, 0, 0];
                            const backgroundColor = languageTypeColors[type] || randomRgb();

                            for (let i = 1; i <= 5; i++) {
                                let weeklyHours = 0;

                                for (const date in typeStats[type]) {
                                    if (typeStats[type].hasOwnProperty(date) && isDateInCurrentMonthWeek(date, i)) {
                                        const hours = typeStats[type][date];
                                        weeklyHours += hours;
                                    }
                                }
                                languageMonthlyTypeData[i - 1] = parseFloat(weeklyHours.toFixed(2));
                            }

                            monthlyBarDataForLanguage.datasets.push({
                                label: type,
                                data: languageMonthlyTypeData,
                                backgroundColor: backgroundColor,
                                borderWidth: 1
                            });
                        });

                        if (fetchedCanvasMonth) {
                            const ctxLanguageBarMonth = fetchedCanvasMonth.getContext('2d');
                            const myBarChartLanguageMonth = new Chart(ctxLanguageBarMonth, {
                                type: 'bar',
                                data: monthlyBarDataForLanguage,
                                options: {
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: 'Hours Studied'
                                            },
                                        },
                                        x: {
                                            title: {
                                                display: true,
                                                text: 'Week of Month'
                                            }
                                        }
                                    },
                                    plugins: {
                                        legend: {
                                            display: true,
                                        },
                                        chartAreaBorder: { borderColor: 'gray', borderWidth: 1, borderDash: [5, 5], borderDashOffset: 1 }
                                    }
                                },
                                plugins: [{ id: 'chartAreaBorder', beforeDraw: (chart) => { const { ctx, chartArea: { left, top, width, height } } = chart; ctx.save(); ctx.strokeStyle = chart.options.plugins.chartAreaBorder.borderColor; ctx.lineWidth = chart.options.plugins.chartAreaBorder.borderWidth; ctx.setLineDash(chart.options.plugins.chartAreaBorder.borderDash); ctx.lineDashOffset = chart.options.plugins.chartAreaBorder.borderDashOffset; ctx.strokeRect(left, top, width, height); ctx.restore(); } }]
                            });
                            chartInstances[`${language.toLowerCase().replace(' ', '-')}-month-chart`] = myBarChartLanguageMonth;
                            fetchedCanvasMonth.__chart = myBarChartLanguageMonth;
                        }
                    }
                }
                
                // ---- Gráfico de barras del año actual ----
                //--------------------------------------------
                const yearChartCanvas = targetContent.querySelector('canvas[id$="-year-chart"]');
                if (yearChartCanvas) {
                    const newIdYear = `${language.toLowerCase().replace(' ', '-')}-year-chart`;
                    yearChartCanvas.id = newIdYear;

                    const fetchedCanvasYear = document.getElementById(newIdYear);
                    if (fetchedCanvasYear && fetchedCanvasYear.__chart) {
                        fetchedCanvasYear.__chart.destroy();
                    }

                    const languageStats = estadisticasPorIdioma.find(stats => stats.idioma === language);
                    if (languageStats && languageStats.hasOwnProperty('types_hours')) {
                        const typeStats = languageStats.types_hours;
                        const yearlyBarDataForLanguage = {
                            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                            datasets: []
                        };
                    
                        const allTypesForLanguage = Object.keys(typeStats);
                        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    
                        allTypesForLanguage.forEach(type => {
                            const typeDataForYear = Array(12).fill(0);
                            const backgroundColor = languageTypeColors[type] || randomRgb();
                    
                            for (const monthIndex in months) {
                                const monthName = months[monthIndex];
                                for (const date in typeStats[type]) {
                                    if (typeStats[type].hasOwnProperty(date)) {
                                        const logDate = new Date(date);
                                        const month = logDate.getMonth();
                                        if (logDate.getMonth() === parseInt(monthIndex)) {
                                            typeDataForYear[monthIndex] += typeStats[type][date];
                                        }
                                    }
                                }
                            }
                    
                            yearlyBarDataForLanguage.datasets.push({
                                label: type,
                                data: typeDataForYear,
                                backgroundColor: backgroundColor,
                                borderWidth: 1
                            });
                        });

                        if (fetchedCanvasYear) {
                            const ctxLanguageBarYear = fetchedCanvasYear.getContext('2d');
                            const myBarChartLanguageYear = new Chart(ctxLanguageBarYear, {
                                type: 'bar',
                                data: yearlyBarDataForLanguage,
                                options: {
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: 'Hours Studied'
                                            }
                                        },
                                        x: {
                                            title: {
                                                display: true,
                                                text: 'Month'
                                            }
                                        }
                                    },
                                    plugins: {
                                        legend: {
                                            display: true, // Mostramos leyenda para los tipos
                                        },
                                        chartAreaBorder: { borderColor: 'gray', borderWidth: 1, borderDash: [5, 5], borderDashOffset: 1 }
                                    }
                                },
                                plugins: [{ id: 'chartAreaBorder', beforeDraw: (chart) => { const { ctx, chartArea: { left, top, width, height } } = chart; ctx.save(); ctx.strokeStyle = chart.options.plugins.chartAreaBorder.borderColor; ctx.lineWidth = chart.options.plugins.chartAreaBorder.borderWidth; ctx.setLineDash(chart.options.plugins.chartAreaBorder.borderDash); ctx.lineDashOffset = chart.options.plugins.chartAreaBorder.borderDashOffset; ctx.strokeRect(left, top, width, height); ctx.restore(); } }]
                            });
                            chartInstances[`${language.toLowerCase().replace(' ', '-')}-year-chart`] = myBarChartLanguageYear;
                            fetchedCanvasYear.__chart = myBarChartLanguageYear;
                        }
                    }
                }
            }
            document.querySelector('.all').style.display = 'none';
        }
        // Aplicamos el modo oscuro al cambiar de tab
        applyDarkMode(localStorage.getItem('darkMode') === 'enabled');
    });
});

// Actualizamos el gráfico inicialmente (si es necesario, por defecto "All" ya tiene datos)
document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.all').style.display = 'block';
    tabContents.forEach(content => content.style.display = 'none');
});

// ----------------- ALL LANGUAGES

// ###########################################
// ############### PIE CHART #################
// ###########################################

const ctx = document.getElementById('all-pie-chart');

// Objeto para almacenar la asignación de color por idioma
const languageColors = {};

// Arrays para almacenar las etiquetas (idiomas) y los datos (porcentajes)
const pieLabels = [];
const pieDataValues = [];
const pieBackgroundColors = []; // Opcional: para personalizar los colores

// Función para generar un color RGB aleatorio
function randomRgb() {
    const r = Math.floor(Math.random() * 256);
    const g = Math.floor(Math.random() * 256);
    const b = Math.floor(Math.random() * 256);
    return `rgb(${r}, ${g}, ${b})`;
}

// Iteramos sobre el array de porcentajes para llenar los arrays de Chart.js
for (const language in languagePercentages) {
    if (languagePercentages.hasOwnProperty(language)) {
        pieLabels.push(language);
        pieDataValues.push(languagePercentages[language]);
        const color = randomRgb();
        pieBackgroundColors.push(color); // Generamos y añadimos el color al array
        languageColors[language] = color; // Guardamos el color para el idioma
    }
}

const pieData = {
    labels: pieLabels,
    datasets: [{
        label: 'Porcentaje de Estudio por Idioma',
        data: pieDataValues,
        backgroundColor: pieBackgroundColors,
        hoverOffset: 4
    }]
};

const allPieChart = new Chart(ctx, {
    type: 'pie',
    data: pieData,
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

chartInstances['all-pie-chart'] = allPieChart;

// ###########################################
// ############# WEEK BAR CHART ##############
// ###########################################

const barData = {
    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], // Etiquetas de los días de la semana
    datasets: [] // Array para almacenar los datasets de cada idioma
};

// Función para verificar si una fecha está en la semana actual
function isDateInCurrentWeek(dateString) {
    const today = new Date();
    const firstDayOfWeek = new Date(today.setDate(today.getDate() - today.getDay() + (today.getDay() === 0 ? -6 : 1))); // Primer día de la semana (Lunes)
    const lastDayOfWeek = new Date(today.setDate(today.getDate() - today.getDay() + 7)); // Último día de la semana (Domingo)
    const checkDate = new Date(dateString);
    return checkDate >= firstDayOfWeek && checkDate <= lastDayOfWeek;
}

// Iteramos sobre el array de estadísticas por idioma para el gráfico de barras
estadisticasPorIdioma.forEach(idiomaStats => {
    if (idiomaStats.hasOwnProperty('idioma') && idiomaStats.hasOwnProperty('solo_horas')) {
        const languageLabel = idiomaStats.idioma;
        const languageData = [];
        const currentWeekStudyDays = {};
        const backgroundColor = languageColors[languageLabel] || randomRgb(); // Reutilizamos el color o generamos uno nuevo si no existe

        // Filtramos las solo_horas para la semana actual
        for (const date in idiomaStats.solo_horas) {
            if (idiomaStats.solo_horas.hasOwnProperty(date) && isDateInCurrentWeek(date)) {
                currentWeekStudyDays[date] = idiomaStats.solo_horas[date];
            }
        }
        
        // Asegurarse de que los datos de la semana actual coinciden con las etiquetas de los días
        const daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        const studyDays = Object.keys(currentWeekStudyDays);

        daysOfWeek.forEach(day => {
            let hoursForDay = 0;
            studyDays.forEach(studyDay => {
                const date = new Date(studyDay);
                const dayOfWeek = new Intl.DateTimeFormat('en-US', { weekday: 'short' }).format(date);
                if (dayOfWeek === day) {
                    hoursForDay = currentWeekStudyDays[studyDay];
                }
            });
            languageData.push(hoursForDay);
        });

        barData.datasets.push({
            label: languageLabel,
            data: languageData,
            backgroundColor: backgroundColor,
            borderWidth: 1
        });
    }
});

const chartAreaBorder = {
    id: 'chartAreaBorder',
    beforeDraw(chart, args, options) {
      const {ctx, chartArea: {left, top, width, height}} = chart;
      ctx.save();
      ctx.strokeStyle = options.borderColor;
      ctx.lineWidth = options.borderWidth;
      ctx.setLineDash(options.borderDash || []);
      ctx.lineDashOffset = options.borderDashOffset;
      ctx.strokeRect(left, top, width, height);
      ctx.restore();
    }
  };

const ctxBar = document.getElementById('all-line-chart').getContext('2d');
const weeklyBarChart = new Chart(ctxBar, {
    type: 'bar',
    data: barData,
    options: {
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Hours Studied'
                },
                
            }
        },
        plugins: {
            chartAreaBorder: {
              borderColor: 'gray',
              borderWidth: 1,
              borderDash: [5, 5],
              borderDashOffset: 1,
            }
          }
        },
    plugins: [chartAreaBorder]
});
chartInstances['all-line-chart'] = weeklyBarChart;

// ----------------- ALL LANGUAGES

// ###########################################
// ############### MONTHLY BAR CHART #########
// ###########################################

const monthlyBarData = {
    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'], // Etiquetas de las semanas del mes (aproximado)
    datasets: [] // Array para almacenar los datasets de cada idioma
};

// Función para verificar si una fecha está dentro de un rango de semanas del mes actual
function isDateInCurrentMonthWeek(dateString, weekNumber) {
    const date = new Date(dateString);
    const today = new Date();
    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    const weekStartDate = new Date(firstDayOfMonth);

    // Ajustamos para que la semana empiece el lunes
    const dayOfWeek = firstDayOfMonth.getDay(); // 0=Sunday, 1=Monday, ..., 6=Saturday
    if (dayOfWeek !== 1) {
        const daysToSubtract = (dayOfWeek === 0 ? 6 : dayOfWeek - 1);
        weekStartDate.setDate(firstDayOfMonth.getDate() - daysToSubtract);
    }

    const weekEndDate = new Date(weekStartDate);
    weekEndDate.setDate(weekStartDate.getDate() + 7);

    for (let i = 1; i < weekNumber; i++) {
        weekStartDate.setDate(weekStartDate.getDate() + 7);
        weekEndDate.setDate(weekEndDate.getDate() + 7);
    }

    return date >= weekStartDate && date <= weekEndDate && date.getMonth() === today.getMonth() && date.getFullYear() === today.getFullYear();
}

// Iteramos sobre el array de estadísticas por idioma
estadisticasPorIdioma.forEach(idiomaStats => {
    if (idiomaStats.hasOwnProperty('idioma') && idiomaStats.hasOwnProperty('solo_horas')) {
        const languageLabel = idiomaStats.idioma;
        const languageMonthlyData = [0, 0, 0, 0, 0]; // Inicializamos datos para 5 semanas
        const backgroundColor = languageColors[languageLabel] || randomRgb();

        for (let i = 1; i <= 5; i++) {
            for (const date in idiomaStats.solo_horas) {
                if (idiomaStats.solo_horas.hasOwnProperty(date) && isDateInCurrentMonthWeek(date, i)) {
                    languageMonthlyData[i - 1] += idiomaStats.solo_horas[date];
                }
            }
            languageMonthlyData[i - 1] = parseFloat(languageMonthlyData[i - 1].toFixed(2));
        }

        monthlyBarData.datasets.push({
            label: languageLabel,
            data: languageMonthlyData,
            backgroundColor: backgroundColor,
            borderWidth: 1
        });
    }
});

const ctxMonthlyBar = document.getElementById('monthly-line-chart').getContext('2d');
const myMonthlyBarChart = new Chart(ctxMonthlyBar, {
    type: 'bar',
    data: monthlyBarData,
    options: {
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Hours Studied'
                },
                
            }
        },
        plugins: {
            chartAreaBorder: {
              borderColor: 'gray',
              borderWidth: 1,
              borderDash: [5, 5],
              borderDashOffset: 1,
            }
          }
        },
    plugins: [chartAreaBorder]
});
chartInstances['monthly-line-chart'] = myMonthlyBarChart;

// ----------------- ALL LANGUAGES

// ###########################################
// ############### YEAR BAR CHART ############
// ###########################################

const monthlyTotalBarData = {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    datasets: []
};

// Iteramos sobre el array de estadísticas por idioma
estadisticasPorIdioma.forEach(idiomaStats => {
    if (idiomaStats.hasOwnProperty('idioma') && idiomaStats.hasOwnProperty('solo_horas')) {
        const languageLabel = idiomaStats.idioma;
        const languageMonthlyTotals = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        const backgroundColor = languageColors[languageLabel] || randomRgb();

        for (const date in idiomaStats.solo_horas) {
            if (idiomaStats.solo_horas.hasOwnProperty(date)) {
                const studyDate = new Date(date);
                const monthIndex = studyDate.getMonth(); // 0 = Jan, 1 = Feb, ... , 11 = Dec
                languageMonthlyTotals[monthIndex] += idiomaStats.solo_horas[date];
            }
        }

        // Redondeamos los totales mensuales
        const roundedTotals = languageMonthlyTotals.map(total => parseFloat(total.toFixed(2)));

        monthlyTotalBarData.datasets.push({
            label: languageLabel,
            data: roundedTotals,
            backgroundColor: backgroundColor,
            borderWidth: 1
        });
    }
});

const ctxMonthlyTotalBar = document.getElementById('monthly-total-line-chart').getContext('2d');
const myMonthlyTotalBarChart = new Chart(ctxMonthlyTotalBar, {
    type: 'bar',
    data: monthlyTotalBarData,
    options: {
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Hours Studied'
                }
            }
        },
        plugins: {
            chartAreaBorder: {
              borderColor: 'gray',
              borderWidth: 1,
              borderDash: [5, 5],
              borderDashOffset: 1,
            }
          }
        },
    plugins: [chartAreaBorder]
});
chartInstances['monthly-total-line-chart'] = myMonthlyTotalBarChart;
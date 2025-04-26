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

// Iterar sobre el array de porcentajes para llenar los arrays de Chart.js
for (const language in languagePercentages) {
    if (languagePercentages.hasOwnProperty(language)) {
        pieLabels.push(language);
        pieDataValues.push(languagePercentages[language]);
        const color = randomRgb();
        pieBackgroundColors.push(color); // Generar y añadir el color al array
        languageColors[language] = color; // Guardar el color para el idioma
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

new Chart(ctx, {
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

// ###########################################
// ############### BAR CHART ################
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

// Iterar sobre el array de estadísticas por idioma para el gráfico de barras
estadisticasPorIdioma.forEach(idiomaStats => {
    if (idiomaStats.hasOwnProperty('idioma') && idiomaStats.hasOwnProperty('solo_horas')) {
        const languageLabel = idiomaStats.idioma;
        const languageData = [];
        const currentWeekStudyDays = {};
        const backgroundColor = languageColors[languageLabel] || randomRgb(); // Reutilizar el color o generar uno nuevo si no existe

        // Filtrar las solo_horas para la semana actual
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
const myBarChart = new Chart(ctxBar, {
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

    // Ajustar para que la semana empiece el lunes (si no lo hace por defecto)
    const dayOfWeek = firstDayOfMonth.getDay(); // 0=Sunday, 1=Monday, ..., 6=Saturday
    if (dayOfWeek !== 1) {
        const daysToSubtract = (dayOfWeek === 0 ? 6 : dayOfWeek - 1);
        weekStartDate.setDate(firstDayOfMonth.getDate() - daysToSubtract);
    }

    const weekEndDate = new Date(weekStartDate);
    weekEndDate.setDate(weekStartDate.getDate() + 6);

    for (let i = 1; i < weekNumber; i++) {
        weekStartDate.setDate(weekStartDate.getDate() + 7);
        weekEndDate.setDate(weekEndDate.getDate() + 7);
    }

    return date >= weekStartDate && date <= weekEndDate && date.getMonth() === today.getMonth() && date.getFullYear() === today.getFullYear();
}

// Iterar sobre el array de estadísticas por idioma
estadisticasPorIdioma.forEach(idiomaStats => {
    if (idiomaStats.hasOwnProperty('idioma') && idiomaStats.hasOwnProperty('solo_horas')) {
        const languageLabel = idiomaStats.idioma;
        const languageMonthlyData = [0, 0, 0, 0, 0]; // Inicializar datos para 5 semanas
        const backgroundColor = languageColors[languageLabel] || randomRgb();

        for (let i = 1; i <= 5; i++) {
            for (const date in idiomaStats.solo_horas) {
                if (idiomaStats.solo_horas.hasOwnProperty(date) && isDateInCurrentMonthWeek(date, i)) {
                    languageMonthlyData[i - 1] += idiomaStats.solo_horas[date];
                }
            }
            languageMonthlyData[i - 1] = parseFloat(languageMonthlyData[i - 1].toFixed(2)); // Redondear
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

// ----------------- ALL LANGUAGES

// ###########################################
// ############### YEAR BAR CHART ####
// ###########################################

const monthlyTotalBarData = {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    datasets: []
};

// Iterar sobre el array de estadísticas por idioma
estadisticasPorIdioma.forEach(idiomaStats => {
    if (idiomaStats.hasOwnProperty('idioma') && idiomaStats.hasOwnProperty('solo_horas')) {
        const languageLabel = idiomaStats.idioma;
        const languageMonthlyTotals = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        const backgroundColor = languageColors[languageLabel] || randomRgb();

        console.log(idiomaStats);
        for (const date in idiomaStats.solo_horas) {
            if (idiomaStats.solo_horas.hasOwnProperty(date)) {
                const studyDate = new Date(date);
                const monthIndex = studyDate.getMonth(); // 0 = Jan, 1 = Feb, ... , 11 = Dec
                languageMonthlyTotals[monthIndex] += idiomaStats.solo_horas[date];
            }
        }

        // Redondear los totales mensuales
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

// ----------------- ONLY ONE LANGUAGE

// ###########################################
// ############### TYPE PIE CHART ##############
// ###########################################

// Iterar sobre el array de estadísticas por idioma
estadisticasPorIdioma.forEach(idiomaStats => {
    if (idiomaStats.hasOwnProperty('idioma') && idiomaStats.hasOwnProperty('type_percentages')) {
        const language = idiomaStats.idioma;
        const typePercentages = idiomaStats.type_percentages;

        // Seleccionar el contenedor de gráficos *dentro* de la pestaña del idioma actual
        const tabId = `${language}-tab`; // ID de la pestaña (ej: japanese-tab)
        const languageTab = document.getElementById(tabId); // Obtener el elemento de la pestaña
        const typeChartsContainer = languageTab.querySelector('.pie-area'); // Seleccionar .chart dentro de la pestaña
        const area = languageTab.querySelector(".area");

        if (typeChartsContainer) { // Si se encuentra el contenedor de gráficos
            // Crear un nuevo div para contener el gráfico de este idioma
            const chartContainer = document.createElement('div');
            chartContainer.classList.add('chart');

            // Crear un nuevo canvas para el gráfico
            const canvas = document.createElement('canvas');
            canvas.id = `type-pie-chart-${language.replace(/\s+/g, '-').toLowerCase()}`; // ID único
            chartContainer.appendChild(canvas);

            // Añadir el contenedor del gráfico al contenedor de la pestaña
            typeChartsContainer.insertBefore(chartContainer, area);

            const ctxType = canvas.getContext('2d');

            const typeLabels = Object.keys(typePercentages);
            const typeDataValues = Object.values(typePercentages);
            const typeBackgroundColors = typeLabels.map(() => randomRgb());

            const typeData = {
                labels: typeLabels,
                datasets: [{
                    label: 'Percentage of Study by Type',
                    data: typeDataValues,
                    backgroundColor: typeBackgroundColors,
                    hoverOffset: 4
                }]
            };

            new Chart(ctxType, {
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
        } else {
            console.error(`No se encontró el contenedor .chart dentro de la pestaña ${tabId}`);
        }
    }
});

// function adjustPieAreaPosition() {
//     const sidebar = document.querySelector('.barra-lateral, .mini-barra-lateral'); // Selector para ambos estados
//     const pieArea = document.querySelector('.pie-area');
//     const mainContent = document.querySelector('.all'); // O el contenedor principal del contenido

//     if (!sidebar || !pieArea || !mainContent) {
//         console.log("Se salió");
//         return; // Si no se encuentran los elementos, salir
//     }

//     console.log("Aquí está");
//     const isSidebarMini = sidebar.classList.contains('mini-barra-lateral');
//     const sidebarWidth = isSidebarMini ? sidebar.offsetWidth : sidebar.offsetWidth; // El ancho será diferente

//     const mainContentWidth = window.innerWidth - sidebarWidth; // Ancho del área principal

//     // Calcular la posición 'left' para centrar el pie-area en la mitad izquierda del mainContent
//     const desiredLeft = sidebarWidth + (mainContentWidth / 2) - (pieArea.offsetWidth / 2);

//     pieArea.style.left = `${desiredLeft}px`;
// }

// // Llama a la función inicialmente al cargar la página
// document.addEventListener('DOMContentLoaded', adjustPieAreaPosition);

// Llama a la función cada vez que la barra lateral cambia de estado
const sidebarToggleButton = document.querySelector('.cloud'); // Selector de tu botón de toggle (ajusta esto)
if (sidebarToggleButton) {
    console.log("Pulsando");
    // sidebarToggleButton.addEventListener('click', adjustPieAreaPosition);
}

// // También es posible que necesites ajustar la posición si la ventana cambia de tamaño
// window.addEventListener('resize', adjustPieAreaPosition);
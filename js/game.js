function animateValue(obj, start, end, duration, callback) {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        obj.innerText = Math.floor(progress * (end - start) + start) + '%';
        if (progress < 1) {
            window.requestAnimationFrame(step);
        } else if (callback) {
            callback();
        }
    };
    window.requestAnimationFrame(step);
}

function animateProgressBar(obj, start, end, duration, callback) {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        obj.style.width = Math.floor(progress * (end - start) + start) + '%';
        // Actualizar la posición del texto durante la animación de la barra
        updateTextPosition();
        if (progress < 1) {
            window.requestAnimationFrame(step);
        } else if (callback) {
            callback();
        }
    };
    window.requestAnimationFrame(step);
}

function updateTextPosition() {
    const experienceBar = document.getElementById('experience-bar');
    const experienceText = document.getElementById('experience-text');

    if (experienceBar && experienceText) {
        const barWidth = experienceBar.offsetWidth;
        const textWidth = experienceText.offsetWidth;
        const marginLeft = Math.max(0, (barWidth - textWidth) / 2);
        experienceText.style.marginLeft = `${marginLeft}px`;
    }
}

function updateExperienceAnimated(newExperience) {
    const experienceTextElement = document.getElementById('experience-text');
    const experienceBarElement = document.getElementById('experience-bar');
    const currentExperience = parseInt(experienceTextElement.innerText);
    const duration = 500;

    if (newExperience === 0 && currentExperience !== 0) {
        const toFullDuration = Math.max(0, 500 * (1 - currentExperience / 100));
        animateValue(experienceTextElement, currentExperience, 100, toFullDuration, () => {
            animateProgressBar(experienceBarElement, currentExperience, 100, toFullDuration, () => {
                experienceTextElement.innerText = '0%';
                experienceBarElement.style.width = '0%';
                updateTextPosition(); // Asegurar la posición correcta en 0
                animateValue(experienceTextElement, 0, newExperience, duration);
                animateProgressBar(experienceBarElement, 0, newExperience, duration);
            });
        });
    } else {
        animateValue(experienceTextElement, currentExperience, newExperience, duration, () => {
            experienceTextElement.innerText = `${newExperience}%`;
            updateTextPosition(); // Asegurar la posición correcta al final
        });
        animateProgressBar(experienceBarElement, currentExperience, newExperience, duration);
    }
}

function updateLevel(newLevel) {
    const levelValueElement = document.getElementById('level-value');
    levelValueElement.innerText = newLevel;
}

// Llama a updateTextPosition al cargar la página para posicionar el texto inicial
document.addEventListener('DOMContentLoaded', updateTextPosition);

// Llama a updateTextPosition si la ventana cambia de tamaño (opcional)
window.addEventListener('resize', updateTextPosition);

  document.getElementById('addLogForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Evitar la recarga de página por defecto

    const formData = new FormData(this);

    fetch('log', { // Reemplaza con la URL correcta de tu controlador
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateExperienceAnimated(data.nuevaExperiencia);
            updateLevel(data.nuevoNivel);
            console.log('Log guardado y experiencia actualizada:', data);
            document.getElementById('addLogForm').reset();

            // --- Aquí agrega la lógica para cerrar el popup ---
            const popup = document.getElementById('myPopup'); // Reemplaza 'myPopup' con el ID real de tu popup
            const overlay = document.getElementById('overlay'); // Reemplaza 'overlay' si lo usas

            if (popup) {
                popup.classList.remove("show"); // Si usas una clase para controlar la visibilidad
            }
            if (overlay) {
                overlay.style.visibility = "hidden"; // Oculta la capa oscura si la tienes
            }

            // O cualquier otra lógica que uses para cerrar tu popup
        } else {
            console.error('Error al guardar el log:', data.error);
            // Opcional: Mostrar un mensaje de error en el popup
        }
    })
    .catch(error => {
        console.error('Error de red:', error);
        // Opcional: Mostrar un mensaje de error de red al usuario
    });
});
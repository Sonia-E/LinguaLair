// ------------ Set Profile: Language JSON list for select ----------
const nativeLanguage1 = document.getElementById('language1');
const nativeLanguage2 = document.getElementById('language2');
const learningLanguage1 = document.getElementById('language3');
const learningLanguage2 = document.getElementById('language4');
const learningLanguage3 = document.getElementById('language5');
const gistUrl = 'https://gist.githubusercontent.com/joshuabaker/d2775b5ada7d1601bcd7b31cb4081981/raw/languages.json';

fetch(gistUrl)
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(languageData => {
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'Select a language (optional)';
        defaultOption.selected = true;

        // Añadimos la opción por defecto a los selects de idiomas nativos
        nativeLanguage1.appendChild(defaultOption.cloneNode(true));
        nativeLanguage2.appendChild(defaultOption.cloneNode(true));

        // Añadimos la opción por defecto a los selects de idiomas aprendiendo
        learningLanguage1.appendChild(defaultOption.cloneNode(true));
        learningLanguage2.appendChild(defaultOption.cloneNode(true));
        learningLanguage3.appendChild(defaultOption.cloneNode(true));

        languageData.forEach(language => {
            const option = document.createElement('option');
            option.value = language.name;
            option.textContent = `${language.name} (${language.native})`;

            // Añadimos las opciones de idioma a los selects de idiomas nativos
            nativeLanguage1.appendChild(option.cloneNode(true));
            nativeLanguage2.appendChild(option.cloneNode(true));

            // Añadimos las opciones de idioma a los selects de idiomas aprendiendo
            learningLanguage1.appendChild(option.cloneNode(true));
            learningLanguage2.appendChild(option.cloneNode(true));
            learningLanguage3.appendChild(option.cloneNode(true));
        });
    })
    .catch(error => {
        console.error('Error fetching or parsing language data:', error);
    });

// DARK MODE
document.addEventListener('DOMContentLoaded', () => {
    const darkModeYes = document.getElementById('dark_yes');
    const darkModeNo = document.getElementById('dark_no');
    const body = document.body;
    const circulo = document.querySelector(".circulo");

    if (localStorage.getItem("darkMode") === "enabled") {
        darkModeYes.checked = true;
    } else {
        darkModeNo.checked = true;
    }

    // Función para aplicar el modo oscuro
    function applyDarkMode(enable) {
        if (enable) {
            body.classList.add('dark-mode');
            circulo.classList.add("pulsado");
            localStorage.setItem('darkMode', 'enabled'); // Guarda "enabled"
        } else {
            body.classList.remove('dark-mode');
            circulo.classList.remove("pulsado");
            localStorage.setItem('darkMode', 'disabled'); // Guarda "disabled"
        }
    }

    // Comprobamos el estado guardado al cargar la página
    if (localStorage.getItem('darkMode') === 'enabled') {
        darkModeYes.checked = true;
        applyDarkMode(true);
    } else {
        darkModeNo.checked = true;
        applyDarkMode(false);
    }

    // Event listeners para los botones de radio
    darkModeYes.addEventListener('change', () => {
        applyDarkMode(true); // Activa el modo oscuro
    });

    darkModeNo.addEventListener('change', () => {
        applyDarkMode(false); // Desactiva el modo oscuro
    });
});

// DeleteUser Popup
const deleteBtn = document.querySelector(".delete-user-btn");
const DeletePopup = document.getElementById("DeletePopup");
const overlayProfile = document.getElementById("overlay");
const closeButtonProfile = DeletePopup.querySelector(".close-button");


deleteBtn.addEventListener("click", () => {
    DeletePopup.classList.add("show");
    overlayProfile.style.visibility = "visible";
});

// Cerramos el popup al hacer clic en el overlay
    overlayProfile.addEventListener("click", () => {
    DeletePopup.classList.remove("show");
    overlayProfile.style.visibility = "hidden";
});

// Cerramos el popup con un botón dentro
if (closeButtonProfile) {
    closeButtonProfile.addEventListener("click", () => {
        DeletePopup.classList.remove("show");
        overlayProfile.style.visibility = "hidden";
    });
}

// Delete Yes Button
const deleteLink = document.querySelector('.link-delete-yes');
if (deleteLink) {
    deleteLink.addEventListener('click', function(event) {
        if (DeletePopup) {
            DeletePopup.classList.remove("show");
        }
        if (overlayProfile) {
            overlayProfile.style.visibility = "hidden";
        }
    });
}

// Delete No Button
const deleteNo = document.querySelector('.delete-no');
if (deleteNo) {
    deleteNo.addEventListener('click', () => {
        DeletePopup.classList.remove("show");
        overlayProfile.style.visibility = "hidden";
    });
}


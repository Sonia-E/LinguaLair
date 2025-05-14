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

    // Comprueba el estado guardado al cargar la página
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

// Cerrar el popup al hacer clic en el overlay
    overlayProfile.addEventListener("click", () => {
    DeletePopup.classList.remove("show");
    overlayProfile.style.visibility = "hidden";
});

// Cerrar el popup con un botón dentro
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
        // event.preventDefault(); // Evita la acción por defecto del enlace
        if (DeletePopup) {
            DeletePopup.classList.remove("show"); // Si usas una clase para controlar la visibilidad
        }
        if (overlayProfile) {
            overlayProfile.style.visibility = "hidden"; // Oculta la capa oscura si la tienes
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
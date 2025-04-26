// DeleteLog Popup
const deleteLogBtn = document.querySelector(".delete_log");
const DeleteLogPopup = document.getElementById("DeleteLogPopup");
const overlayLog = document.getElementById("overlay");
const closeButtonLog = DeleteLogPopup.querySelector(".close-button");

const deleteLogBtns = document.querySelectorAll('.delete_log'); // Asegúrate de que esta sea la clase de tus botones de eliminar
const logIdentifierInput = document.getElementById("log_identifier"); // Referencia al input hidden del formulario

deleteLogBtns.forEach(deleteLogBtn => {
    deleteLogBtn.addEventListener("click", () => {
        const logElement = deleteLogBtn.closest('.dropdown'); // Encontrar el log padre
        if (logElement) {
            const currentLogIdentifier = logElement.dataset.logIdentifier;
            console.log('Log Identifier del botón clickeado:', currentLogIdentifier);

            DeleteLogPopup.classList.add("show");
            overlayLog.style.visibility = "visible";

            // Actualiza el valor del input hidden con el log identifier
            logIdentifierInput.value = currentLogIdentifier;
        } else {
            console.error('No se encontró el elemento log padre.');
        }
    });
});

// Cerrar el popup al hacer clic en el overlay
    overlayLog.addEventListener("click", () => {
    DeleteLogPopup.classList.remove("show");
    overlayLog.style.visibility = "hidden";
    logIdentifierInput.value = '';
});

// Cerrar el popup con un botón dentro
if (closeButtonLog) {
    closeButtonLog.addEventListener("click", () => {
        DeleteLogPopup.classList.remove("show");
        overlayLog.style.visibility = "hidden";
        logIdentifierInput.value = '';
    });
}

// // Delete Yes Button
// const deleteLink = document.querySelector('.link-delete-yes'); // Reemplaza con el selector de tu botón de "Eliminar"
// if (deleteLink) {
//     deleteLink.addEventListener('click', function(event) {
//         // event.preventDefault(); // Evita la acción por defecto del enlace
//         if (DeleteLogPopup) {
//             DeleteLogPopup.classList.remove("show"); // Si usas una clase para controlar la visibilidad
//         }
//         if (overlayProfile) {
//             overlayProfile.style.visibility = "hidden"; // Oculta la capa oscura si la tienes
//         }
//     });
// }

// Delete No Button
const deleteNo = document.querySelector('.delete-log-no');
if (deleteNo) {
    deleteNo.addEventListener('click', () => {
        DeleteLogPopup.classList.remove("show");
        overlayLog.style.visibility = "hidden";
        logIdentifierInput.value = '';
    });
}

  document.getElementById('deleLogForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Evitar la recarga de página por defecto
    console.log("Entramos en el fetch");

    const formData = new FormData(this);

    fetch('delete_log', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // CREAR MÉTODOS OPUESTOS PARA QUITAR LA EXPERIENCIA O EL NIVEL GANADO
            // updateExperienceAnimated(data.nuevaExperiencia);
            // updateLevel(data.nuevoNivel);
            // console.log('Log guardado y experiencia actualizada:', data);
            document.getElementById('deleLogForm').reset();

            // --- Lógica para cerrar el popup ---
            if (DeleteLogPopup) {
                DeleteLogPopup.classList.remove("show");
            }
            if (overlayLog) {
                overlayLog.style.visibility = "hidden";
            }

            // --- Reload the feed content ---
            fetch('get_feed')
            .then(feedResponse => feedResponse.text())
            .then(feedHtml => {
                const followingDiv = document.querySelector('.following.show');
                if (followingDiv) {
                    followingDiv.innerHTML = feedHtml;
                    setTimeout(() => {
                        location.reload();
                    }, "1000");
                } else {
                    console.error('No se encontró el elemento .following.show para recargar el feed.');
                }
            })
            .catch(error => {
                console.error('Error al recargar el feed:', error);
            });
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
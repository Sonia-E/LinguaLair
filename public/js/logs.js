document.addEventListener('DOMContentLoaded', function() {
    const logContainer = document.querySelector('.show'); // O un ancestor estático más cercano

    if (logContainer) {
        logContainer.addEventListener('click', function(event) {
            const logOptionsButton = event.target.closest('.log_options_btn');

            if (logOptionsButton) {
                event.stopPropagation();
                const logContainer = logOptionsButton.closest('.log-container');
                const optionsPopup = logContainer.querySelector('.log-options-popup');

                document.querySelectorAll('.log-options-popup.show').forEach(otherPopup => {
                    if (otherPopup !== optionsPopup) {
                        otherPopup.classList.remove('show');
                    }
                });
                optionsPopup.classList.toggle('show');
            }
        });
    }

    // Listener para ocultar el popup (este puede permanecer en document)
    document.addEventListener('click', function(event) {
        document.querySelectorAll('.log-options-popup.show').forEach(optionsPopup => {
            if (!event.target.closest('.log-container') || !event.target.closest('.log_options_btn')) {
                optionsPopup.classList.remove('show');
            }
        });
    });
});

// ----------------------- DeleteLog Popup
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

// ----------------------- EditLog Popup
const EditLogPopup = document.getElementById("EditLogPopup");
// const overlayLog = document.getElementById("overlay");
const closeButtonEditLog = EditLogPopup.querySelector(".close-button");
const editLogForm = document.getElementById('editLogForm');
const editLogBtns = document.querySelectorAll('.edit_log');
const editLogIdentifierInput = document.getElementById("edit_log_identifier"); // Referencia al input hidden del formulario

editLogBtns.forEach(editLogBtn => {
    editLogBtn.addEventListener("click", () => {
        const logElement = editLogBtn.closest('.dropdown'); // Encontrar el log padre
        if (logElement) {
            const currentLogIdentifier = logElement.dataset.logIdentifier;

            // Reseteamos el formulario ANTES de mostrar el popup
            if (editLogForm) {
                editLogForm.reset();
            }

            EditLogPopup.classList.add("show");
            overlayLog.style.visibility = "visible";

            // Actualiza el valor del input hidden con el log identifier
            editLogIdentifierInput.value = currentLogIdentifier;

            fetch('get_log_data', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ log_identifier: currentLogIdentifier })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Asegúrate de que el popup sea visible ANTES de rellenar
                            if (EditLogPopup.classList.contains('show')) {
                                document.getElementById('edit_description').value = data.log.description;
                                document.getElementById('edit_language').value = data.log.language;
                                // Establecer la opción selected para el tipo de actividad
                            const typeSelect = document.getElementById('edit_type');
                            const logType = data.log.type;
                            for (let i = 0; i < typeSelect.options.length; i++) {
                                if (typeSelect.options[i].value.toLowerCase() === logType.toLowerCase()) {
                                    typeSelect.selectedIndex = i;
                                    break;
                                }
                            }
                                const dateObject = new Date(data.log.log_date);
                                const formattedDate = dateObject.toISOString().split('T')[0];
                                document.getElementById('edit_date').value = formattedDate;
                                document.getElementById('edit_log_identifier').value = currentLogIdentifier;
                            } else {
                                console.warn('El popup de edición no está visible al intentar rellenar los datos.');
                            }
                        } else {
                            console.error('Error al obtener los datos del log para editar:', data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error de red al obtener los datos del log:', error);
                    });
        } else {
            console.error('No se encontró el elemento log padre.');
        }
    });
});

// Cerrar el popup al hacer clic en el overlay
    overlayLog.addEventListener("click", () => {
    EditLogPopup.classList.remove("show");
    overlayLog.style.visibility = "hidden";
    logIdentifierInput.value = '';
});

// Cerrar el popup con un botón dentro
if (closeButtonEditLog) {
    closeButtonEditLog.addEventListener("click", () => {
        EditLogPopup.classList.remove("show");
        overlayLog.style.visibility = "hidden";
        editLogIdentifierInput.value = '';
    });
}

document.getElementById('editLogForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Evitar la recarga de página por defecto
    console.log("Entramos en el fetch");

    const formData = new FormData(this);

    fetch('edit_log', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('editLogForm').reset();

            // --- Lógica para cerrar el popup ---
            if (EditLogPopup) {
                EditLogPopup.classList.remove("show");
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
            console.error('Error al editar el log:', data.error);
            // Opcional: Mostrar un mensaje de error en el popup
        }
    })
    .catch(error => {
        console.error('Error de red:', error);
        // Opcional: Mostrar un mensaje de error de red al usuario
    });
});
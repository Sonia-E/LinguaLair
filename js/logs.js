// DeleteLog Popup
const deleteLogBtn = document.querySelector(".delete_log");
const DeleteLogPopup = document.getElementById("DeleteLogPopup");
const overlayLog = document.getElementById("overlay");
const closeButtonLog = DeleteLogPopup.querySelector(".close-button");

deleteLogBtn.addEventListener("click", () => {
    DeleteLogPopup.classList.add("show");
    overlayLog.style.visibility = "visible";
});

// Cerrar el popup al hacer clic en el overlay
    overlayLog.addEventListener("click", () => {
    DeleteLogPopup.classList.remove("show");
    overlayLog.style.visibility = "hidden";
});

// Cerrar el popup con un botón dentro
if (closeButtonLog) {
    closeButtonLog.addEventListener("click", () => {
        DeleteLogPopup.classList.remove("show");
        overlayLog.style.visibility = "hidden";
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
    });
}
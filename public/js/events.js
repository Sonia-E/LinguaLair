const events = document.querySelectorAll('.feed .event');
const profile = document.querySelector(".dashboard .profile");
const feed = document.querySelector(".dashboard .feed");
let isProfileVisible = false; // Inicialmente el perfil está oculto
let currentActiveEventId = null; // Variable para almacenar el ID del evento actualmente mostrado

events.forEach(event => {
    event.addEventListener("click", () => {
        const eventId = event.dataset.eventIdentifier;
        console.log("Clicked on event ID:", eventId);

        if (eventId === currentActiveEventId && isProfileVisible) {
            // Clic en el mismo evento que ya está abierto, hacer toggle de la visibilidad
            profile.classList.add("hidden");
            feed.classList.remove("profile-hidden");
            isProfileVisible = false;
            currentActiveEventId = null;
        } else {
            // Clic en un evento diferente o el perfil está cerrado
            fetch(`event_details?id=${eventId}`)
                .then(response => response.json())
                .then(data => {
                    console.log("Event details:", data);
                    updateProfile(data);
                    if (!isProfileVisible) {
                        profile.classList.remove("hidden");
                        feed.classList.add("profile-hidden");
                        isProfileVisible = true;
                    }
                    currentActiveEventId = eventId; // Actualizar el ID del evento activo
                })
                .catch(error => {
                    console.error("Error fetching event details:", error);
                });
        }
    });
});

function updateProfile(eventData) {
    // ... (Tu función updateProfile actual - sin cambios necesarios)
}

// Inicialización
profile.classList.add("hidden");
feed.classList.remove("profile-hidden");
function updateProfile(eventData) {
    // Selecciona los elementos dentro de .profile que necesitas actualizar
    const profileName = profile.querySelector(".header .event-name");
    const profileSubtypeExchange = profile.querySelector(".header .event-subtype .exchange-type");
    const profileExchangeLangs = profileSubtypeExchange.querySelector(".exchange-langs");
    const profileMainLang = profile.querySelector(".header .event-subtype .main-lang");
    const profileLearningLang = profile.querySelector(".header .event-subtype .learning-lang");
    const profileEventDate = profile.querySelector(".user-details .languages .event-date span");
    const profileEventType = profile.querySelector(".user-details .languages .event-type");
    const profileCreationDateSpan = profile.querySelector(".user-details .bio .languages span:nth-child(3)");
    const profileLocation = profile.querySelector(".user-details .languages .location");
    const profileCity = profileLocation.querySelector("span span:first-child");
    const profileCountry = profileLocation.querySelector("span span:last-child");
    const profileDescription = profile.querySelector(".user-details .bio .text span");
    const profileLongDescription = profile.querySelector(".user-details .stats .long-description p");

    // Actualiza el contenido con los datos del evento
    profileName.textContent = eventData.name;
    if (eventData.subtype === 'Language Exchange') {
        profileExchangeLangs.textContent = `${eventData.exchange_lang_1} - ${eventData.exchange_lang_2}`;
    } else {
        profileExchangeLangs.textContent = '';
        profileSubtypeExchange.style.display = 'none';
    }
    profileMainLang.classList.toggle("hidden", !eventData.main_lang);
    if (eventData.main_lang) {
        profileMainLang.textContent = `Event language: ${eventData.main_lang}`;
    }
    profileLearningLang.classList.toggle("hidden", !eventData.learning_lang);
    if (eventData.learning_lang) {
        profileLearningLang.textContent = `Target language: ${eventData.learning_lang}`;
    }
    profileEventDate.textContent = `Event Date: ${eventData.event_date}`;
    profileEventType.textContent = eventData.type;
    if (profileCreationDateSpan) {
        // Para mantener solo la fecha, podrías necesitar manipular el texto
        const fullText = profileCreationDateSpan.textContent;
        const datePart = fullText.substring(fullText.indexOf(':') + 2); // Extrae la parte después de ": "
        profileCreationDateSpan.textContent = datePart;
    } else {
        console.error("No se encontró el span de la fecha de creación en el perfil.");
    }
    profileLocation.classList.toggle("hidden", !eventData.city);
    if (eventData.city) {
        profileCity.textContent = eventData.city;
        profileCountry.textContent = eventData.country;
    }
    profileDescription.textContent = eventData.description;
    profileLongDescription.textContent = eventData.long_description;

    // Opcional: Asegurarse de que el botón "Attend" también se actualice si es necesario
}
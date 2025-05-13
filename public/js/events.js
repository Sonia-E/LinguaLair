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
                    updateEvent(data);
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

// Inicialización
profile.classList.add("hidden");
feed.classList.remove("profile-hidden");
function updateEvent(eventData) {
    // Selecciona los elementos dentro de .profile que tenemos que actualizar
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

    const attendButton = profile.querySelector(".attend button");
    const attendButtonSpan = attendButton.querySelector("span");

    if (eventData.attending) {
        attendButton.classList.remove("bookButton");
        attendButton.classList.add("unbookButton");
        attendButtonSpan.textContent = "Attending";
    } else {
        attendButton.classList.remove("unbookButton");
        attendButton.classList.add("bookButton");
        attendButtonSpan.textContent = "Attend";
    }

    // Agregar el data-event-identifier al botón
    attendButton.dataset.eventId = eventData.id;

    // Actualiza el contenido con los datos del evento
    profileName.textContent = eventData.name;
    profileSubtypeExchange.classList.add("hidden", !eventData.subtype === 'Language Exchange');
    if (eventData.subtype === 'Language Exchange') {
        profileExchangeLangs.textContent = `${eventData.exchange_lang_1} - ${eventData.exchange_lang_2}`;
        profileSubtypeExchange.classList.remove("hidden");
        console.log("Current event subtype: " + eventData.subtype);
        profileSubtypeExchange.style.display = 'flex';
    } else {
        console.log("Current event subtype in else: " + eventData.subtype);
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
}

document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(event) {
        const bookButton = event.target.closest('.bookButton');
        const unbookButton = event.target.closest('.unbookButton');
        const buttonSpan = event.target.querySelector('span') || (event.target.tagName === 'SPAN' ? event.target : null);

        if (bookButton) {
            const userId = bookButton.dataset.userId;
            const eventId = bookButton.dataset.eventId;

            if (userId === 'null') {
                alert('You must be logged in to book events.');
                return;
            }

            if (eventId) {
                fetch('book_event', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `user_id=${userId}&event_id=${eventId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (buttonSpan) {
                            buttonSpan.textContent = 'Attending';
                        }
                        bookButton.classList.remove('bookButton');
                        bookButton.classList.add('unbookButton');

                    } else {
                        alert(data.message || 'Error booking event.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Network error occurred.');
                });
            } else {
                console.error('Event ID to book not found.');
                alert('Could not book event.');
            }
        } else if (unbookButton) {
            const userId = unbookButton.dataset.userId;
            const eventId = unbookButton.dataset.eventId;

            if (userId === 'null') {
                alert('You must be logged in to unbook events.');
                return;
            }

            if (eventId) {
                fetch('unbook_event', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `user_id=${userId}&event_id=${eventId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (buttonSpan) {
                            buttonSpan.textContent = 'Attend';
                        }
                        unbookButton.classList.remove('unbookButton');
                        unbookButton.classList.add('bookButton');

                    } else {
                        alert(data.message || 'Error unbooking event.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Network error occurred.');
                });
            } else {
                console.error('Event ID to unbook not found.');
                alert('Could not unbook event.');
            }
        }
    });
});

function search(texto) {
    // Si no se ha introducido un valor en el input
    if (texto.length === 0) {
        // Vacíamos los resultados que hubieran
        document.getElementById("resultados").innerHTML = "";
        return;
    } else {
        // Creamos un nuevo objeto XMLHttpRequest para realizar peticiones
        var request = new XMLHttpRequest();

        // Creamos un evento que se disparará cuando cambie el estado de la petición
        request.onreadystatechange = estadoCambiado;

        // Abrimos la comunicación asíncrona y hacemos una petición GET
        request.open("GET", "explore?texto="+texto, true);
        // Enviamos la petición
        request.send(null);

        // Función que se ejecuta cuando se recibe la respuesta del servidor
        function estadoCambiado() {
            // Comprobamos si se han recibido los datos y si la comunicación es correcta
            if (request.readyState === 4 && request.status === 200) {
                try {
                    // Intentamos parsear la respuesta como JSON
                    var resultados = JSON.parse(request.responseText);
                    var lista = "";
        
                    if (resultados.length === 0) {
                        lista = "<p class='error'>There are no results for: " + texto + "</p>";
                    } else if (resultados[0] && resultados[0].password) {
                        // Si el primer resultado tiene la propiedad 'username', asumimos que son usuarios
                        lista = "<div class='user-container'>";
                        resultados.forEach(function(usuario) {
                            lista += `
                                <div class="user-result efecto">
                                    <a href="profile?id=${usuario.id}">
                                        <div class="user-info">
                                        <img src="${usuario.profile_pic ? usuario.profile_pic : './public/img/pic_placeholder.png'}" alt="profile picture">
                                            <div class="user-details">
                                                <span class="nickname">${usuario.nickname}</span>
                                                <span class="username">@${usuario.username}</span>
                                            </div>
                                        </div>
                                        <div class="user-row">
                                            <div class="bio">
                                                <span>${usuario.bio ? usuario.bio : 'No bio available'}</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            `;
                        });
                        lista += "</div>";
                    } else if (resultados[0] && resultados[0].description) {
                        // Si el primer resultado tiene la propiedad 'description', asumimos que son logs
                        lista = "<div class='log-container'>";
                        resultados.forEach(function(log) {
                            lista += `
                                <div class="log efecto">
                                    <div class="usuario">
                                        <a href="profile?id=${log.user_id}">
                                            <div class="log-user">
                                                <img src="${log.profile_pic}" alt="profile picture">
                                                <div class="info-usuario">
                                                    <div class="nick-user">
                                                        <span class="nickname">${log.nickname}</span>
                                                        <span class="username">@${log.username}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="log-column">
                                            <div class="log-date"><span>${log.log_date}</span></div>
                                            <div class="duration">
                                                <span>${log.duration}</span>
                                                <span>minutes</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="log-data">
                                        <div class="log-row">
                                            <div class="description">
                                                <span>${log.description}</span>
                                            </div>
                                            <div class="log-column">
                                                <div class="language">
                                                    <span>${log.language}</span>
                                                </div>
                                                <div class="type">
                                                    <span>${log.type}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="post-date">
                                            <span><strong>Post Date:</strong> ${log.post_date}</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        lista += "</div>";
                    }
        
                    // Introducimos los resultados en el div de id "resultados"
                    document.getElementById("resultados").innerHTML = lista;
        
                    // Animación de aparición secuencial
                    var elementos = document.querySelectorAll('.efecto');
                    elementos.forEach(function(elemento, index) {
                        setTimeout(function() {
                            elemento.classList.add('aparecer');
                        }, index * 200);
                    });
        
                } catch (error) {
                    console.error("Error al parsear la respuesta JSON:", error);
                    document.getElementById("resultados").innerHTML = "<p class='error'>Error al procesar la respuesta del servidor.</p>";
                }
            }
        }
    }
}
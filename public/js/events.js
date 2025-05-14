const profile = document.querySelector(".dashboard .profile");
const feed = document.querySelector(".dashboard .feed");
let isProfileVisible = false; // Inicialmente el perfil está oculto
let currentActiveEventId = null; // Variable para almacenar el ID del evento actualmente mostrado

document.getElementById("resultados").addEventListener("click", (event) => {
    const clickedEvent = event.target.closest('.event');
    if (clickedEvent) {
        const eventId = clickedEvent.dataset.eventIdentifier;
        console.log("Clicked on event ID:", eventId);

        if (eventId === currentActiveEventId && isProfileVisible) {
            profile.classList.add("hidden");
            feed.classList.remove("profile-hidden");
            isProfileVisible = false;
            currentActiveEventId = null;
        } else {
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
                    currentActiveEventId = eventId;
                })
                .catch(error => {
                    console.error("Error fetching event details:", error);
                });
        }
    }
});

profile.classList.add("hidden");
feed.classList.remove("profile-hidden");
function updateEvent(eventData) {
    // Seleccionamos los elementos dentro de .profile que tenemos que actualizar
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

    // Agregamos el data-event-identifier al botón
    attendButton.dataset.eventId = eventData.id;

    // Actualizamos el contenido con los datos del evento
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
        const datePart = fullText.substring(fullText.indexOf(':') + 2);
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
    // Obtenemos el elemento donde se muestran los resultados
    var resultadosDiv = document.getElementById("resultados");
    // Guardamos el HTML inicial en una variable global o en un atributo del elemento
    if (!resultadosDiv.dataset.initialHtml) {
        resultadosDiv.dataset.initialHtml = resultadosDiv.innerHTML;
    }
    // Si no se ha introducido un valor en el input
    if (texto.length === 0) {
        // Restauramos los resultados iniciales
        resultadosDiv.innerHTML = resultadosDiv.dataset.initialHtml;
        return;
    } else {
        // Creamos un nuevo objeto XMLHttpRequest para realizar peticiones
        var request = new XMLHttpRequest();

        // Creamos un evento que se disparará cuando cambie el estado de la petición
        request.onreadystatechange = estadoCambiado;

        // Abrimos la comunicación asíncrona y hacemos una petición GET
        request.open("GET", "events?texto=" + texto, true);
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
                        lista = "<p class='error'>No hay resultados para: " + texto + "</p>";
                    } else if (resultados) {
                        lista = "<div class='user-container'>";
                        resultados.forEach(function (event) {
                            lista += `
                                <div class="event efecto" data-event-identifier="${event.id}">
                                    <div class="event-header usuario">
                                        <div class="event-column">
                                            <h3 class="event-name">${event.name}</h3>
                                            
                                            <div class="event-subtype">
                                                <div class="${event.subtype == 'Language Exchange' ? 'exchange-type' : 'hidden' }">
                                                    <span class="exchange-langs">${event.exchange_lang_1} - ${event.exchange_lang_2}</span>
                                                </div>
                                                <div class="main-lang ${event.main_lang ? '' : 'hidden' }">Event language: ${event.main_lang}</div>
                                                <div class="learning-lang ${event.learning_lang ? '' : 'hidden' }">Target language: ${event.learning_lang}</div>
                                            </div>
                                        </div>
                                        <div class="event-right">
                                            <div class="event-date"><span>${event.event_date}</span></div>
                                            <span class="event-type">${event.type}</span>
                                        </div>
                                    </div>
                                    <div class="event-info">
                                        <div class="log-row">
                                            <div class="description">
                                                <!-- Poner un límite de mostrar la descripción: poner botón de show more y ahí se muestra el evento a la derecha -->
                                                <span>${event.description}</span>
                                            </div>
                                        </div>
                                        <div class="post-date">
                                            <span><strong>Creation Date:</strong> ${event.creation_date}</span>
                                            <!-- Location if it's in person -->
                                            <div class="location ${event.city ? '' : 'hidden' }">
                                                <span>
                                                    <span>${event.city}</span>, <span>${event.country}</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        lista += "</div>";
                    }

                    // Introducimos los resultados en el div de id "resultados"
                    resultadosDiv.innerHTML = lista;

                    // Animación de aparición secuencial (si la necesitas)
                    var elementos = document.querySelectorAll('.efecto');
                    elementos.forEach(function (elemento, index) {
                        setTimeout(function () {
                            elemento.classList.add('aparecer');
                        }, index * 200);
                    });

                } catch (error) {
                    console.error("Error al parsear la respuesta JSON:", error);
                    resultadosDiv.innerHTML = "<p class='error'>Error al procesar la respuesta del servidor.</p>";
                }
            }
        }
    }
}
/**
 * Busca logs en la base de datos según el texto introducido por el usuario.
 *
 * Esta función realiza una petición AJAX al servidor para buscar logs cuya descripción
 * contenga el texto introducido. Una vez recibida la respuesta, muestra los
 * resultados en un elemento HTML con el id "resultados".
 *
 * @param {string} texto - El texto introducido por el usuario para realizar la búsqueda.
 * @returns {void}
 */
function explore(texto) {
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
                console.log("saludos desde estadoCambiado " + texto);
                // Convertimos la respuesta JSON en un objeto Javascript
                var resultados = JSON.parse(request.responseText);
                // Creamos variable vacía que contendrá la lista de logs encontrados
                var lista = "";

                

                // Comprobamos que se han encontrado logs
                if (resultados.length === 0) {
                    // Si no hay, mostrar mensaje
                    lista = "<p class='error'>No se encontraron logs con ese texto.</p>";
                } else {
                    // Creamos la lista de los logs encontrados
                    lista = "<div class='log-container'>";
                    // Recorremos los resultados para mostrar los datos de cada log
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

                // Introducimos los logs encontrados en el div de id "resultados"
                document.getElementById("resultados").innerHTML = lista;

                // Animación de aparición secuencial
                var logs = document.querySelectorAll('.efecto');
                logs.forEach(function(log, index) {
                    setTimeout(function() {
                        log.classList.add('aparecer');
                    }, index * 200); // Retardo de 200ms entre cada elemento
                });
            }
        }
    }
}
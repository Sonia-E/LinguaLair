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
                try {
                    // Intentamos parsear la respuesta como JSON
                    var resultados = JSON.parse(request.responseText);
                    var lista = "";
        
                    if (resultados.length === 0) {
                        lista = "<p class='error'>No se encontraron resultados para: " + texto + "</p>";
                    } else if (resultados[0] && resultados[0].password) {
                        // Si el primer resultado tiene la propiedad 'username', asumimos que son usuarios
                        console.log("saludos desde usuario");
                        lista = "<div class='user-container'>";
                        resultados.forEach(function(usuario) {
                            lista += `
                                <div class="user-result efecto">
                                    <a href="profile?id=${usuario.id}">
                                        <div class="user-info">
                                        <img src="${usuario.profile_pic ? usuario.profile_pic : 'img/pic_placeholder.png'}" alt="profile picture">
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
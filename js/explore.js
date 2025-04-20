/**
 * Busca libros en la base de datos según el texto introducido por el usuario.
 *
 * Esta función realiza una petición AJAX al servidor para buscar libros que coincidan con el texto introducido.
 * Una vez recibida la respuesta, muestra los resultados en un elemento HTML con el id "resultados".
 *
 * @param {string} texto - El texto introducido por el usuario para realizar la búsqueda.
 * @returns {void}
 */
function explore(texto) {
    // Si no se ha introducido un valor en el input
    if (texto.length == 0) {
        // Vacíamos los resultados que hubieran
        document.getElementById("resultados").innerHTML = "";
        return;
    } else {
        // // Nos aseguramos que solo se introduzcan caracteres
        // if (!/^[a-zA-ZñÑ\s]+$/.test(texto)) {
        //     // Salta mensaje de error si no es así
        //     alert('Por favor, introduce solo caracteres alfabéticos');
        //     return;
        // }
        // Creamos un nuevo objeto XMLHttpRequest para realizar peticiones
        var request = new XMLHttpRequest();

        // Creamos un evento que se disparará cuando cambie el estado de la petición
        request.onreadystatechange = estadoCambiado;
        
        // Abrimos la comunicación asíncrona y hacemos una petición GET
        request.open("GET", "controllers/busquedaLibros.php?texto="+texto, true);
        // Enviamos la petición
        request.send(null);

        // Función que se ejecuta cuando se recibe la respuesta del servidor
        function estadoCambiado() {
            // Comprobamos si se han recibido los datos y si la comunicaicón es correcta
            if (request.readyState == 4 && request.status == 200) {
                // Convertimos la respuesta JSON en un objeto Javascript
                var resultados = JSON.parse(request.responseText);
                // Creamos variable vacía que contendrá la lista de libros encontrados
                var lista = "";

                // Comprobamos que se han encontrado libros
                if (resultados.length === 0) {
                    // Si no hay, mostrar mensaje de error
                    lista = "<p class='error'>No se encontraron libros</p>";
                } else {
                    // Creamos la lista de los libros encontrados
                    lista = "<ul>";
                    // Recorremos los resultados para sacar los datos de cada libro
                    resultados.forEach(function(libro) {
                        // Mostramos su título y nombre y apellidos de su autor
                        lista += "<div class='log efecto'>" + libro.titulo + " - " + libro.nombre + " " + libro.apellidos + "</div>";
                    });
                    lista += "</ul>";
                }

                // Introducimos los libros encontrados en el div de id "resultados"
                document.getElementById("resultados").innerHTML = lista;

                // Animación de aparición secuencial
                var logs = document.querySelectorAll('.efecto');
                logs.forEach(function(log, index) {
                setTimeout(function() {
                    log.classList.add('aparecer');
                }, index * 200); // Retardo de 500ms entre cada elemento
                });
            }
        }
    }
}
<?php
/**
 * Establece una conexión con una base de datos MySQL utilizando los parámetros proporcionados.
 *
 * @param string $servidor Nombre del servidor de la base de datos.
 * @param string $usuario Nombre de usuario para la conexión.
 * @param string $contrasenia Contraseña del usuario.
 * @param string $base_datos Nombre de la base de datos.
 * @return mysqli|null Devuelve un objeto mysqli $conexion si la conexión es exitosa, null en caso de error.
 */
function conexion($servidor, $usuario, $contrasenia, $base_datos) {
  // Crear una conexión a la base de datos
  $conexion = new mysqli($servidor, $usuario, $contrasenia, $base_datos);

  // Verificar si hay algún error en la conexión
  if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
    return null;
  } else {
    return $conexion;
  }
}

/**
 * Consulta la base de datos para obtener los libros según el texto introducido por el usuario.
 *
 * La consulta se realiza mediante una unión interna entre las tablas `Libro` y `Autor` para 
 * obtener información de ambas.
 *
 * @param mysqli $conexion Objeto de conexión a la base de datos.
 * @param string $texto Texto introducido para filtrar los libros.
 * @return array|null Devuelve un array de objetos $lista_libros, donde cada objeto representa libro y contiene 
 *          los campos de ambas tablas. Si ocurre un error en la consulta, devuelve null y muestra 
 *          un mensaje de error.
 */
function get_listado_libros($conexion, $texto) {
  // Preparar la consulta
  $consulta = "SELECT l.id, l.titulo, l.f_publicacion,
        a.id as id_autor, a.nombre, a.apellidos
        FROM Libro l
        LEFT JOIN Autor a ON l.id_autor = a.id
        WHERE l.titulo LIKE '%$texto%'";

  $resultado = $conexion->query($consulta);

  if ($resultado) {
    $lista_libros = [];
    while ($row = $resultado->fetch_object()) {
      $lista_libros[] = $row; // Cada fila contiene datos tanto de Libro como de Autor
      }
      $conexion->close();
      return $lista_libros;
  } else {
    echo "Error al consultar BD: " . $conexion->error;
    return null;
  }
}

// Recogemos el valor introducido en el input
$texto = $_REQUEST["texto"];
$libros = "";

if ($texto !== "") {
  // Pasamos el valor del input a minúsculas
  $texto = strtolower($texto);

  // Establecemos la conexión con la base de datos Libros
  $conexion = conexion("localhost", "foc", "foc", 'Libros');

  // Buscamos a un usuario con @ o no
  if (preg_match('/^@/', $texto)) {
      // Meter método que busque en la tabla usuarios
  } else {
    // Llamamos a la función para buscar los libros con el valor $texto
    $libros = get_listado_libros($conexion, $texto);

    // Devolvemos el resultado en formato JSON
    exit(json_encode($libros));
  }
}
?>
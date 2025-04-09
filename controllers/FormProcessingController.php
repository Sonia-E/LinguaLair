<!-- <?php

// Asegúrate de que CON_CONTROLADOR esté definido si lo usas en otros archivos
// if (!defined('CON_CONTROLADOR')) die('Acceso no permitido.');

////// COMO CLASE NO FUNCIONAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA

// class FormProcessingController {
//     private $modelo;

//     public function __construct($modelo) { // Accept the $modelo instance
//         $this->modelo = $modelo; // Assign the passed $modelo to the class property
//     }

//     public function procesarFormulario() {
//         if ($_SERVER["REQUEST_METHOD"] == "POST") {
//             $userId = 1; // Obtener el ID del usuario de la sesión o de alguna otra manera segura
//             $description = $_POST["description"] ?? '';
//             $language = $_POST["language"] ?? '';
//             $type = $_POST["type"] ?? '';
//             $duration = $_POST["duration"] ?? '';
//             $logDate = $_POST["date"] ?? '';

//             // Validar los datos del formulario (¡importante!)

//             // Llamar al modelo para guardar el log
//             if ($this->modelo->addLog($userId, $description, $language, $type, $duration, $logDate)) {
//                 // Redirigir con éxito
//                 header("Location: /LinguaLair/");
//                 exit();
//             } else {
//                 // Redirigir con error
//                 header("Location: /LinguaLair/");
//                 exit();
//             }
//         } else {
//             // Si se intenta acceder directamente por GET
//             header("HTTP/1.0 403 Forbidden");
//             echo 'Acceso no permitido.';
//         }
//     }
// }

?> -->

<?php
// Importamos el modelo
// require_once '../modelo.php';
// $conexion = conexion("localhost", "foc", "foc", 'LinguaLair');

// $uri = $_SERVER['REQUEST_URI'];

// if ($_SERVER["REQUEST_METHOD"] == "POST") {

//     if ($uri === '/login_processing.php') {
//         // Lógica para procesar el formulario de guardar log
//         // ...
//         echo "Procesando guardar log.<br>";
//     } elseif ($uri === '/signup_processing.php') {
//         // Lógica para procesar el otro formulario
//         // ...
//         echo "Procesando otra cosa.<br>";
//     } elseif ($uri === '/log_processing.php') {
//         // Lógica para procesar el otro formulario
//         // ...
//         echo "Procesando otra cosa.<br>";
//     } else {
//         echo "Endpoint no encontrado.";
//     }
// } else {
//     echo "Petición no válida.";
// }

// -----------LOG FORM----------

// Importamos el modelo
require_once '../modelo.php';
$modelo = new Modelo("localhost", "foc", "foc", 'LinguaLair');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $description = $_POST["description"];
    $language = $_POST["language"];
    $type = $_POST["type"];
    $duration = $_POST["duration"];
    $log_date = $_POST["date"];
    $user_id = 1;

    // Aquí puedes realizar las operaciones que necesites con los datos recogidos:
    // - Guardarlos en una base de datos
    // - Validarlos
    // - Realizar alguna otra lógica

    $modelo->addLog($user_id, $description, $language, $type, $duration, $log_date);

    // Ejemplo de cómo mostrar los datos recibidos:
    echo "Descripción: " . htmlspecialchars($description) . "<br>";
    echo "Idioma: " . htmlspecialchars($language) . "<br>";
    echo "Tipo de Actividad: " . htmlspecialchars($type) . "<br>";
    echo "Duración (minutos): " . htmlspecialchars($duration) . "<br>";
    echo "Fecha de la Actividad: " . htmlspecialchars($date) . "<br>";

    // Después de procesar los datos, puedes redirigir al usuario a otra página:
    header("Location: /LinguaLair/");
} else {
    // Si se intenta acceder a este script por GET o cualquier otro método
    echo "No se recibieron datos del formulario.";
}

?>
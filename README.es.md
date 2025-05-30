[![en](https://img.shields.io/badge/lang-en-red.svg)](https://github.com/Sonia-E/LinguaLair/blob/main/README.md)

# <p style='text-align: center;'>¿Qué es LinguaLair?</p>

<p style='text-align: center;'>¿No te harta no saber dónde y cómo apuntar lo que haces para aprender un idioma?</p>
<p style='text-align: center;'>¿Encima estudias más de un idioma?</p>

<p style='text-align: center;'>¿Las aplicaciones generales de productividad se te quedan cortas?</p>

<p style='text-align: center;'>¡Vente a LinguaLair! Una aplicación de productividad orientada al aprendizaje de idiomas:</p>

<p style='text-align: justify;'>En LinguaLair podrás registrar tu actividad en el proceso de aprendizaje de tus idiomas. La plataforma guardará tus registros, los analizará y te mostrará las estadísticas de tus estudios para que puedas optimizar y planificar tu estudio y progreso en cada idioma acorde a los gráficos que te mostremos. Así no perderás tiempo valioso intentando recordar cuánto tiempo le has dedicado a qué; ¡nosotros lo hacemos por ti! Así podrás dedicar ese tiempo a estudiar tus queridos idiomas.

Además, ¡puedes conectar con otros amantes de otros idiomas para crear comunidad y motivaros los unos a los otros! Puedes conectar con ellos tanto siguiendo sus perfiles o asistiendo a eventos online o en persona. Estos eventos los organizamos nosotros. ¡No te cortes y practica tus idiomas con otros apasionados de los idiomas!</p>

## Qué necesitas para empezar

- XAMPP con PHP y MySQL: https://www.apachefriends.org/download.html
- Crear la la base de datos con el script <code>bd.php</code>: Explicado en el apartado "[Base de datos LinguaLair](#base-de-datos)"
- Composer: manejador de paquetes para PHP: https://getcomposer.org/download/
- Framework PHPUnit: para la realización de las pruebas unitarias:

    ```bash
    composer require --dev phpunit/phpunit
    ```

- Framework PHPMailer: para mandar correos electrónicos desde la aplicación -> Utilizado para el envío de las incidencias a través del formulario en <code>Contact</code>:

    ```bash
    composer require phpmailer/phpmailer
    ```
- Poder recibir correos con PHPMailer: en <code>IncidentsModel.php</code> tenemos la siguiente configuración del servidor:

    ```php
    // Configuración del servidor
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->Port       = 587;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->SMTPAuth   = true;
    $mail->Username   = 'soniaenjutom94@gmail.com';
    $mail->Password   =  $smtp_password; // Contraseña de aplicación
    ```
    Por seguridad la contraseña de aplicación no se ha incluido directamente sino que se importa de un archivo PHP externo que deberás crear. La importación se realiza también en <code>IncidentsModel.php</code>:

    ```php
    require_once 'SMTP_password.php';
    ```
    Crea un archivo con este nombre <code>SMTP_password.php</code> en la raíz del proyecto. En su interior añade lo siguiente:

    ```php
    <?php
    global $smtp_password;
    $smtp_password = "[tu contraseña de aplicación]";
    ?>
    ```
    La contraseña de aplicación deberás generarla dependiendo de que sistema de correo uses (Gmail, Outlook, etc.); aquí se ha utilizado Gmail, por eso el host es <code>smtp.gmail.com</code>.

    Para crear dicha contraseña en Gmail, accede a <code>https://support.google.com/mail/answer/185833?hl=es</code> y pulsa sobre [Crea y gestiona contraseñas de aplicación](https://myaccount.google.com/apppasswords).

    Por último, no olvides cambiar el siguiente correo para poder recibir los correos de las incidencias a las direcciones que desees:

    ```php
    $mail->Username   = 'soniaenjutom94@gmail.com'; // Esto cambia dependiendo de si usas Gmail, Outlook, etc.
    $mail->Password   =  $smtp_password; // Contraseña de aplicación

    // Configuración del correo
    $mail->setFrom('soniaenjutom94@gmail.com', 'Incidents Management System'); // Remitente
    $mail->addAddress('soniaenjutom94@gmail.com'); // Destinatario
    $mail->addReplyTo('soniaenjutom94@gmail.com', 'Incidents Management System');
    ```

## Base de datos LinguaLair

### Usuario

El usuario utilizado en phpMyAdmin es el usuario root:

```bash
Usuario: root, Servidor: localhost, Contraseña: [no tiene]
```
Puedes usar el usuario que desees, pero tendrás que modificar dichos datos de conexión en 3 archivos diferentes del proyecto:

1. <code>bd.php</code>: Debes modificar lo siguiente al comienzo del código:
```php
<?php
// Creamos la conexión
if ($mysqli = new mysqli("localhost", "root", "")) {
```
2. <code>index.php</code>
3. <code>feed.php</code>

Para estos 2 archivos, deberás buscar para ambos en el principio del código la siguiente declaración de variables y modificarlas según las credenciales que escojas; no cambies el nombre de la base de datos ("LinguaLair"):

```php
// Conection variables
$server = 'localhost';
$user = 'root';
$password = '';
$database = 'LinguaLair';
```

### Dos formas de crear la base de datos

1. <strong>Creación automática:</strong> En <code>bd.php</code> al principio hay una línea de código que comprueba si ya existe la base de datos <code>LinguaLair</code>: si ya existe, no volverá a crearla y hacer las inserciones de datos; si no existe, sí lo hará. Esto es posible porque en <code>index.php</code>, el controlador frontal, importa <code>bd.php</code> antes del enrutamiento. Por ello, basta con ir a la siguiente dirección de la aplicación web y así se creará automáticamente la base de datos: <code>http://localhost/LinguaLair/</code>

2. <strong>Creación manual:</strong> Si por el contrario, se desea crear manualmente, escribe <code>http://localhost/LinguaLair/bd/bd.php</code> en tu navegador y verás los mensajes de creación de la base de datos.

### Usuarios de la base de datos para pruebas

Puedes elegir registrarte con el usuario que desees, pero también se crean tres usuarios de ejemplo cada uno con un rol diferente:

1.	<strong><code>chieloveslangs</code>:</strong> usuario principal con el rol <code>ADMIN</code> y perfil público
2.	<strong><code>Sauron</code>:</strong> usuario con el perfil privado y el rol <code>STANDARD</code>: rol para usuarios normales y recién registrados
3.	<strong><code>Kakashi</code>:</strong> usuario con el rol <code>PREMIUM</code> y perfil público

De esta forma puedes probar las funcionalidades para cada tipo de usuario. Para usar cualquiera de ellos, basta con poner estos nombres de usuario y la contraseña de acceso <code>contrasenia</code> en el inicio de sesión.


## Cómo usar

- Aplicación web LinguaLair: Una vez instalado todo e iniciar los módulos de Apache y MySQL en XAMPP, solo tienes que introducir la siguiente dirección en tu navegador: <code>http://localhost/LinguaLair/</code>. Puedes usar cualquier navegador, aunque el proyecto se realizó usando Google Chrome y Microsoft Edge como navegadores principales para su visualización.

- Realizar pruebas unitarias:
    - Sin detalles:

        ```bash
        .\vendor\bin\phpunit tests\unit\LoginFormControllerTest.php  
        ```
    - Con detalles: añade <code>--debug</code>

        ```bash
        .\vendor\bin\phpunit --debug tests\unit\SignupFormControllerTest.php  
        ```
    - Probar solo un método: añade <code>--filter</code> + nombre del método

        ```bash
        .\vendor\bin\phpunit --filter testProcesarFormularioSuccess  tests\unit\SignupFormControllerTest.php 
        ```

Si tienes más dudas, aquí puedes consultar los siguientes manuales:

- [Guía Rápida](https://github.com/Sonia-E/LinguaLair/blob/main/manuales/Gu%C3%ADa%20R%C3%A1pida.pdf)
- [Manual de usuario](https://github.com/Sonia-E/LinguaLair/blob/main/manuales/Manual%20de%20usuario.pdf)
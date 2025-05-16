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
- Creación de la base de datos con el script <code>bd.php</code>: <span style='color: red;'>FALTA ESTOOOOOOOOOOO</span>
- Composer: manejador de paquetes para PHP: https://getcomposer.org/download/
- Framework PHPUnit: para la realización de las pruebas unitarias:

    ```bash
    composer require --dev phpunit/phpunit
    ```

- Framework PHPMailer: para mandar correos electrónicos desde la aplicación -> Utilizado para el envío de las incidencias a través del formulario en <code>Contact</code>:

    ```bash
    composer require phpmailer/phpmailer
    ```

Una vez instalado todo, solo tienes que introducir la siguiente dirección en tu navegador: <code>http://localhost/LinguaLair/</code>. Puedes usar cualquier navegador, aunque el proyecto se realizó usando Google Chrome y Microsoft Edge como navegadores principales para su visualización.

## Cómo usar

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
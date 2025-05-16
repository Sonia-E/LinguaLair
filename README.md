[![es](https://img.shields.io/badge/lang-es-yellow.svg)](https://github.com/Sonia-E/LinguaLair/blob/main/README.es.md)

# <p style='text-align: center;'>What is LinguaLair?</p>

<p style='text-align: center;'>Aren't you tired of not knowing where and how to track your language learning activities?</p>
<p style='text-align: center;'>Do you study more than one language?</p>

<p style='text-align: center;'>Do general productivity applications fall short for you?</p>

<p style='text-align: center;'>Come to LinguaLair! A productivity application geared towards language learning:</p>

<p style='text-align: justify;'>In LinguaLair, you can log your activity in all the languages you're learning. The platform will save your logs, analyze them, and show you the statistics of your studies in the form of graphs so that you can optimize and plan your study and progress in each language accordingly. Thanks to this, you won't have to waste valuable time trying to remember how much time you've dedicated to what; we do it for you! This will allow you to dedicate that saved time fully to studying your beloved languages.

And what's more, you can connect with other language lovers to create a thriving community and help motivate each other! You can connect with them by following their profiles or attending online or in-person events. We are in charge of organizing them to cater to your tastes and needs, so don't be shy and practice your languages with other language enthusiasts!</p>

## Getting started

- XAMPP with PHP and MySQL: https://www.apachefriends.org/download.html
- Database creation with the <code>bd.php</code> script: <span style='color: red;'>FALTA ESTOOOOOOOOOOO</span>
- Composer: Dependency manager for PHP: https://getcomposer.org/download/
- PHPUnit Framework: Used to perform unit tests:

    ```bash
    composer require --dev phpunit/phpunit
    ```

- PHPMailer Framework: Used to send emails from the application -> Used to sned incidents via the form in <code>Contact</code>:

    ```bash
    composer require phpmailer/phpmailer
    ```

Once everything is installed and the Apache and MySQL modules in XAMPP are running, you just need to go to the following address in your browser: <code>http://localhost/LinguaLair/</code>. You can use any preferred browser, but the application was developed using Google Chrome and Microsoft Edge as the main browsers for visualization.

## Usage

- How to perform unit tests:
    - Not in detail:

        ```bash
        .\vendor\bin\phpunit tests\unit\LoginFormControllerTest.php
        ```
    - In detail: Add <code>--debug</code>

        ```bash
        .\vendor\bin\phpunit --debug tests\unit\SignupFormControllerTest.php
        ```
    - Only testing one method: Add <code>--filter</code> + method name

        ```bash
        .\vendor\bin\phpunit --filter testProcesarFormularioSuccess tests\unit\SignupFormControllerTest.php
        ```
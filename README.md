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
- Database creation with the <code>bd.php</code> script: Explanation at "[LinguaLair Database](#base-de-datos)"
- Composer: Dependency manager for PHP: https://getcomposer.org/download/
- PHPUnit Framework: Used to perform unit tests:

    ```bash
    composer require --dev phpunit/phpunit
    ```

- PHPMailer Framework: Used to send emails from the application -> Used to sned incidents via the form in <code>Contact</code>:

    ```bash
    composer require phpmailer/phpmailer
    ```
- Receive emails with PHPMailer: In <code>IncidentsModel.php</code> the following server configuration is included:

    ```php
    // Server Configuration
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->Port       = 587;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->SMTPAuth   = true;
    $mail->Username   = 'soniaenjutom94@gmail.com';
    $mail->Password   =  $smtp_password; // App password
    ```
   For security reasons the app password hasn't been included directly; it is instead imported from an externar PHP file that you must create. The import is done within <code>IncidentsModel.php</code> as well:

    ```php
    require_once 'SMTP_password.php';
    ```
    Create a file with the name <code>SMTP_password.php</code> in the project root. Inside add the following:

    ```php
    <?php
    global $smtp_password;
    $smtp_password = "[your app password]";
    ?>
    ```
    Your app password must be generated depending on the mail system you use (Gmail, Outlook, etc.). Since Gmail has been used here, the host is <code>smtp.gmail.com</code>.

    To generate this password in Gmail, go to <code>https://support.google.com/mail/answer/185833?hl=en</code> and click on [Create and manage your app passwords](https://myaccount.google.com/apppasswords).

## LinguaLair Database

### User

The user used for phpMyAdmin is the root user:

```bash
User: root, Server: localhost, Password: [no password]
```
You may use any user you desire, but be aware that if you do so, you will have to modify these connection credentials in 3 different files from the project:

1. <code>bd.php</code>: At the beginning of the code, modify the following:
```php
<?php
// Creamos la conexión
if ($mysqli = new mysqli("localhost", "root", "")) {
```
2. <code>index.php</code>
3. <code>feed.php</code>

As for these 2 files, you have to find at the beginning of both codes the following declaration of variables and modify them according to the credentials of your choosing. Just don't change the database name ("LinguaLair"):

```php
// Conection variables
$server = 'localhost';
$user = 'root';
$password = '';
$database = 'LinguaLair';
```

### Database creation: two methods

1. <strong>Automatic Creation:</strong> At the  beginning of <code>bd.php</code> there's a line of code that checks if the <code>LinguaLair</code> database already exists or not: if it already does, it won't be created again and no data insertions will be done to it; if it doesn't exist, it will be created as well as the data insertions will be done. This is possible because in <code>index.php</code>, the front controller, <code>bd.php</code> is imported before the path routing. This way, simply go to the following application address and the databse will automatically be created: <code>http://localhost/LinguaLair/</code>

2. <strong>Manual Creation:</strong> If instead you want to do it yourself, just go to <code>http://localhost/LinguaLair/bd/bd.php</code> in your browser and you will see the database creation messages.

### Users created by the database for testing

You can signup as a new user of your choosing, but you can also use one of our three sample users with a different assigned role each:

1.	<strong><code>chieloveslangs</code>:</strong> Main user with the <code>ADMIN</code> role and public profile
2.	<strong><code>Sauron</code>:</strong> user with private profile and the <code>STANDARD</code> role: role for common users and newcomers
3.	<strong><code>Kakashi</code>:</strong> user with the <code>PREMIUM</code> role and public role

Thanks to these sample users, you can test each role's unique functionalities. If you want to use any of them, simply add these usernames and the access password <code>contrasenia</code> in the login process.

## Usage

- Once everything is installed and the Apache and MySQL modules in XAMPP are running, you just need to go to the following address in your browser: <code>http://localhost/LinguaLair/</code>. You can use any preferred browser, but the application was developed using Google Chrome and Microsoft Edge as the main browsers for visualization.
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
If you have any more doubts, you can check here some user manuals:

- [Quick Guide](https://github.com/Sonia-E/LinguaLair/blob/main/manuales/Gu%C3%ADa%20R%C3%A1pida.pdf)
- [User Manual](https://github.com/Sonia-E/LinguaLair/blob/main/manuales/Manual%20de%20usuario.pdf)
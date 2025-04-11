<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LinguaLair</title>
        <link rel="stylesheet" href="/lingualair/css/login.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    </head>
    <body>
        <div class="container">
            <div class="left-side">
                <h1>Master your target languages!</h1>
                <p>Keep track of your activity and progress in each of the languages 
                    you're learning and connect with other language lovers!</p>
                <p></p>
            </div>
            <div class="right-side">
                <div class="logo">
                    <ion-icon name="earth-outline"></ion-icon>
                    <h2>LinguaLair</h2>
                </div>
                <form action="login" method="post" class="login-form">
                    <div class="form-group">
                        <label for="username">Username or email</label>
                        <input type="text" id="username" name="username" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                        <a href="#" class="forgot-password">Forgot password?</a>
                    </div>
                    <button type="submit" class="sign-in-button">Sign in</button>
                </form>
                <div class="separator">
                    <span>or</span>
                </div>
                <button class="google-sign-in">
                    <i class="fab fa-google"></i> Sign in with Google
                </button>
                <p class="create-account">Are you new? <a href="#">Create an Account</a></p>
            </div>
        </div>
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
            <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
            <script type="text/javascript" src="./js/barra.js"></script>
    </body>
</html>
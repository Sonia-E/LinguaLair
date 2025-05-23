<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LinguaLair</title>
        <link rel="stylesheet" href="public/css/signup.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <!-- Country select -->
        <link rel="stylesheet" href="libreria/countrySelect/countrySelect.css">
    </head>
    <body>
        <div class="container">
            <div class="right-side">
                <div class="logo">
                    <ion-icon name="earth-outline"></ion-icon>
                    <h2>LinguaLair</h2>
                </div>
                <form action="signup" method="post" class="login-form">
                    <div class="form-group">
                        <label for="username">Pick a username!</label>
                        <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" autofocus>
                        <?php if (isset($errores['username'])): ?>
                            <div class="error-message"><?php echo htmlspecialchars($errores['username']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" autofocus>
                        <?php if (isset($errores['email'])): ?>
                            <div class="error-message"><?php echo htmlspecialchars($errores['email']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="country">Country</label>
                        <div class="form-item">
                            <input id="country_selector" type="text" name="country">
                            <label for="country_selector" style="display:none;">Select a country here...</label>
                        </div>
                        <div class="form-item" style="display:none;">
                            <input type="text" id="country_selector_code" name="country_selector_code" data-countrycodeinput="1" readonly="readonly" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password">
                        <?php if (isset($errores['password'])): ?>
                            <div class="error-message"><?php echo htmlspecialchars($errores['password']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password">
                        <?php if (isset($errores['confirm_password'])): ?>
                            <div class="error-message"><?php echo htmlspecialchars($errores['confirm_password']); ?></div>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($errores['registration'])): ?>
                        <div class="error-message general-error"><?php echo htmlspecialchars($errores['registration']); ?></div>
                    <?php endif; ?>

                    <button type="submit" class="sign-in-button">Sign up</button>
                </form>
                <div class="separator">
                    <span>or</span>
                </div>
                <p class="create-account">Do you already have an account? <a href="login">Sign in to your Account</a></p>
            </div>
        </div>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="libreria/countrySelect/countrySelect.js"></script>
		<script>
			$("#country_selector").countrySelect({
				preferredCountries: ['es', 'gb', 'us']
			});
		</script>
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    </body>
</html>
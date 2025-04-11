<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LinguaLair</title>
        <link rel="stylesheet" href="/lingualair/css/signup.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <!-- Country select -->
        <link rel="stylesheet" href="/lingualair/css/countrySelect.css">
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
                        <input type="text" id="username" name="username" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" id="email" name="email" required autofocus>
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
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="sign-in-button">Sign up</button>
                </form>
                <div class="separator">
                    <span>or</span>
                </div>
                <button class="google-sign-in">
                    <i class="fab fa-google"></i> Sign up with Google
                </button>
                <p class="create-account">Do you already have an account? <a href="login">Sign in to your Account</a></p>
            </div>
        </div>
        <!-- Load jQuery from CDN so can run demo immediately -->
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="/lingualair/js/countrySelect.js"></script>
		<script>
			$("#country_selector").countrySelect({
				// defaultCountry: "jp",
				// onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
				// responsiveDropdown: true,
				preferredCountries: ['es', 'gb', 'us']
			});

            var country = $.fn.countrySelect._selectListItem();

            console.log(country)
		</script>
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    </body>
</html>
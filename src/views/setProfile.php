<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LinguaLair</title>
        <link rel="stylesheet" href="public/css/setProfile.css">
    </head>
    <body>
        <div class="container">
            <div class="left-side">
            <div class="logo">
                    <h2>Almost finished!</h2>
                    <h3>Let's set your basic profile!</h3>
                </div>
            <form action="set_profile" method="post">
                <div class="login-form">
                    <div class="form-group">
                        <label for="nickname">Pick a nickname (you can change it later)</label>
                        <input type="text" id="nickname" name="nickname" autofocus>
                    </div>
                    <div class="form-group">
                        <label class="languages" for="native_languages">Select your native language/s</label>
                        <div class="form-item">
                            <small class="label">Language 1</small>
                            <select id="language1" name="native_languages[]"></select>
                            <small class="label">Language 2</small>
                            <select id="language2" name="native_languages[]"></select> 
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="languages" for="learning_languages">What languages are you currently learning?</label>
                        <div class="form-item">
                            <small class="label">Language 1</small>
                            <select id="language3" name="learning_languages[]"></select>
                            <small class="label">Language 2</small>
                            <select id="language4" name="learning_languages[]"></select> 
                            <small class="label">Language 3</small>
                            <select id="language5" name="learning_languages[]"></select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="right-side">
                <div class="login-form">
                    <div class="form-group">
                        <label for="bio">Tell us something about yourself</label>
                        <textarea name="bio" id="bio"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="public">Do you want other users to see your profile?</label> 
                        <div class="radio-group">
                            <div class="radio-item">
                                <input type="radio" id="public_yes" name="public" value="1" checked>
                                <label for="public_yes">Yes</label>
                            </div>
                            <div class="radio-item">
                                <input type="radio" id="public_no" name="public" value="0">
                                <label for="public_no">No</label>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (isset($errores['set_profile'])): ?>
                        <div class="error-message general-error"><?php echo htmlspecialchars($errores['set_profile']); ?></div>
                    <?php endif; ?>

                    <button type="submit" class="sign-in-button">Let's begin!</button>
                </div>
                </form>
            </div>
        </div>
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
        <!-- Language JSON list -->
        <script type="text/javascript" src="public/js/profile.js"></script>
    </body>
</html>
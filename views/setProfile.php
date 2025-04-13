<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LinguaLair</title>
        <link rel="stylesheet" href="/lingualair/css/setProfile.css">
    </head>
    <body>
        <div class="container">
            <div class="right-side">
                <div class="logo">
                    <h2>Almost finished!</h2>
                    <h3>Let's set your basic profile!</h3>
                </div>
                <form action="set_profile" method="post" class="login-form">
                    <div class="form-group">
                        <label for="nickname">Pick a nickname (you can change this later)</label>
                        <input type="text" id="nickname" name="nickname" autofocus>
                    </div>
                    <div class="form-group">
                        <label for="languages">What languages are you currently learning?</label>
                        <div class="form-item">
                            
                            <small>Language 1</small>
                            <select id="language1" name="languages[]"></select>
                            <small>Language 2</small>
                            <select id="language2" name="languages[]"></select> 
                            <small>Language 3</small>
                            <select id="language3" name="languages[]"></select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="bio">Tell us something about yourself</label>
                        <textarea name="bio" id="bio"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="public">Do you want other users to see your profile?</label> 
                        <div class="radio-group">
                            <input type="radio" id="public_yes" name="public" value="1" checked>
                            <label for="public_yes">Yes</label>

                            <input type="radio" id="public_no" name="public" value="0">
                            <label for="public_no">No</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="sign-in-button">Let's begin!</button>
                </form>
            </div>
        </div>

        <!-- PROFILE  (user_id,  bio,  native_lang,  languages,  fluent,  learning,  on_hold,  dabbling,  level,  
         experience, dark_mode, num_followers, num_following, is_active, profile_pic, bg_pic, game_roles) -->
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
        <!-- Language JSON list -->
        <script>
            const languageSelect1 = document.getElementById('language1');
            const languageSelect2 = document.getElementById('language2');
            const languageSelect3 = document.getElementById('language3');
            const gistUrl = 'https://gist.githubusercontent.com/joshuabaker/d2775b5ada7d1601bcd7b31cb4081981/raw/languages.json'; // Reemplaza con la URL RAW de tu Gist

            fetch(gistUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(languageData => {
                    languageData.forEach(language => {
                        const option = document.createElement('option');
                        option.value = language.code;
                        option.textContent = `${language.name} (${language.native})`;

                        // Append the same option to each select element
                        languageSelect1.appendChild(option.cloneNode(true));
                        languageSelect2.appendChild(option.cloneNode(true));
                        languageSelect3.appendChild(option.cloneNode(true));
                    });
                })
                .catch(error => {
                    console.error('Error fetching or parsing language data:', error);
                    // Puedes mostrar un mensaje de error al usuario aquí si lo deseas
                });
        </script>
    </body>
</html>
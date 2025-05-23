<?php include 'src/views/base.php'; ?>
<!-- Iniciamos la estructura -->
<?php startblock('contenido') ?>
<link rel="stylesheet" type="text/css" href="public/css/profile.css"/>
<link rel="stylesheet" type="text/css" href="libreria/countrySelect/countrySelect.css"/>
<div class="dashboard">
    <div class="profile">
        <div class="header" style="background-image: url('<?php echo $usuario->bg_pic; ?>')">
            <a href="edit_profile"><button class="edit">Edit</button></a>
            <div class="avatar">
                <img src="<?php echo $usuario->profile_pic ?>" alt="profile picture" width="100%" height="100%">
                <div class="country-select">
                    <div class="flag"></div>
                </div>
                <div class="info-usuario">
                    <div class="nick-user">
                        <div class="nickname"><span class="nick-text"><?php echo $usuario->nickname ?></span><span class="role"><?php echo $usuario->game_roles ?></span></div>
                        <span class="username">@<?php echo $usuario->username ?></span>
                    </div>
                    
                    <div class="progress">
                        <span class="level"><span class="level-value"><?php echo $usuario->level ?></span></span>
                        <div class="life-bar-container">
                            <div class="life-bar experience-bar" style="width: <?php echo $usuario->experience ?>%;">
                                <span class="experience-value"><span class="experience-text"><?php echo $usuario->experience ?>%</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="user-details">
            <div class="bio">
                <div class="languages">
                    <span class="native <?php echo $usuario->native_lang ? '' : 'hidden' ?>">Native: <?php echo $usuario->native_lang ?></span>
                    <span class="fluent <?php echo $usuario->fluent ? '' : 'hidden' ?>">Fluent: <?php echo $usuario->fluent ?></span>
                    <span class="learning">Learning: <?php echo $usuario->learning ? $usuario->learning : $usuario->languages; ?></span>
                    <span class="future <?php echo $usuario->future ? '' : 'hidden' ?>">Future: <?php echo $usuario->future ?></span>
                    <span class="on-hold <?php echo $usuario->on_hold ? '' : 'hidden' ?>">On hold: <?php echo $usuario->on_hold ?></span>
                    <span class="dabbling <?php echo $usuario->dabbling ? '' : 'hidden' ?>">Dabbling: <?php echo $usuario->dabbling ?></span>
                </div>
                <hr class="v">
                <div class="text"><span><?php echo $usuario->bio ?></span></div>
            </div>

            <hr class="separator">
            
            <div class="stats">
                <div class="logs">
                    <span class="nb"><?php echo $totalLogs ?? 0; ?></span>
                    <span class="title">Logs</span>
                </div>
                <div class="logs">
                    <span class="nb"><?php echo $totalHoras ?? 0; ?></span>
                    <span class="title"><?php echo ($totalMinutosRaw >= 60) ? "Study Hours" : "Minutes"; ?></span>
                </div>
                <div class="logs">
                    <span class="nb">5</span>
                    <span class="title">Achievements</span>
                </div>
                <div class="logs">
                    <span class="nb">3</span>
                    <span class="title">Day Streak</span>
                </div>
            </div>

            <hr class="separator">

            <div class="follow">
                <span class="following"><?php echo $usuario->num_following ?> following</span>
                <span class="divider">|</span>
                <span class="followers"><?php echo $usuario->num_followers ?> followers</span>
            </div>
        </div>
    </div>
    <div class="feed">
        <div class="button-group">
            <h2>@<?php echo $usuario->username ?>'s Logs</h2>
        </div>
        <div class="following show">
            <?php include 'src/views/feed.php' ?>
        </div>
        <div class="my-logs hidden">
            <div class="log"></div>
            <div class="log"></div>
            <div class="log"></div>
        </div>
    </div>
</div>

<?php endblock() ?>

<?php $nombrePaisPHP = $usuario->country ?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" src="libreria/countrySelect/countrySelect.js"></script>
<script>
    
    function buscarIsoPorNombre(nombrePais) {
    var allCountries = $.fn.countrySelect.getCountryData();
    const nombrePaisLower = nombrePais.toLowerCase();
  
    for (var i = 0; i < allCountries.length; i++) {
        const nombreEnListaLower = allCountries[i].name.toLowerCase();
        if (nombreEnListaLower.includes(nombrePaisLower)) {
        return allCountries[i].iso2;
        }
    }

    return null;
    }

    // Obtenemos el valor de la variable PHP y usar la función JavaScript
    var paisDesdePHP = "<?php echo $nombrePaisPHP; ?>";
    var isoCode = buscarIsoPorNombre(paisDesdePHP);
    console.log("El código ISO de " + paisDesdePHP + " es: " + isoCode);
    document.querySelector(".flag").classList.add(isoCode);

</script>

<script>
    let successMessage = "<?php echo $_SESSION['success_message'] ?>"

    if (successMessage) {
    // Creamos y mostramos el popup de éxito
    const popup = document.createElement('div');
    popup.classList.add('popup', 'edit-profile-popup');
    const popupBubble = document.createElement('div');
    popupBubble.classList.add('speech-bubble');
    const successText = document.createElement('div');
    successText.textContent = 'Profile updated successfully!';
    popupBubble.appendChild(successText);
    popup.appendChild(popupBubble);
    document.body.appendChild(popup);

    setTimeout(() => {
      popup.classList.add('show');
      setTimeout(() => {
        popup.remove();
        <?php echo $_SESSION['success_message'] = null ?>
      }, 1500);
    }, 100);
  }

  console.log(successMessage);
</script>
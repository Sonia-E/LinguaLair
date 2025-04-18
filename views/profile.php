<?php
    // Evitamos que se llame al fichero sin pasar por el controlador
    if (!defined('CON_CONTROLADOR')) {
        // Matamos el proceso php
        die('Error: No se permite el acceso directo a esta ruta');
    }
?>
<?php include 'base.php' ?>
<!-- Iniciamos la estructura -->
<?php startblock('contenido') ?>
<link rel="stylesheet" type="text/css" href="css/profile.css"/>
<link rel="stylesheet" type="text/css" href="css/countrySelect.css"/>
<div class="dashboard">
    <div class="profile">
        <div class="header">
            <button class="edit">Edit</button>
            <div class="avatar">
                <img src="<?php echo $usuario->profile_pic ?>" alt="profile picture" width="100%" height="100%">
                <div class="country-select">
                    <div class="flag"></div>
                </div>
                <div class="info-usuario">
                    <div class="nick-user">
                        <span class="nickname"><?php echo $usuario->nickname ?><span class="role"><?php echo $usuario->game_roles ?></span></span>
                        <span class="username">@<?php echo $usuario->username ?></span>
                    </div>
                    
                    <div class="progress">
                        <span class="level"><span id="level-value"><?php echo $usuario->level ?></span></span>
                        <div class="life-bar-container">
                            <div class="life-bar" id="experience-bar" style="width: <?php echo $usuario->experience ?>%;">
                                <span id="experience-value"><span id="experience-text"><?php echo $usuario->experience ?>%</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="user-details">
            <div class="bio">
                <div class="languages">
                    <span class="fluent">Fluent: SP, EN</span>
                    <span class="learning">Learning: JP, CH, KR, PT</span>
                    <span class="future">Future: Arabic, Thai</span>
                    <span class="on-hold hidden">On hold:</span>
                    <span class="dabbling hidden">Dabbling:</span>
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
                <?php include './views/feed.php' ?>
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
<script type="text/javascript" src="./js/countrySelect.js"></script>
<script>
    
    function buscarIsoPorNombre(nombrePais) {
  var allCountries = $.fn.countrySelect.getCountryData();
  const nombrePaisLower = nombrePais.toLowerCase();
  
  for (var i = 0; i < allCountries.length; i++) {
    const nombreEnListaLower = allCountries[i].name.toLowerCase(); // Use 'name' instead of 'n'
    if (nombreEnListaLower.includes(nombrePaisLower)) {
      return allCountries[i].iso2; // Use 'iso2' instead of 'i'
    }
  }

  return null;
}

    // Obtener el valor de la variable PHP y usar la función JavaScript
  var paisDesdePHP = "<?php echo $nombrePaisPHP; ?>";
//   console.log(paisDesdePHP);
  var isoCode = buscarIsoPorNombre(paisDesdePHP);
  console.log("El código ISO de " + paisDesdePHP + " es: " + isoCode);
  document.querySelector(".flag").classList.add(isoCode);

</script>
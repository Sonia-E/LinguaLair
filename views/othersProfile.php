<?php
    // Evitamos que se llame al fichero sin pasar por el controlador
    if (!defined('CON_CONTROLADOR')) {
        // Matamos el proceso php
        die('Error: No se permite el acceso directo a esta ruta');
    }
?>
<?php include 'views/base.php' ?>
<!-- Iniciamos la estructura -->
<?php startblock('contenido') ?>
<link rel="stylesheet" type="text/css" href="css/profile.css"/>
<link rel="stylesheet" type="text/css" href="libreria/countrySelect/countrySelect.css"/>
<div class="dashboard">
    <div class="profile">
        <div class="header" style="background-image: url('<?php echo $other_user->bg_pic; ?>')">
            <button class="followButton" data-user-id="<?php echo $other_user->id; ?>">Follow</button>
            <div class="avatar">
                <img src="<?php echo $other_user->profile_pic ?>" alt="profile picture" width="100%" height="100%">
                <div class="country-select">
                    <div class="flag"></div>
                </div>
                <div class="info-usuario">
                    <div class="nick-user">
                        <div class="nickname"><span class="nick-text"><?php echo $other_user->nickname ?></span><span class="role"><?php echo $other_user->game_roles ?></span></div>
                        <span class="username">@<?php echo $other_user->username ?></span>
                    </div>
                    
                    <div class="progress">
                        <span class="level"><span id="level-value"><?php echo $other_user->level ?></span></span>
                        <div class="life-bar-container">
                            <div class="life-bar" id="experience-bar" style="width: <?php echo $other_user->experience ?>%;">
                                <span id="experience-value"><span id="experience-text"><?php echo $other_user->experience ?>%</span></span>
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
                <div class="text"><span><?php echo $other_user->bio ?></span></div>
            </div>

            <hr class="separator">
            
            <div class="stats">
                <div class="logs">
                    <span class="nb"><?php echo $other_totalLogs ?? 0; ?></span>
                    <span class="title">Logs</span>
                </div>
                <div class="logs">
                    <span class="nb"><?php echo $other_totalHoras ?? 0; ?></span>
                    <span class="title"><?php echo ($other_totalMinutosRaw >= 60) ? "Study Hours" : "Minutes"; ?></span>
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
                <span class="following"><?php echo $other_user->num_following ?> following</span>
                <span class="divider">|</span>
                <span class="followers"><?php echo $other_user->num_followers ?> followers</span>
            </div>
        </div>
    </div>
    <div class="feed">
            <div class="button-group">
                <h2>@<?php echo $other_user->username ?>'s Logs</h2>
            </div>
            <div class="following show">
                <?php $usuario = $other_user ?>
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

<?php $nombrePaisPHP = $other_user->country ?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" src="libreria/countrySelect/countrySelect.js"></script>
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
    var isoCode = buscarIsoPorNombre(paisDesdePHP);
    document.querySelector(".flag").classList.add(isoCode);

    document.addEventListener('DOMContentLoaded', function() {
        const followButton = document.querySelector('.followButton');

        if (followButton) {
            followButton.addEventListener('click', function() {
                console.log('El script de follow se está ejecutando.');
                const followedId = this.dataset.userId;
                const followerId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;

                if (followerId === 'null') {
                    alert('You must be logged in to follow users.');
                    return;
                }

                if (followedId) {
                    fetch('controllers/follow_user.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `follower_id=${followerId}&followed_id=${followedId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            followButton.textContent = 'Following'; // Update button text
                            // Optionally disable the button or provide other feedback
                        } else {
                            alert(data.message || 'Error following user.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Network error occurred.');
                    });
                } else {
                    console.error('User ID to follow not found.');
                    alert('Could not follow user.');
                }
            });
        }
    });
</script>
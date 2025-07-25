<?php include 'src/views/base.php'; ?>
<!-- Iniciamos la estructura -->
<?php startblock('contenido') ?>
<link rel="stylesheet" type="text/css" href="public/css/profile.css"/>
<link rel="stylesheet" type="text/css" href="libreria/countrySelect/countrySelect.css"/>
<div class="dashboard">
    <div class="profile other-user" data-follower-id="<?php echo isset($_SESSION['user_id']) ? htmlspecialchars(json_encode($_SESSION['user_id']), ENT_QUOTES) : 'null'; ?>">
        <div class="header" style="background-image: url('<?php echo $other_user->bg_pic; ?>')">

            <div class="follows-you <?php echo $followsYou ? '' : 'hidden' ?>"><span>Follows you</span></div>
            
            <div class="btn-group">
                <button
                    class="<?php echo $isFollowing ? 'unfollowButton' : 'followButton'; ?>"
                    data-user-id="<?php echo $other_user->id; ?>"
                >
                    <span><?php echo $isFollowing ? 'Following' : 'Follow'; ?></span>
                </button>
                <?php
                if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                    // Si el rol del usuario en la sesión es 'admin', muestra el botón Delete User
                    echo '<button class="delete-user-btn">Delete User</button>';
                    include 'src/views/deleteUser.php';
                }
                ?>
            </div>
            <div class="avatar">
                <img src="<?php echo $other_user->profile_pic ? $other_user->profile_pic : './public/img/pic_placeholder.png' ?>" alt="profile picture" width="100%" height="100%">
                <div class="country-select">
                    <div class="flag"></div>
                </div>
                <div class="info-usuario">
                    <div class="nick-user">
                        <div class="nickname"><span class="nick-text"><?php echo $other_user->nickname ?></span><span class="role"><?php echo $other_user->game_roles ?></span></div>
                        <span class="username">@<?php echo $other_user->username ?>
                        <?php if ($other_user->is_public == 0) { ?>
                            <ion-icon name="lock-closed" style="color: gray; font-size: 20px;"></ion-icon>
                        <?php } ?> 
                        </span>
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
                    <span class="native <?php echo $other_user->native_lang ? '' : 'hidden' ?>">Native: <?php echo $other_user->native_lang ?></span>
                    <span class="fluent <?php echo $other_user->fluent ? '' : 'hidden' ?>">Fluent: <?php echo $other_user->fluent ?></span>
                    <span class="learning">Learning: <?php echo $other_user->learning ? $other_user->learning : $other_user->languages; ?></span>
                    <span class="future <?php echo $other_user->future ? '' : 'hidden' ?>">Future: <?php echo $other_user->future ?></span>
                    <span class="on-hold <?php echo $other_user->on_hold ? '' : 'hidden' ?>">On hold: <?php echo $other_user->on_hold ?></span>
                    <span class="dabbling <?php echo $other_user->dabbling ? '' : 'hidden' ?>">Dabbling: <?php echo $other_user->dabbling ?></span>
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
                    <span class="nb"><?php echo $other_totalAchievements; ?></span>
                    <span class="title">Achievements</span>
                </div>
                <div class="logs">
                    <span class="nb"><?php echo $other_dayStreak; ?></span>
                    <span class="title">Day Streak</span>
                </div>
            </div>

            <hr class="separator">

            <div class="follow">
                <span class="following-count"><?php echo $other_user->num_following; ?> following</span>
                <span class="divider">|</span>
                <span class="followers-count"><?php echo $other_user->num_followers; ?> followers</span>
            </div>
        </div>
    </div>
    <div class="feed">
        <div class="button-group">
            <h2>@<?php echo $other_user->username ?>'s Logs</h2>
        </div>
        <div class="following show">
            <?php $usuario = $other_user ?>
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

<?php $nombrePaisPHP = $other_user->country ?>
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

    // Obtener el valor de la variable PHP y usar la función JavaScript
    var paisDesdePHP = "<?php echo $nombrePaisPHP; ?>";
    var isoCode = buscarIsoPorNombre(paisDesdePHP);
    document.querySelector(".flag").classList.add(isoCode);

</script>
<script type="text/javascript" src="public/js/profile.js"></script>
<script type="text/javascript" src="public/js/otherProfile.js"></script>
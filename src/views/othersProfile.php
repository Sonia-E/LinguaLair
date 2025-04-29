<?php //include 'views/base.php' ?>
<?php include 'src/views/base.php'; ?>
<!-- Iniciamos la estructura -->
<?php startblock('contenido') ?>
<link rel="stylesheet" type="text/css" href="public/css/profile.css"/>
<link rel="stylesheet" type="text/css" href="libreria/countrySelect/countrySelect.css"/>
<div class="dashboard">
    <div class="profile">
        <div class="header" style="background-image: url('<?php echo $other_user->bg_pic; ?>')">
            
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
    document.addEventListener('click', function(event) {
        const followButton = event.target.closest('.followButton');
        const unfollowButton = event.target.closest('.unfollowButton');
        const buttonSpan = event.target.querySelector('span') || (event.target.tagName === 'SPAN' ? event.target : null);

        if (followButton) {
            // Lógica para seguir (ya existente)
            const followedId = followButton.dataset.userId;
            const followerId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;

            if (followerId === 'null') {
                alert('You must be logged in to follow users.');
                return;
            }

            if (followedId) {
                fetch('follow_user', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `follower_id=${followerId}&followed_id=${followedId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (buttonSpan) {
                            buttonSpan.textContent = 'Following';
                        }
                        followButton.classList.remove('followButton');
                        followButton.classList.add('unfollowButton');
                        
                        // Actualizar los contadores usuario del perfil
                        const followersCountElement = document.querySelector('.followers-count');

                        if (followersCountElement) {
                            let currentFollowers = parseInt(followersCountElement.textContent);
                            followersCountElement.textContent = (currentFollowers + 1) + ' followers';
                        }

                        // Actualizar contadores usuario loggeado
                        const loggedFollowingCountElement = document.getElementById('logged-following-count');
                        const loggedFollowersCountElement = document.getElementById('logged-followers-count');

                        if (loggedFollowingCountElement) {
                            let currentFollowing = parseInt(loggedFollowingCountElement.textContent);
                            loggedFollowingCountElement.textContent = (currentFollowing + 1) + ' following';
                        }

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
        } else if (unfollowButton) {
            // Lógica para dejar de seguir
            const followedId = unfollowButton.dataset.userId;
            const followerId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;

            if (followerId === 'null') {
                alert('You must be logged in to unfollow users.');
                return;
            }

            if (followedId) {
                fetch('unfollow_user', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `follower_id=${followerId}&followed_id=${followedId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (buttonSpan) {
                            buttonSpan.textContent = 'Follow';
                        }
                        unfollowButton.classList.remove('unfollowButton');
                        unfollowButton.classList.add('followButton');

                        // Actualizar los contadores
                        const followersCountElement = document.querySelector('.followers-count');

                        if (followersCountElement) {
                            let currentFollowers = parseInt(followersCountElement.textContent);
                            followersCountElement.textContent = (currentFollowers - 1) + ' followers';
                        }

                        // Actualizar contadores usuario loggeado
                        const loggedFollowingCountElement = document.getElementById('logged-following-count');

                        if (loggedFollowingCountElement) {
                            let currentFollowing = parseInt(loggedFollowingCountElement.textContent);
                            loggedFollowingCountElement.textContent = (currentFollowing - 1) + ' following';
                        }

                    } else {
                        alert(data.message || 'Error unfollowing user.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Network error occurred.');
                });
            } else {
                console.error('User ID to unfollow not found.');
                alert('Could not unfollow user.');
            }
        }
    });
});
</script>
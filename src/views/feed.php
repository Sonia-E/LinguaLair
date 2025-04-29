<?php
    $initialLogLimit = 10; // Number of logs to load initially

    use Sonia\LinguaLair\Models\SocialModel;
    $SocialModel = new SocialModel("localhost", "foc", "foc", 'LinguaLair');

    $loggedInUserId = $_SESSION['user_id'];

    // Encaminamos la petición internamente
    $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $uri = str_replace('/LinguaLair/', '', $uri);
    
    if ($uri == 'profile' && isset($_GET['id'])) {
        $usersToShowLogs = [$_GET['id']];
        $logs = $SocialModel->getLogsForUsers($usersToShowLogs, $initialLogLimit);
        $totalLogCount = $SocialModel->getTotalLogCountForUsers($usersToShowLogs);
    } else {
        // Obtener la lista de IDs de los usuarios que el usuario logueado sigue
        $followingIds = $SocialModel->getFollowingUsers($loggedInUserId);

        // Inicializar el array de usuarios a mostrar con el ID del usuario logueado
        $usersToShowLogs = [$loggedInUserId];

        // Agregar los IDs de los usuarios seguidos al array, si existen y es un array
        if ($followingIds && is_array($followingIds)) {
            $usersToShowLogs = array_merge($usersToShowLogs, $followingIds);
        }

        $logs = $SocialModel->getLogsForUsers($usersToShowLogs, $initialLogLimit);
        
        $totalLogCount = $SocialModel->getTotalLogCountForUsers($usersToShowLogs);
    }
?>

<div class="log-container">
    <?php foreach ($logs as $log) { ?>
        <div class="log-container">
            <div class="dropdown" data-log-identifier="<?php echo $log['username'] ?>_<?php echo $log['id'] ?>">
                <div class="log">
                    <div class="usuario">
                        <a href="profile?id=<?php echo $log['user_id'] ?>">
                            <div class="log-user">
                                <img src="<?php echo $log['profile_pic'] ?>" alt="profile picture">
                                <div class="info-usuario">
                                    <div class="nick-user">
                                        <span class="nickname"><?php echo $log['nickname'] ?></span>
                                        <span class="username">@<?php echo $log['username'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <div class="log-column">
                            <div class="log-date"><span><?php echo $log['log_date'] ?></span></div>
                            <div class="duration">
                                <span><?php echo $log['duration'] ?></span>
                                <span>minutes</span>
                            </div>
                        </div>
                    </div>
                    <div class="log-data">
                        <div class="log-row">
                            <div class="description">
                                <span><?php echo $log['description'] ?></span>
                            </div>
                            <div class="log-column">
                                <div class="language">
                                    <span><?php echo $log['language'] ?></span>
                                </div>
                                <div class="type">
                                    <span><?php echo $log['type'] ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="post-date">
                            <span><strong>Post Date:</strong> <?php echo $log['post_date'] ?></span>
                            <div class="log_options_btn"><ion-icon name="ellipsis-horizontal-outline"></ion-icon></div>
                        </div>
                    </div>
                </div>
                
                <div class="log-options-popup"> 
                    <div class="delete_log"><ion-icon name="trash-outline"></ion-icon><span>Delete Log</span></div>
                </div>
            </div>
            <?php include 'src/views/deleteLog.php' ?>
        </div>
    <?php } ?>

    <?php if ($totalLogCount > $initialLogLimit) { ?>
        <div class="load-more-container">
            <button id="load-more-logs">Load More</button>
        </div>
    <?php } ?>
</div>

<script>
    const logContainer = document.querySelector('.show');
    let currentLogCount = <?php echo count($logs); ?>;
    const logsToLoad = 5;
    let isLoading = false;
    const totalLogCount = <?php echo $totalLogCount; ?>; // Make sure this is available in your JS

    if (logContainer) {
        logContainer.addEventListener('click', function(event) {
            const loadMoreButton = event.target.closest('#load-more-logs'); // Use closest for robustness

            if (loadMoreButton && !isLoading && currentLogCount < totalLogCount) {
                isLoading = true;
                loadMoreButton.textContent = 'Loading...';

                fetch(`/LinguaLair/index.php?action=load_more_logs&offset=${currentLogCount}&limit=${logsToLoad}&user_id=<?php echo $loggedInUserId; ?>
                &followed_users=<?php echo json_encode($usersToShowLogs); ?>`)
                    .then(response => response.text())
                    .then(data => {
                        if (data) {
                            logContainer.insertAdjacentHTML('beforeend', data);
                            currentLogCount += logsToLoad;

                            // Move the button container to the end (if you still want this)
                            const loadMoreContainer = document.querySelector('.load-more-container');
                            if (loadMoreContainer) {
                                logContainer.appendChild(loadMoreContainer);
                            }

                            if (currentLogCount >= totalLogCount) {
                                if (loadMoreContainer) {
                                    loadMoreContainer.style.display = 'none';
                                }
                            } else {
                                loadMoreButton.textContent = 'Load More';
                            }
                        } else {
                            const loadMoreContainer = document.querySelector('.load-more-container');
                            if (loadMoreContainer) {
                                loadMoreButton.textContent = 'No More Logs';
                            }
                        }
                        isLoading = false;
                    })
                    .catch(error => {
                        console.error('Error loading more logs:', error);
                        const loadMoreContainer = document.querySelector('.load-more-container');
                        if (loadMoreContainer) {
                            loadMoreButton.textContent = 'Error Loading';
                        }
                        isLoading = false;
                    });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
    const logOptionButtons = document.querySelectorAll('.log_options_btn');
    console.log("saludos desde botón options");

    logOptionButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.stopPropagation(); // Evitar que el clic se propague a otros elementos

            const logContainer = this.closest('.log-container');
            const optionsPopup = logContainer.querySelector('.log-options-popup');

            // Ocultar cualquier otro popup abierto
            document.querySelectorAll('.log-options-popup.show').forEach(otherPopup => {
                if (otherPopup !== optionsPopup) {
                    otherPopup.classList.remove('show');
                }
            });

            // Mostrar u ocultar el popup actual
            optionsPopup.classList.toggle('show');
        });
    });

    // Ocultar el popup cuando se hace clic fuera de él
    document.addEventListener('click', function(event) {
        document.querySelectorAll('.log-options-popup.show').forEach(optionsPopup => {
            if (!event.target.closest('.log-container') || !event.target.closest('.log_options_btn')) {
                optionsPopup.classList.remove('show');
            }
        });
    });
});
</script>
<script type="text/javascript" src="public/js/logs.js"></script>
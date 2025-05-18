<?php
    $initialLogLimit = 10; // Number of logs to load initially

    // Conection variables
    $server = 'localhost';
    $user = 'root';
    $password = '';
    $database = 'LinguaLair';

    use Sonia\LinguaLair\Models\modelo;
    $modelo = new Modelo($server, $user, $password, $database);
    use Sonia\LinguaLair\Models\NotificationModel;
    $NotificationModel = new NotificationModel($server, $user, $password, $database);
    use Sonia\LinguaLair\Models\SocialModel;
    $SocialModel = new SocialModel($server, $user, $password, $database, $NotificationModel, $modelo);

    $loggedInUserId = $_SESSION['user_id'];
    $userRole = $_SESSION['user_role'] ?? 'user';

    // Encaminamos la petición internamente
    $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $uri = str_replace('/LinguaLair/', '', $uri);
    
    if ($uri == 'profile' && isset($_GET['id'])) {
        $usersToShowLogs = [$_GET['id']];
        $logs = $SocialModel->getLogsForUsers($usersToShowLogs, $initialLogLimit);
        $totalLogCount = $SocialModel->getTotalLogCountForUsers($usersToShowLogs);
    } else {
        // Obtenemos la lista de IDs de los usuarios que el usuario logueado sigue
        $followingIds = $SocialModel->getFollowingUsers($loggedInUserId);

        // Inicializamos el array de usuarios a mostrar con el ID del usuario logueado
        $usersToShowLogs = [$loggedInUserId];

        // Agregamos los IDs de los usuarios seguidos al array, si existen y es un array
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
                            <?php if ($userRole == 'admin' || $userRole == 'premium' && $log['user_id'] == $loggedInUserId || $log['user_id'] == $loggedInUserId): ?>
                                <div class="log_options_btn"><ion-icon name="ellipsis-horizontal-outline"></ion-icon></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="log-options-popup">
                    <div class="options-buttons">
                        <?php if ($userRole == 'standard' || $userRole == 'admin' || $userRole == 'premium'): ?>
                            <div class="delete_log"><ion-icon name="trash-outline"></ion-icon><span>Delete Log</span></div>
                        <?php endif; ?>
                        <?php if ($userRole == 'admin' || $userRole == 'premium' && $log['user_id'] == $loggedInUserId): ?>
                            <div class="edit_log"><ion-icon name="create-outline"></ion-icon><span>Edit Log</span></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php include 'src/views/deleteLog.php' ?>
            <?php include 'src/views/editLog.php' ?>
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
    const totalLogCount = <?php echo $totalLogCount; ?>;

    if (logContainer) {
        logContainer.addEventListener('click', function(event) {
            const loadMoreButton = event.target.closest('#load-more-logs');

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
</script>
<script type="text/javascript" src="public/js/logs.js"></script>
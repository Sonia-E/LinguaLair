<?php
$initialLogLimit = 10; // Number of logs to load initially
require_once './modelo.php';
    $modelo = new Modelo("localhost", "foc", "foc", 'LinguaLair');
    $loggedInUserId = $_SESSION['user_id'];

// Array containing only the ID of the logged-in user
$usersToShowLogs = [$loggedInUserId];
$logs = $modelo->getLogsForUsers($usersToShowLogs, $initialLogLimit);
$totalLogCount = $modelo->getTotalLogCountForUsers($usersToShowLogs); // Function to get the total number of logs
?>

<div class="log-container">
    <?php foreach ($logs as $log) { ?>
        <div class="log">
            <div class="usuario">
                <div class="log-user">
                    <img src="<?php echo $usuario->profile_pic ?>" alt="profile picture">
                    <div class="info-usuario">
                        <div class="nick-user">
                            <span class="nickname"><?php echo $usuario->nickname ?></span>
                            <span class="username">@<?php echo $usuario->username ?></span>
                        </div>
                    </div>
                </div>
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
                </div>
            </div>
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

            fetch(`/LinguaLair/index.php?action=load_more_logs&offset=${currentLogCount}&limit=${logsToLoad}&user_id=<?php echo $loggedInUserId; ?>`)
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
</script>
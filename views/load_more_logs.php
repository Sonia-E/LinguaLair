<?php

if (isset($_SESSION['user_id']) && isset($_GET['offset']) && isset($_GET['limit']) && isset($_GET['user_id'])) {
    $userId = intval($_GET['user_id']);
    $offset = intval($_GET['offset']);
    $limit = intval($_GET['limit']);

    $array_usuario = $modelo->getUser($userId);
    $usuario = $array_usuario[0][0];

    $usersToShowLogs = [$userId];

    // Get the users the current user is following (adapt as needed)
    // $following = $modelo->getFollowingUsers($userId);
    // $usersToShowLogs = array_merge([$userId], $following);

    $moreLogs = $modelo->getLogsForUsers($usersToShowLogs, $limit, $offset);

    if ($moreLogs) {
        foreach ($moreLogs as $log) {
            ?>
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
            <?php
        }
    } else {
        // No more logs - you can optionally send a specific response
        echo ''; // Send an empty string
    }
} else {
    // Handle invalid request
    header('HTTP/1.1 400 Bad Request');
    echo 'Invalid request.';
}

// Crucially, ensure nothing else is echoed here that would form a full HTML page.
?>
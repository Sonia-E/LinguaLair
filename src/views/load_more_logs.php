<?php
if (isset($_SESSION['user_id']) && isset($_GET['offset']) && isset($_GET['limit']) && isset($_GET['user_id']) && isset($_GET['followed_users'])) {
    $userId = intval($_GET['user_id']);
    $offset = intval($_GET['offset']);
    $limit = intval($_GET['limit']);
    $followedUsersJson = isset($_GET['followed_users']) ? $_GET['followed_users'] : '[]';
    $followedUsers = json_decode($followedUsersJson, true);

    $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $uri = str_replace('/LinguaLair/', '', $uri);
    
    if ($uri == 'profile' && isset($userId)) {
        $usersToShowLogs = [$userId];
        $moreLogs = $SocialModel->getLogsForUsers($usersToShowLogs, $limit, $offset);
        $totalLogCount = $SocialModel->getTotalLogCountForUsers($usersToShowLogs);
    } else {
        $moreLogs = $SocialModel->getLogsForUsers($followedUsers, $limit, $offset);
        $totalLogCount = $SocialModel->getTotalLogCountForUsers($followedUsers);
    }

    if ($moreLogs) {
        foreach ($moreLogs as $log) {
            ?>
            <div class="log">
                <div class="usuario">
                    <a href="profile?id=<?php echo $log['user_id']?>">
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
?>
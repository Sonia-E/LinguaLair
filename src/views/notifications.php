<?php include 'src/views/base.php'; ?>
<?php startblock('contenido') ?>
<link rel="stylesheet" type="text/css" href="public/css/achievements.css"/> 
<div class="dashboard">
    <div class="achievements"> <div class="button-group">
            <h2>Notifications</h2> </div>
        <div class="achievement-list">  
            <?php if (!empty($notifications)): ?>
            <div class="unlocked-achievements-container"> <?php foreach ($notifications as $notification): ?>
                <div class="achievement-item">  <?php
                    $icon = '';
                    switch ($notification['type']) {
                        case 'follow':
                            $icon = '<ion-icon name="people-outline"></ion-icon>';
                            break;
                        case 'achievements':
                            $icon = '<ion-icon name="diamond-outline"></ion-icon>'; 
                            break;
                        case 'events':
                            $icon = '<ion-icon name="chatbubbles-outline"></ion-icon>';
                            break;
                        default:
                            $icon = '<ion-icon name="notifications-outline"></ion-icon>';
                    }
                    ?>
                    <div class="achievement-icon" alt="notification icon"><?php echo $icon; ?></div>
                    <div class="achievement-details">
                        <h4 class="achievement-title"><?php echo $notification['content']; ?></h4> 
                    </div>
                    <span class="achievement-date">Date: <?php echo $notification['created_at']; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="no-notifs">You have no notifications</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endblock() ?>
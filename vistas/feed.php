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
                <div class="post-date"><span><?php echo $log->post_date ?></span></div>
                <div class="duration">
                    <span><?php echo $log->duration ?></span>
                    <span>minutes</span>
                </div>
            </div>
        </div>
        <div class="log-data">
            <div class="log-row">
                <div class="description">
                    <span><?php echo $log->description ?></span>
                </div>
                <div class="log-column">
                    <div class="language">
                        <span><?php echo $log->language ?></span>
                    </div>
                    <div class="type">
                        <span><?php echo $log->type ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
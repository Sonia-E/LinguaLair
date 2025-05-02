<?php include 'src/views/base.php'; ?>
<!-- Iniciamos la estructura -->
<?php startblock('contenido') ?>
<link rel="stylesheet" type="text/css" href="public/css/achievements.css"/>
<div class="dashboard">
    <div class="achievements">
        <div class="button-group">
            <h2>Unlocked</h2>
        </div>
        <div class="achievement-list">
            <?php if (!empty($unlockedAchievements)): ?>
            <div class="unlocked-achievements-container">
                <?php foreach ($unlockedAchievements as $achievement): ?>
                    <div class="achievement-item">
                        <img class="achievement-icon" src="<?php echo $achievement->icon ?>" alt="achievement icon">
                        <div class="achievement-details">
                            <h4 class="achievement-title"><?php echo $achievement->name ?></h4>
                            <p class="achievement-description"><?php echo $achievement->description ?></p>
                            <?php if ($achievement->level): ?>
                                <span class="achievement-level"><?php echo ucfirst($achievement->level) ?></span>
                            <?php endif; ?>
                        </div>
                        <span class="achievement-date">Unlocked Date: <?php echo $achievement->unlock_date ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <p>You haven't unlocked any achievement yet :(</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="achievements">
        <div class="button-group">
            <h2>Locked</h2>
        </div>
        <div class="achievement-list">
            <?php if (!empty($lockedAchievements)): ?>
            <div class="unlocked-achievements-container">
                <?php foreach ($lockedAchievements as $achievement): ?>
                    <div class="achievement-item">
                        <img class="achievement-icon" src="<?php echo $achievement->icon ?>" alt="achievement icon">
                        <div class="achievement-details">
                            <h4 class="achievement-title"><?php echo $achievement->name ?></h4>
                            <p class="achievement-description"><?php echo $achievement->description ?></p>
                            <?php if ($achievement->level): ?>
                                <span class="achievement-level"><?php echo ucfirst($achievement->level) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <p>You have unlocked all achievements!</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endblock() ?>

<script type="text/javascript" src="public/js/explore.js"></script>
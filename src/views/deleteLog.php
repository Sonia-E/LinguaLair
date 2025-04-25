<?php // $logId = $_GET['log_id']; ?>
<div class="popup">
    <div class="popuptext" id="DeleteLogPopup">
    <ion-icon class="close-button" name="close-outline"></ion-icon>
        <div class="delete-title">
            <h2>Are you sure you want</h2>
            <h2>to delete this log?</h2>
            <hr>
        </div>
        <div class="delete-btn-group">
            <a href="delete_user?id=<?php echo htmlspecialchars($logId) ?>" class="link-delete-yes"><button class="delete-log-yes">Yes</button></a>
            <button class="delete-log-no">No</button>
        </div>
    </div>
</div>
<script type="text/javascript" src="js/logs.js"></script>
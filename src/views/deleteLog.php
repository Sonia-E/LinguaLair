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
            <form id="deleLogForm" action="delete_log" method="post">
                <input type="hidden" name="log_identifier" id="log_identifier">
                <button type="submit" class="delete-log-yes">Yes</button>
            </form>
            <button class="delete-log-no">No</button>
        </div>
    </div>
</div>
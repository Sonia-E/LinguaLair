<?php $profileUserId = $_GET['id']; ?>
<div class="popup">
    <div class="popuptext" id="DeletePopup">
    <ion-icon class="close-button" name="close-outline"></ion-icon>
        <div class="delete-title">
            <h2>Are you sure you want</h2>
            <h2>to delete this user's account?</h2>
            <hr>
        </div>
        <div class="delete-btn-group">
            <a href="delete_user?id=<?php echo htmlspecialchars($profileUserId) ?>" class="link-delete-yes"><button class="delete-yes">Yes</button></a>
            <button class="delete-no">No</button>
        </div>
    </div>
</div>
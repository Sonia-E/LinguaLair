<?php include 'src/views/base.php'; ?>
<!-- Iniciamos la estructura -->
<?php startblock('contenido') ?>
<link rel="stylesheet" type="text/css" href="public/css/editProfile.css"/>
<div class="dashboard">
        <form id="profile-form" action="edit_profile" method="post" enctype="multipart/form-data">
    <div class="container">
        <a href="my_profile"><ion-icon class="close-edit-button" name="close-outline"></ion-icon></a>
        <div class="left-side">
            <div class="logo">
                <h2>Edit Profile</h2>
            </div>
            <div class="login-form">
                <div class="form-group">
                    <label for="nickname">Nickname:</label>
                    <input type="text" id="nickname" name="nickname" class="form-control" value="<?php echo $usuario->nickname ?>">
                </div>

                <div class="form-group">
                    <label for="bio">Write something about yourself:</label>
                    <textarea id="bio" name="bio" class="form-control" placeholder="<?php echo $usuario->bio ?>"></textarea>
                </div>

                <div class="form-group">
                    <label for="native_lang">Native Languages (always visible):</label>
                    <input type="text" id="native_lang" name="native_lang" class="form-control" value="<?php echo $usuario->native_lang ?>" required>
            </div>

                <div class="form-group">
                    <label for="languages">Your languages for the stats and logs:</label>
                    <input type="text" id="languages" name="languages" class="form-control" value="<?php echo $usuario->languages ?>" required>
                </div>

                <div class="form-group">
                    <label for="learning">Languages Learning (always visible):</label>
                    <input type="text" id="learning" name="learning" class="form-control" value="<?php echo $usuario->learning ? $usuario->learning : $usuario->languages; ?>">
                </div>

                <div class="form-group visible">
                    <div class="label-input">
                        <label for="fluent">Languages Fluent In:</label>
                        <input type="text" id="fluent" name="fluent" class="form-control" value="<?php echo $usuario->fluent ?>">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="fluent_visible" name="fluent_visible" class="form-check-input" <?php echo $usuario->fluent ? 'checked' : '' ?>>
                        <label class="form-check-label" for="fluent_visible">Visible</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="right-side">
            <div class="login-form">
                <div class="form-group visible">
                    <div class="label-input">
                        <label for="on_hold">Languages On Hold:</label>
                        <input type="text" id="on_hold" name="on_hold" class="form-control" value="<?php echo $usuario->on_hold ?>">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="on_hold_visible" name="on_hold_visible" class="form-check-input" <?php echo $usuario->on_hold ? 'checked' : '' ?>>
                        <label class="form-check-label" for="on_hold_visible">Visible</label>
                    </div>
                </div>

                
                <div class="form-group visible">
                    <div class="label-input">
                        <label for="dabbling">Languages Dabbling In:</label>
                        <input type="text" id="dabbling" name="dabbling" class="form-control" value="<?php echo $usuario->dabbling ?>">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="dabbling_visible" name="dabbling_visible" class="form-check-input" <?php echo $usuario->dabbling ? 'checked' : '' ?>>
                        <label class="form-check-label" for="dabbling_visible">Visible</label>
                    </div>
                </div>

                <div class="form-group visible">
                    <div class="label-input">
                        <label for="future">Languages for the Future:</label>
                        <input type="text" id="future" name="future" class="form-control" value="<?php echo $usuario->future ?>">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="future_visible" name="future_visible" class="form-check-input" <?php echo $usuario->future ? 'checked' : '' ?>>
                        <label class="form-check-label" for="future_visible">Visible</label>
                    </div>
                </div>

                <div class="form-group">
                    <input type="file" hidden id="profile_pic" name="profile_pic" accept="image/*">
                    <input type="button" id="profile_pic_button" value="Choose a profile picture">
                    <label id="selected_profile">Nothing selected</label>
                </div>

                <div class="form-group">
                    <input type="file" hidden id="bg_pic" name="bg_pic" accept="image/*">
                    <input type="button" id="bg_pic_button" value="Choose a header picture">
                    <label id="selected_bg">Nothing selected</label>
                </div>

                <div class="form-group">
                    <label for="dark_mode">Dark Mode:</label>
                    <div class="radio-group">
                        <div class="radio-item">
                            <input type="radio" id="dark_yes" name="dark_mode" value="1" <?php echo $usuario->dark_mode ? 'checked' : '' ?>>
                            <label for="dark_yes">Yes</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="dark_no" name="dark_mode" value="0" <?php echo $usuario->dark_mode ? '' : 'checked' ?>>
                            <label for="dark_no">No</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="public">Do you want other users to see your profile?</label> 
                    <div class="radio-group">
                        <div class="radio-item">
                            <input type="radio" id="public_yes" name="public" value="1" <?php echo $usuario->is_public ? 'checked' : '' ?>>
                            <label for="public_yes">Yes</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="public_no" name="public" value="0" <?php echo $usuario->is_public ? '' : 'checked' ?>>
                            <label for="public_no">No</label>
                        </div>
                    </div>
                </div>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="error-message"><?php echo $_SESSION['error_message'] ?></div>
                <?php endif; ?>

                <button type="submit" class="sign-in-button">Save Changes</button>
            </div>
        </div>
    </div>
</form>


</div>

<?php endblock() ?>


<script type="text/javascript" src="public/js/selectFile.js"></script>
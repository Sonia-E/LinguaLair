<?php include 'src/views/base.php'; ?>
<!-- Iniciamos la estructura -->
<?php startblock('contenido') ?>
<link rel="stylesheet" type="text/css" href="public/css/editProfile.css"/>
<div class="dashboard">
        <form id="profile-form" action="edit_profile" method="post" enctype="multipart/form-data">
    <div class="container">
        <div class="left-side">
            <div class="login-form">
                <div class="form-group">
                    <label for="nickname">Nickname:</label>
                    <input type="text" id="nickname" name="native_lang" class="form-control" placeholder="<?php echo $usuario->nickname ?>">
                </div>

                <div class="form-group">
                    <label for="bio">Write something about yourself:</label>
                    <textarea id="bio" name="bio" class="form-control" placeholder="<?php echo $usuario->bio ?>"></textarea>
                </div>

                <div class="form-group visible">
                    <div class="label-input">
                        <label for="native_lang">Native Languages:</label>
                        <input type="text" id="native_lang" name="native_lang" class="form-control" placeholder="<?php echo $usuario->native_lang ?>" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="fluent_visible" name="fluent_visible" class="form-check-input" checked>
                        <label class="form-check-label" for="fluent_visible">Visible</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="languages">Your languages for the stats and logs:</label>
                    <input type="text" id="languages" name="languages" class="form-control" placeholder="<?php echo $usuario->languages ?>" required>
                </div>

                <div class="form-group visible">
                    <div class="label-input">
                        <label for="fluent">Languages Fluent In:</label>
                        <input type="text" id="fluent" name="fluent" class="form-control" placeholder="<?php echo $usuario->fluent ?>">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="fluent_visible" name="fluent_visible" class="form-check-input">
                        <label class="form-check-label" for="fluent_visible">Visible</label>
                    </div>
                </div>

                <div class="form-group visible">
                    <div class="label-input">
                        <label for="learning">Languages Learning:</label>
                        <input type="text" id="learning" name="learning" class="form-control" placeholder="<?php echo $usuario->learning ? $usuario->learning : $usuario->languages; ?>">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="learning_visible" name="learning_visible" class="form-check-input" checked>
                        <label class="form-check-label" for="learning_visible">Visible</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="right-side">
            <div class="login-form">
                <div class="form-group visible">
                    <div class="label-input">
                        <label for="on_hold">Languages On Hold:</label>
                        <input type="text" id="on_hold" name="on_hold" class="form-control" placeholder="<?php echo $usuario->on_hold ?>">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="on_hold_visible" name="on_hold_visible" class="form-check-input">
                        <label class="form-check-label" for="on_hold_visible">Visible</label>
                    </div>
                </div>

                
                <div class="form-group visible">
                    <div class="label-input">
                        <label for="dabbling">Languages Dabbling In:</label>
                        <input type="text" id="dabbling" name="dabbling" class="form-control" placeholder="<?php echo $usuario->dabbling ?>">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="dabbling_visible" name="dabbling_visible" class="form-check-input">
                        <label class="form-check-label" for="dabbling_visible">Visible</label>
                    </div>
                </div>

                <div class="form-group visible">
                    <div class="label-input">
                        <label for="future">Languages for the Future:</label>
                        <input type="text" id="future" name="future" class="form-control" placeholder="<?php echo $usuario->future ?>">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="future_visible" name="future_visible" class="form-check-input">
                        <label class="form-check-label" for="future_visible">Visible</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="profile_pic">Profile Picture:</label>
                    <input type=file hidden id=profile_pic name="profile_pic">
                    <input type=button onClick=getFile.simulate() value="Choose a picture">
                    <label id=selected>Nothing selected</label>
                </div>

                <div class="form-group">
                    <label for="bg_pic">Background Picture:</label>
                    <input type="file" id="bg_pic" name="bg_pic" class="form-control-file" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="dark_mode">Dark Mode:</label>
                    <div class="radio-group">
                        <div class="radio-item">
                            <input type="radio" id="dark_yes" name="dark_mode" value="1">
                            <label for="dark_yes">Yes</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="dark_no" name="dark_mode" value="0" checked>
                            <label for="dark_no">No</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="public">Do you want other users to see your profile?</label> 
                    <div class="radio-group">
                        <div class="radio-item">
                            <input type="radio" id="public_yes" name="public" value="1" checked>
                            <label for="public_yes">Yes</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="public_no" name="public" value="0">
                            <label for="public_no">No</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="sign-in-button">Save Changes</button>
            </div>
        </div>
    </div>
</form>


</div>

<?php endblock() ?>

<script type="text/javascript" src="public/js/selectFile.js"></script>

<script>

    var getFile = new selectFile;
    getFile.targets('profile_pic','selected');
</script>
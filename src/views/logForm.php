<div class="popup">
    <div class="popuptext" id="myPopup">
    <ion-icon class="close-button" name="close-outline"></ion-icon>
        <h2>Create a new log</h2>
        <hr>
        <form id="addLogForm" action="" method="post" class="log-form">
            <div class="form-group">
                <label for="description">What did you do in your target language?</label>
                <textarea name="description" id="description" required></textarea>
            </div>

            <div class="form-group">
                <label for="language">Language</label>
                <select name="language" id="language">
                    <?php
                    $languages = explode(',', $usuario->languages);

                    foreach ($languages as $language) {
                        $trimmedLanguage = trim($language);
                        echo '<option value="' . $trimmedLanguage . '">' . htmlspecialchars($trimmedLanguage) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="type">Type of Activity</label>
                <select name="type" id="type">
                    <?php
                    foreach ($logTypes as $type) {
                        $capitalizedType = ucfirst($type);
                        echo '<option value="' . htmlspecialchars($type) . '">' . htmlspecialchars($capitalizedType) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group row-inputs">
                <div class="input-group">
                    <label for="duration">Duration (min)</label>
                    <input type="number" name="duration" id="duration" min="1" required>
                </div>
                <div class="input-group">
                    <label for="date">Date</label>
                    <input type="date" name="date" id="date" required>
                </div>
            </div>

            <button type="submit">Save Log</button>
        </form>
    </div>
</div>
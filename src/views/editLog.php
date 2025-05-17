<div class="popup">
    <div class="popuptext" id="EditLogPopup">
    <ion-icon class="close-button" name="close-outline"></ion-icon>
        <h2>Edit log</h2>
        <hr>
        <form id="editLogForm" action="" method="post" class="log-form">
            <div class="form-group">
                <label for="description">What did you do in your target language?</label>
                <textarea name="description" id="edit_description" required></textarea>
            </div>

            <div class="form-group">
                <label for="language">Language</label>
                <select name="language" id="edit_language">
                    <?php
                    $languages = explode(',', $usuario->languages);

                    foreach ($languages as $language) {
                        $trimmedLanguage = trim($language);
                        if ($trimmedLanguage == $log['language']) {
                            echo '<option value="' . $log['language'] . '" selected>' . $log['language'] . '</option>';
                        } else {
                            echo '<option value="' . $trimmedLanguage . '">' . htmlspecialchars($trimmedLanguage) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="type">Type of Activity</label>
                <select name="type" id="edit_type">
                    <?php
                    foreach ($logTypes as $type) {
                        $capitalizedType = ucfirst($type);
                        if ($type == $log['type']) {
                            $capitalizedType = ucfirst($log['type']);
                            echo '<option value="' . $capitalizedType . '" selected>' . $capitalizedType . '</option>';
                        } else {
                            echo '<option value="' . htmlspecialchars($type) . '">' . htmlspecialchars($capitalizedType) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group row-inputs">
                <div class="input-group">
                    <label for="duration">Duration (min)</label>
                    <input type="number" name="duration" id="edit_duration" min="1" required value="<?php echo $log['duration'] ?>">
                </div>
                <div class="input-group">
                    <label for="date">Date</label>
                    <input type="date" name="date" id="edit_date" required value="<?php
                        $fecha_guardada = trim($log['log_date']);
                        $fecha_objeto = new DateTime($fecha_guardada, new DateTimeZone('UTC')); // O la zona horaria que corresponda a tus datos
                        echo $fecha_objeto->format('Y-m-d');
                    ?>">
                </div>
            </div>

            <input type="hidden" name="edit_log_identifier" id="edit_log_identifier">

            <button type="submit">Save Log</button>
        </form>
    </div>
</div>
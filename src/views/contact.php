<?php include 'src/views/base.php'; ?>
<?php startblock('contenido') ?>
<link rel="stylesheet" type="text/css" href="public/css/contact.css"/>
<div class="dashboard">
    <div class="container contact">
        <h2>Contact us!</h2>
        <form action="contact" method="POST" id="reportarIncidenciaForm">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="user_email">Email:</label>
                <input type="email" id="user_email" name="user_email">
            </div>
            <div class="form-group">
                <label for="incident_type">Incident Type:</label>
                <select id="incident_type" name="incident_type" required>
                    <option value="">Select a category</option>
                    <option value="Login issue">Login issue</option>
                    <option value="Functionality not working">Functionality not working</option>
                    <option value="Consultation">Consultation</option>
                    <option value="Improvement suggestion">Improvement suggestion</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="urgency">Urgency:</label>
                <select id="urgency" name="urgency" required>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="critical">Critical</option>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Describe the incident:</label>
                <textarea id="description" name="description" rows="5" required></textarea>
            </div>
            <div class="btn">
                <button type="submit" name="submit_incident">Send Incident</but>
            </div>

        </form>

        <?php
        // Mensajes de error o éxito después del procesamiento del formulario
        if (isset($mensajeError)) {
            echo '<div style="color: red; margin-top: 10px; text-align: center;">' . $mensajeError . '</div>';
        }
        if (isset($mensajeExito)) {
            echo '<div style="color: green; margin-top: 10px; text-align: center;">' . $mensajeExito . '</div>';
        }
        ?>
    </div>
</div>
<?php endblock() ?>
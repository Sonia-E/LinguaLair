<?php

function enviarCorreoIncidencia($destinatarioEmail, $destinatarioNombre, $incidencia) {
    $to = $destinatarioEmail;
    $subject = 'New Incident Registered - ID: ' . $incidencia['id'];

    $message = "A new incident has been registered with the following details:\n\n";
    $message .= "ID: " . $incidencia['id'] . "\n";
    $message .= "Creation date: " . $incidencia['fecha_creacion'] . "\n";
    $message .= "Username: " . $incidencia['nombre_usuario'] . "\n";
    if (!empty($incidencia['email_usuario'])) {
        $message .= "User's email: " . $incidencia['email_usuario'] . "\n";
    }
    $message .= "Incidet type: " . $incidencia['tipo_incidencia'] . "\n";
    $message .= "Urgency: " . $incidencia['urgencia'] . "\n";
    $message .= "Description:\n" . $incidencia['descripcion'] . "\n";
    $message .= "State: " . $incidencia['estado'] . "\n";
    $message .= "\n";
    $message .= "Sincerelly,\n";
    $message .= "Incidents Management System";

    $headers = 'From: tu_email_remitente@tu_dominio.com' . "\r\n" .
               'Reply-To: tu_email_remitente@tu_dominio.com' . "\r\n" .
               'X-Mailer: PHP/' . phpversion();

    if (mail($to, $subject, $message, $headers)) {
        return true; // El correo se envió correctamente
    } else {
        error_log("Error al enviar el correo de incidencia con mail()");
        return false; // Hubo un error al enviar el correo
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_incidencia'])) {
    // 1. Recibir datos del formulario
    $username = $_POST['username'] ?? '';
    $user_email = $_POST['user_email'] ?? null;
    $incident_type = $_POST['incident_type'] ?? '';
    $description = $_POST['description'] ?? '';
    $urgency = $_POST['urgency'] ?? 'medium'; // Valor por defecto si no se selecciona

    // 2. Validar los datos (¡Importante!)
    if (empty($username) || empty($incident_type) || empty($description)) {
        $error = "Please fill out all required fields";
        // ... Mostrar mensaje de error al usuario ...
    } else {
        try {
            // 3. Construir la consulta INSERT
            $sql = "INSERT INTO incidents (username, user_email, incident_type, description, urgency)
                    VALUES (:username, :email, :type, :desc, :urg)";
            $stmt = $pdo->prepare($sql);

            // 4. Ejecutar la consulta
            $stmt->execute([
                ':username' => $username,
                ':email' => $user_email,
                ':type' => $incident_type,
                ':desc' => $description,
                ':urg' => $urgency,
            ]);

            // 5. Obtener el ID de la incidencia recién insertada
            $incident_id = $pdo->lastInsertId();

            // 6. Recuperar los datos de la incidencia
            $sql_select = "SELECT * FROM incidents WHERE id = :id";
            $stmt_select = $pdo->prepare($sql_select);
            $stmt_select->execute([':id' => $incident_id]);
            $newIncident = $stmt_select->fetch(PDO::FETCH_ASSOC);

            if ($newIncident) {
                // 7. Definir el destinatario
                $destinatarioEmail = 'soporte@tudominio.com'; // Reemplaza con la dirección de soporte
                $destinatarioNombre = 'Equipo de Soporte';

                // 8. Enviar el correo electrónico
                if (enviarCorreoIncidencia($destinatarioEmail, $destinatarioNombre, $nuevaIncidencia)) {
                    $mensajeExito = "Incidencia registrada correctamente. Se ha enviado una notificación al equipo de soporte.";
                    // ... Mostrar mensaje de éxito al usuario ...
                } else {
                    $mensajeExito = "Incidencia registrada correctamente, pero hubo un error al enviar la notificación.";
                    // ... Mostrar mensaje de éxito con advertencia al usuario ...
                }
            } else {
                $mensajeError = "Incidencia registrada, pero no se pudieron obtener los detalles para la notificación.";
                // ... Mostrar mensaje de error al usuario ...
            }

            // 9. Redirigir o mostrar mensaje de éxito
            // header("Location: pagina_de_confirmacion.php");
            // exit();

        } catch (PDOException $e) {
            $error = "Error al registrar la incidencia: " . $e->getMessage();
            // ... Mostrar mensaje de error de la base de datos al usuario ...
        }
    }
}

// ... (El resto de tu código HTML para el formulario) ...

?>
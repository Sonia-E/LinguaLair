<?php
namespace Sonia\LinguaLair\Models;

class IncidentsModel {
    private $conexion;

    public function __construct($servidor, $usuario, $contrasenia, $base_datos) {
        $this->conexion = new \mysqli($servidor, $usuario, $contrasenia, $base_datos);

        if ($this->conexion->connect_error) {
            die("Conexión fallida: " . $this->conexion->connect_error);
        } else {
            $this->conexion->set_charset("utf8");
        }
    }

    public function insertarIncidencia(int $userId, string $username, ?string $userEmail, string $incidentType, string $description, string $urgency = 'medium'): ?array {
        if (!$this->conexion) {
            error_log("No hay conexión a la base de datos.");
            return null; // Importante: retornar null en caso de error
        }

        try {
            // Preparar la consulta SQL para la inserción.
            $sql = "INSERT INTO incidents (user_id, username, user_email, incident_type, description, urgency)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->conexion->prepare($sql);

            if (!$stmt) {
                error_log("Error al preparar la consulta: " . $this->conexion->error);
                return null;
            }

            // Vincular los parámetros a la consulta preparada.
            $stmt->bind_param("isssss", $userId, $username, $userEmail, $incidentType, $description, $urgency);

            // Ejecutar la consulta.
            $stmt->execute();

            // Verificar si la inserción fue exitosa.
            if ($this->conexion->affected_rows <= 0) {
                error_log("No se pudo insertar la incidencia. Detalles: " . $this->conexion->error);
                $stmt->close();
                return null;
            }

            // Obtener el ID de la incidencia recién insertada.
        $incidentId = $this->conexion->insert_id;

        // Recuperar los datos de la incidencia insertada para retornar.
        $sql_select = "SELECT id, user_id, username, user_email, incident_type, description, urgency, creation_date, state FROM incidents WHERE id = ?";
        $stmt_select = $this->conexion->prepare($sql_select);

        if (!$stmt_select) {
            error_log("Error al preparar la consulta de selección: " . $this->conexion->error);
            $stmt->close();
            $this->conexion->rollback();
            return null;
        }
        $stmt_select->bind_param("i", $incidentId);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        $newIncident = $result->fetch_assoc();

        if (!$newIncident)
        {
             error_log("No se pudo obtener la incidencia insertada.");
             $stmt->close();
             $stmt_select->close();
             $this->conexion->rollback();
             return null;
        }

            // Cerrar las declaraciones preparadas.
            $stmt->close();
            $stmt_select->close();

            return $newIncident ?: null; // Retorna null si no se encuentra la incidencia.

        } catch (\Exception $e) {
            // Registrar el error en el log para su posterior análisis.
            error_log("Error al insertar la incidencia: " . $e->getMessage());
            return null; // Retornar null en caso de error
        }
    }



    public function insertarIncidencia2(int $userId, string $username, ?string $userEmail, string $incidentType, string $description, string $urgency = 'medium'): ?array {
    if (!$this->conexion) {
        error_log("No hay conexión a la base de datos.");
        return null;
    }

    try {
        $this->conexion->begin_transaction();

        // Preparar la consulta SQL para la inserción.
        $sql = "INSERT INTO incidents (user_id, username, user_email, incident_type, description, urgency, creation_date, state)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            error_log("Error al preparar la consulta de inserción: " . $this->conexion->error);
            $this->conexion->rollback();
            return null;
        }

        // Vincular los parámetros a la consulta preparada.
        $stmt->bind_param("issssss", $userId, $username, $userEmail, $incidentType, $description, $urgency,);

        // Ejecutar la consulta.
        $stmt->execute();

        // Verificar si la inserción fue exitosa.
        if ($this->conexion->affected_rows <= 0) {
            error_log("No se pudo insertar la incidencia. Detalles: " . $this->conexion->error);
            $stmt->close();
            $this->conexion->rollback();
            return null;
        }

        // Obtener el ID de la incidencia recién insertada.
        $incidentId = $this->conexion->insert_id;

        // Recuperar los datos de la incidencia insertada para retornar.
        $sql_select = "SELECT id, user_id, username, user_email, incident_type, description, urgency, creation_date, state FROM incidents WHERE id = ?";
        $stmt_select = $this->conexion->prepare($sql_select);

        if (!$stmt_select) {
            error_log("Error al preparar la consulta de selección: " . $this->conexion->error);
            $stmt->close();
            $this->conexion->rollback();
            return null;
        }
        $stmt_select->bind_param("i", $incidentId);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        $newIncident = $result->fetch_assoc();

        if (!$newIncident)
        {
             error_log("No se pudo obtener la incidencia insertada.");
             $stmt->close();
             $stmt_select->close();
             $this->conexion->rollback();
             return null;
        }

        // Cerrar las declaraciones preparadas.
        $stmt->close();
        $stmt_select->close();
        $this->conexion->commit();

        return $newIncident;

    } catch (\Exception $e) {
        // Registrar el error en el log para su posterior análisis.
        error_log("Error al insertar la incidencia: " . $e->getMessage());
        $this->conexion->rollback(); // Hacer rollback en caso de excepción
        return null;
    }
}

    public function __destruct() {
        if ($this->conexion) {
            $this->conexion->close();
        }
    }
}
<?php
    namespace Sonia\LinguaLair\Models;

    class PermissionsModel {
        private $conexion;
    
        public function __construct($servidor, $usuario, $contrasenia, $base_datos) {
            $this->conexion = new \mysqli($servidor, $usuario, $contrasenia, $base_datos);
    
            if ($this->conexion->connect_error) {
                die("Conexión fallida: " . $this->conexion->connect_error);
            } else {
                $this->conexion->set_charset("utf8");
            }
        }
    
        // Obtener el rol de un usuario
        public function getUserRole($userId) {
            if (!$this->conexion) return false;
            $sql = "SELECT r.name FROM user u
                    JOIN roles r ON u.role_id = r.id
                    WHERE u.id = ?";
            $stmt = $this->conexion->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    return $row['name'];
                }
                $stmt->close();
            }
            return false;
        }

        // Obtener todos los permisos de un usuario (a través de su rol)
        public function getUserPermissions($userId) {
            if (!$this->conexion) return false;
            $sql = "SELECT p.name FROM user u
                    JOIN roles r ON u.role_id = r.id
                    JOIN role_permissions rp ON r.id = rp.role_id
                    JOIN permissions p ON rp.permission_id = p.id
                    WHERE u.id = ?";
            $stmt = $this->conexion->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                $permissions = [];
                while ($row = $result->fetch_assoc()) {
                    $permissions[] = $row['name'];
                }
                $stmt->close();
                return $permissions;
            }
            return false;
        }
        
        // Método auxiliar para verificar si un usuario tiene un permiso específico
        public function hasPermission($userId, $permissionName) {
            $permissions = $this->getUserPermissions($userId);
            return in_array($permissionName, $permissions);
        }

        // Métodos para gestionar roles de usuario (para administradores)
        public function setUserRole($userId, $roleName) {
            if (!$this->conexion) return false;
            $sql = "UPDATE user SET role_id = (SELECT id FROM roles WHERE name = ?) WHERE id = ?";
            $stmt = $this->conexion->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("si", $roleName, $userId);
                $stmt->execute();
                $affectedRows = $stmt->affected_rows;
                $stmt->close();
                return $affectedRows > 0;
            }
            return false;
        }
        
        public function getAllRoles() {
            if (!$this->conexion) return false;
            $sql = "SELECT id, name, description FROM roles";
            $result = $this->conexion->query($sql);
            $roles = [];
            while ($row = $result->fetch_assoc()) {
                $roles[] = $row;
            }
            return $roles;
        }

        public function deleteUserAndRelatedData($userId) {
            if (!$this->conexion) return false;

            // Iniciar una transacción para asegurar la integridad de los datos
            $this->conexion->begin_transaction();
    
            try {
                $sqlDeleteUser = "DELETE FROM user WHERE id = ?";
                $stmtDeleteUser = $this->conexion->prepare($sqlDeleteUser);
                if (!$stmtDeleteUser) throw new \Exception("Error al preparar la consulta para borrar usuario: " . $this->conexion->error);
                $stmtDeleteUser->bind_param("i", $userId);
                $stmtDeleteUser->execute();
                $stmtDeleteUser->close();
    
                // Si todo fue exitoso, confirmar la transacción
                $this->conexion->commit();
                return true;
    
            } catch (\Exception $e) {
                // Si ocurrió algún error, deshacer la transacción
                $this->conexion->rollback();
                error_log("Error al eliminar usuario y sus datos: " . $e->getMessage());
                return false;
            }
        }

        public function banUser($userId) {
            if (!$this->conexion) return false;
            $sql = "UPDATE user SET banned = 1 WHERE id = ?"; // Asume una columna 'banned' (BOOLEAN o TINYINT)
            $stmt = $this->conexion->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $userId);
                return $stmt->execute();
            }
            return false;
        }

        public function editLog($log_id, $description, $language, $type, $duration, $log_date, $post_date) {
            if (!$this->conexion) return false;

            $consulta = "UPDATE logs SET description = ?, language = ?, type = ?, duration = ?, log_date = ?, post_date = ?
                        WHERE id = ?";

            $stmt = $this->conexion->prepare($consulta);

            if ($stmt) {
                $stmt->bind_param("ssssssi", $description, $language, $type, $duration, $log_date, $post_date, $log_id);
                if ($stmt->execute()) {
                    $stmt->close();
                    return true;
                } else {
                    echo "Error al editar log: " . $stmt->error;
                    $stmt->close();
                    return false;
                }
            } else {
                echo "Error al preparar la consulta de edición: " . $this->conexion->error;
                return false;
            }
        }
    
        public function __destruct() {
            if ($this->conexion) {
                $this->conexion->close();
            }
        }

    }
?>
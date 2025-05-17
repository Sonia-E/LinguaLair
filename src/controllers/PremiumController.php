<?php

namespace Sonia\LinguaLair\controllers;

class PremiumController {
    private $modelo;
    private $PermissionsModel;

    public function __construct($PermissionsModel, $modelo) {
        $this->PermissionsModel = $PermissionsModel;
        $this->modelo = $modelo;
    }

    public function processEditLogForm() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_log_identifier'])) {
            $description = $_POST["description"];
            $language = $_POST["language"];
            $type = $_POST["type"];
            $duration = $_POST["duration"];
            $log_date = $_POST["date"];
            $user_id = $_SESSION["user_id"];
            $logIdentifier = $_POST['edit_log_identifier'];
            $parts = explode('_', $logIdentifier);

            if (count($parts) >= 2) {
                $logIdToEdit = intval(array_pop($parts)); // Obtiene el último elemento como ID
                $username = implode('_', $parts);   // Une el resto como username

                // Verificar si el log existe y pertenece al usuario logueado
                $loggedInUsername = $_SESSION['username'] ?? null;
                $logToEdit = $this->modelo->findLogByUsernameAndId($username, $logIdToEdit);

                $post_date = $logToEdit['post_date'];

                if ($logToEdit && $loggedInUsername === $username && $this->PermissionsModel->hasPermission($user_id, 'edit_own_log')) {
                    if ($this->PermissionsModel->editLog($logIdToEdit, $description, $language, $type, $duration, $log_date, $post_date)) {
                        $response = ['success' => true, 'message' => 'Log editted successfully.'];
                    } else {
                        // El usuario tiene permiso para editar su propio log (admin y premium)
                        $response = ['success' => false, 'message' => 'Error editting log.'];
                    }
                } 
                elseif ($this->PermissionsModel->hasPermission($user_id, 'edit_any_log')) {
                    // El usuario tiene permiso para editar cualquier log (admin)
                    if ($this->PermissionsModel->editLog($logIdToEdit, $description, $language, $type, $duration, $log_date, $post_date)) {
                        $response = ['success' => true, 'message' => 'Log editted successfully.'];
                    } else {
                        $response = ['success' => false, 'message' => 'Error editting log.'];
                    }
                } 
                else {
                    $response = ['success' => false, 'message' => 'Log not found or does not belong to the current user or 
                    user does not have permission to edit any logs'];
                }
            } else {
                $response = ['success' => false, 'message' => 'Invalid log identifier.'];
            }

            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            // Manejar peticiones incorrectas
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        }
    }

    public function getLogData($logIdentifier) {
        $parts = explode('_', $logIdentifier);
        $logIdToEdit = intval(array_pop($parts)); // Obtiene el último elemento como ID
        $username = implode('_', $parts);   // Une el resto como username
        $logToEdit = $this->modelo->findLogByUsernameAndId($username, $logIdToEdit);
        return $logToEdit;
    }

    // Verificar permisos para eliminar o banear usuarios
    public function eliminarUsuario($userIdToDelete) {
        $currentUserId = $_SESSION['user_id'];
        if ($this->PermissionsModel->hasPermission($currentUserId, 'delete_user')) {
            // El administrador tiene permiso para eliminar usuarios
            if ($this->PermissionsModel->deleteUserAndRelatedData($userIdToDelete)) {
                // Éxito al eliminar el usuario y sus datos
                $_SESSION['mensaje'] = "Usuario eliminado con éxito.";
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                // Error al eliminar el usuario o sus datos
                $_SESSION['mensaje'] = "Error al eliminar el usuario de id: " . $userIdToDelete;
                $_SESSION['tipo_mensaje'] = 'error';
            }
        } else {
            // No tiene permiso
            $_SESSION['mensaje'] = "No tienes permiso para eliminar usuarios.";
            $_SESSION['tipo_mensaje'] = 'warning';
        }
    }
    
    // Ejemplo de método para banear un usuario (necesitarías una columna 'banned' en la tabla 'user')
    public function banearUsuario($userIdToBan) {
        $currentUserId = $_SESSION['user_id'];
        if ($this->PermissionsModel->hasPermission($currentUserId, 'ban_user')) {
            // El administrador tiene permiso para banear usuarios
            if ($this->PermissionsModel->banUser($userIdToBan)) {
                $_SESSION['mensaje'] = "Usuario baneado con éxito.";
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = "Error al banear el usuario.";
                $_SESSION['tipo_mensaje'] = 'error';
            }
        } else {
            $_SESSION['mensaje'] = "No tienes permiso para banear usuarios.";
            $_SESSION['tipo_mensaje'] = 'warning';
        }
        header('Location: /admin/usuarios'); // Ejemplo de redirección
        exit();
    }
}

?>
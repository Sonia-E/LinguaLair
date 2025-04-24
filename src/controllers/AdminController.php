<?php

// Asegúrate de que CON_CONTROLADOR esté definido si lo usas en otros archivos
// if (!defined('CON_CONTROLADOR')) die('Acceso no permitido.');

class AdminController {
    private $modelo;
    private $PermissionsModel;

    public function __construct($PermissionsModel) {
        $this->PermissionsModel = $PermissionsModel;
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
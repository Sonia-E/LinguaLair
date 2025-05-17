<?php

namespace Sonia\LinguaLair\controllers;

class LogController {
    private $modelo;
    private $PermissionsModel;

    public function __construct($modelo = null, $PermissionsModel = null) {
        $this->modelo = $modelo;
        $this->PermissionsModel = $PermissionsModel;
    }

    public function obtainExcessExperience($user_id, $experience_gain) {
        $excessExperience = $this->modelo->getExcessExperience($user_id, $experience_gain);
        return $excessExperience;
    }

    public function addExcessExperience($user_id, $excessExperience) {
        $this->modelo->addExperience($user_id, $excessExperience);
    }

    public function procesarFormulario() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Gather data from log form
            $description = $_POST["description"];
            $language = $_POST["language"];
            $type = $_POST["type"];
            $duration = $_POST["duration"];
            $log_date = $_POST["date"];
            $user_id = $_SESSION["user_id"];
            $antiguoRol = $this->modelo->getCurrentGameRole($user_id);

            // Save new log in database
            $log_guardado = $this->modelo->addLog($user_id, $description, $language, $type, $duration, $log_date);

            // Update experience and level
            if ($log_guardado) {
                $experience_gain = $duration;
                $this->modelo->addExperience($user_id, $experience_gain);

                $profile_data = $this->modelo->getProfileData($user_id);
                $nuevo_nivel = $profile_data->level;
                $nueva_experiencia = $profile_data->experience;

                if ($profile_data->experience >= 100) {
                    $this->modelo->levelUp($user_id);
                    $profile_data_updated = $this->modelo->getProfileData($user_id);
                    $nuevo_nivel = $profile_data_updated->level;
                    $nueva_experiencia = $profile_data_updated->experience;
                }

                // Verificar si se subió de nivel
                if ($nuevo_nivel) {
                    // Obtener el nuevo rol del usuario
                    $nuevoRol = $this->modelo->obtenerRolUsuario($user_id);
                
                    // Enviar la respuesta JSON con la información del nivel y el rol
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'nuevaExperiencia' => $nueva_experiencia,
                        'nuevoNivel' => $nuevo_nivel,
                        'nuevoRol' => $nuevoRol,
                        'antiguoRol' => $antiguoRol,
                    ]);
                    exit();

                } else {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'nuevaExperiencia' => $nueva_experiencia,
                        'nuevoNivel' => $nuevo_nivel,
                    ]);
                    exit();
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Error al guardar el log.']);
                exit();
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Acceso no permitido.']);
            exit();
        }
    }

    

    // // Verificar permisos para eliminar logs
    // public function eliminarLog($logId) {
    //     $userId = $_SESSION['user_id'];
    //     $log = $this->modelo->getLogById($logId); // Necesitas esta función en tu modelo
    
    //     if ($log) {
    //         if ($log['user_id'] == $userId && $this->PermissionsModel->hasPermission($userId, 'delete_own_log')) {
    //             // El usuario es el creador del log y tiene permiso para eliminar sus propios logs
    //             if ($this->modelo->deleteLog($logId)) {
    //                 // Éxito al eliminar
    //             } else {
    //                 // Error al eliminar
    //             }
    //         } elseif ($this->PermissionsModel->hasPermission($userId, 'delete_any_log')) {
    //             // El usuario tiene permiso para eliminar cualquier log (admin)
    //             if ($this->modelo->deleteLog($logId)) {
    //                 // Éxito al eliminar
    //             } else {
    //                 // Error al eliminar
    //             }
    //         } else {
    //             // El usuario no tiene permiso
    //             // Mostrar mensaje de error
    //         }
    //     } else {
    //         // Log no encontrado
    //     }
    //     // ... redireccionar o mostrar mensaje ...
    // }
    
    public function deleteUserLog($userId) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log_identifier'])) {
            $logIdentifier = $_POST['log_identifier'];
            $parts = explode('_', $logIdentifier);

            if (count($parts) >= 2) {
                $logIdToDelete = intval(array_pop($parts)); // Obtiene el último elemento como ID
                $username = implode('_', $parts);   // Une el resto como username

                // Verificar si el log existe y pertenece al usuario logueado (opcional pero recomendado)
                $loggedInUsername = $_SESSION['username'] ?? null;
                $logToDelete = $this->modelo->findLogByUsernameAndId($username, $logIdToDelete);

                if ($logToDelete && $loggedInUsername === $username) {
                    if ($this->modelo->deleteLogById($logIdToDelete)) {
                        $response = ['success' => true, 'message' => 'Log deleted successfully.'];
                    } else {
                        $response = ['success' => false, 'message' => 'Error deleting log.'];
                    }
                } elseif ($this->PermissionsModel->hasPermission($userId, 'delete_any_log')) {
                    // El usuario tiene permiso para eliminar cualquier log (admin)
                    if ($this->modelo->deleteLogById($logIdToDelete)) {
                        $response = ['success' => true, 'message' => 'Log deleted successfully.'];
                    } else {
                        $response = ['success' => false, 'message' => 'Error deleting log.'];
                    }
                } else {
                    $response = ['success' => false, 'message' => 'Log not found or does not belong to the current user or 
                    user does not have permission to delete other user\'s logs'];
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
}

?>
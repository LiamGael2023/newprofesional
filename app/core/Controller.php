<?php
/**
 * Clase Controller Base
 * Proporciona métodos comunes para todos los controladores
 */
class Controller {

    /**
     * Cargar vista
     */
    protected function view($view, $data = []) {
        extract($data);

        $viewFile = APP_PATH . '/views/' . $view . '.php';

        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("Vista no encontrada: {$view}");
        }
    }

    /**
     * Cargar modelo
     */
    protected function model($model) {
        $modelFile = APP_PATH . '/models/' . $model . '.php';

        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        } else {
            die("Modelo no encontrado: {$model}");
        }
    }

    /**
     * Redireccionar
     */
    protected function redirect($url) {
        header('Location: ' . APP_URL . '/' . $url);
        exit;
    }

    /**
     * Respuesta JSON
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Verificar si está autenticado
     */
    protected function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Requerir autenticación
     */
    protected function requireAuth() {
        if (!$this->isAuthenticated()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            $this->redirect('auth/login');
        }
    }

    /**
     * Verificar permisos
     */
    protected function hasPermission($permission) {
        if (!isset($_SESSION['permisos'])) {
            return false;
        }

        $permisos = $_SESSION['permisos'];

        // Administrador tiene todos los permisos
        if (in_array('all', $permisos)) {
            return true;
        }

        return in_array($permission, $permisos);
    }

    /**
     * Requerir permiso
     */
    protected function requirePermission($permission) {
        $this->requireAuth();

        if (!$this->hasPermission($permission)) {
            $this->view('errors/403', [
                'message' => 'No tienes permisos para acceder a esta sección'
            ]);
            exit;
        }
    }

    /**
     * Obtener datos POST
     */
    protected function post($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }

    /**
     * Obtener datos GET
     */
    protected function get($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    /**
     * Validar datos
     */
    protected function validate($data, $rules) {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $rulesList = explode('|', $rule);

            foreach ($rulesList as $r) {
                if ($r === 'required' && empty($data[$field])) {
                    $errors[$field][] = "El campo {$field} es obligatorio";
                }

                if (strpos($r, 'min:') === 0) {
                    $min = (int) substr($r, 4);
                    if (strlen($data[$field]) < $min) {
                        $errors[$field][] = "El campo {$field} debe tener al menos {$min} caracteres";
                    }
                }

                if (strpos($r, 'max:') === 0) {
                    $max = (int) substr($r, 4);
                    if (strlen($data[$field]) > $max) {
                        $errors[$field][] = "El campo {$field} no debe exceder {$max} caracteres";
                    }
                }

                if ($r === 'email' && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "El campo {$field} debe ser un email válido";
                }

                if ($r === 'numeric' && !is_numeric($data[$field])) {
                    $errors[$field][] = "El campo {$field} debe ser numérico";
                }
            }
        }

        return $errors;
    }

    /**
     * Mensaje flash
     */
    protected function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    /**
     * Obtener mensaje flash
     */
    protected function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}

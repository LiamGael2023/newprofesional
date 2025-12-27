<?php
/**
 * Controlador de Autenticación
 */
class AuthController extends Controller {

    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = $this->model('Usuario');
    }

    /**
     * Mostrar formulario de login
     */
    public function login() {
        // Si ya está autenticado, redirigir al dashboard
        if ($this->isAuthenticated()) {
            $this->redirect('dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $this->post('username');
            $password = $this->post('password');

            // Validar
            $errors = $this->validate($_POST, [
                'username' => 'required',
                'password' => 'required'
            ]);

            if (empty($errors)) {
                // Autenticar
                $user = $this->usuarioModel->authenticate($username, $password);

                if ($user) {
                    // Crear sesión
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['rol_id'] = $user['rol_id'];
                    $_SESSION['rol_nombre'] = $user['rol_nombre'];
                    $_SESSION['permisos'] = json_decode($user['permisos'], true);

                    // Registrar en auditoría
                    logAudit('usuarios', $user['id'], 'LOGIN');

                    // Redirigir
                    if (isset($_SESSION['redirect_after_login'])) {
                        $redirect = $_SESSION['redirect_after_login'];
                        unset($_SESSION['redirect_after_login']);
                        header('Location: ' . $redirect);
                    } else {
                        $this->redirect('dashboard');
                    }
                } else {
                    $errors['login'] = ['Credenciales incorrectas'];
                }
            }

            $this->view('auth/login', [
                'errors' => $errors,
                'username' => $username
            ]);
        } else {
            $this->view('auth/login');
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout() {
        if ($this->isAuthenticated()) {
            logAudit('usuarios', $_SESSION['user_id'], 'LOGOUT');
        }

        session_destroy();
        $this->redirect('auth/login');
    }

    /**
     * Perfil de usuario
     */
    public function profile() {
        $this->requireAuth();

        $user = $this->usuarioModel->find($_SESSION['user_id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'email' => $this->post('email')
            ];

            // Si se proporciona nueva contraseña
            if (!empty($this->post('new_password'))) {
                $currentPassword = $this->post('current_password');
                $newPassword = $this->post('new_password');
                $confirmPassword = $this->post('confirm_password');

                // Verificar contraseña actual
                if (!password_verify($currentPassword, $user['password'])) {
                    $this->setFlash('error', 'Contraseña actual incorrecta');
                } elseif ($newPassword !== $confirmPassword) {
                    $this->setFlash('error', 'Las contraseñas nuevas no coinciden');
                } elseif (strlen($newPassword) < 6) {
                    $this->setFlash('error', 'La contraseña debe tener al menos 6 caracteres');
                } else {
                    $this->usuarioModel->updatePassword($_SESSION['user_id'], $newPassword);
                    $this->setFlash('success', 'Contraseña actualizada correctamente');
                }
            }

            // Actualizar email
            if ($data['email'] !== $user['email']) {
                if ($this->usuarioModel->emailExists($data['email'], $_SESSION['user_id'])) {
                    $this->setFlash('error', 'El email ya está en uso');
                } else {
                    $this->usuarioModel->update($_SESSION['user_id'], $data);
                    $_SESSION['email'] = $data['email'];
                    $this->setFlash('success', 'Perfil actualizado correctamente');
                }
            }

            $this->redirect('auth/profile');
        }

        $this->view('auth/profile', [
            'user' => $user
        ]);
    }
}

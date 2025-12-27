<?php
/**
 * Modelo Usuario
 * Gestiona la autenticación y usuarios del sistema
 */
class Usuario extends Model {
    protected $table = 'usuarios';

    /**
     * Autenticar usuario
     */
    public function authenticate($username, $password) {
        $sql = "SELECT u.*, r.nombre as rol_nombre, r.permisos
                FROM usuarios u
                INNER JOIN roles r ON u.rol_id = r.id
                WHERE u.username = :username AND u.activo = 1
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Actualizar último acceso
            $this->update($user['id'], ['ultimo_acceso' => date('Y-m-d H:i:s')]);

            return $user;
        }

        return false;
    }

    /**
     * Crear usuario
     */
    public function createUser($data) {
        $data['password'] = password_hash($data['password'], HASH_ALGO, ['cost' => HASH_COST]);
        return $this->insert($data);
    }

    /**
     * Actualizar contraseña
     */
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, HASH_ALGO, ['cost' => HASH_COST]);
        return $this->update($userId, ['password' => $hashedPassword]);
    }

    /**
     * Verificar si existe username
     */
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM usuarios WHERE username = :username";
        if ($excludeId) {
            $sql .= " AND id != :id";
        }

        $stmt = $this->db->prepare($sql);
        $params = ['username' => $username];
        if ($excludeId) {
            $params['id'] = $excludeId;
        }

        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    /**
     * Verificar si existe email
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM usuarios WHERE email = :email";
        if ($excludeId) {
            $sql .= " AND id != :id";
        }

        $stmt = $this->db->prepare($sql);
        $params = ['email' => $email];
        if ($excludeId) {
            $params['id'] = $excludeId;
        }

        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    /**
     * Obtener usuarios con sus roles
     */
    public function getAllWithRoles() {
        $sql = "SELECT u.*, r.nombre as rol_nombre
                FROM usuarios u
                INNER JOIN roles r ON u.rol_id = r.id
                ORDER BY u.created_at DESC";

        return $this->query($sql);
    }

    /**
     * Cambiar estado de usuario
     */
    public function toggleStatus($userId) {
        $user = $this->find($userId);
        if ($user) {
            $newStatus = $user['activo'] ? 0 : 1;
            return $this->update($userId, ['activo' => $newStatus]);
        }
        return false;
    }
}

<?php
/**
 * Funciones Helper del Sistema
 */

/**
 * Escapar HTML
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Generar URL
 */
function url($path = '') {
    return APP_URL . '/' . ltrim($path, '/');
}

/**
 * Generar URL de asset
 */
function asset($path) {
    return APP_URL . '/public/' . ltrim($path, '/');
}

/**
 * Redireccionar
 */
function redirect($url) {
    header('Location: ' . url($url));
    exit;
}

/**
 * Formatear fecha
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

/**
 * Formatear fecha y hora
 */
function formatDateTime($datetime, $format = 'd/m/Y H:i') {
    if (empty($datetime)) return '';
    return date($format, strtotime($datetime));
}

/**
 * Formatear moneda
 */
function formatMoney($amount, $currency = 'S/') {
    return $currency . ' ' . number_format($amount, 2, '.', ',');
}

/**
 * Obtener usuario actual
 */
function currentUser() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Obtener nombre de usuario actual
 */
function currentUsername() {
    return $_SESSION['username'] ?? 'Invitado';
}

/**
 * Verificar si está autenticado
 */
function isAuth() {
    return isset($_SESSION['user_id']);
}

/**
 * Verificar permisos
 */
function hasPermission($permission) {
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
 * Generar token CSRF
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verificar token CSRF
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Limpiar input
 */
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Generar código único
 */
function generateCode($prefix = '', $length = 8) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $prefix . $code;
}

/**
 * Obtener mes en español
 */
function getMonthName($month) {
    $months = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    return $months[(int)$month] ?? '';
}

/**
 * Obtener periodo actual
 */
function getCurrentPeriod() {
    return date('Y-m');
}

/**
 * Registrar en log
 */
function logMessage($message, $level = 'INFO') {
    $logFile = ROOT_PATH . '/logs/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

/**
 * Registrar auditoría
 */
function logAudit($tabla, $registro_id, $accion, $datos_anteriores = null, $datos_nuevos = null) {
    try {
        $db = Database::getInstance()->getConnection();

        $sql = "INSERT INTO auditoria (tabla, registro_id, accion, usuario_id, datos_anteriores, datos_nuevos, ip_address, user_agent)
                VALUES (:tabla, :registro_id, :accion, :usuario_id, :datos_anteriores, :datos_nuevos, :ip, :user_agent)";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            'tabla' => $tabla,
            'registro_id' => $registro_id,
            'accion' => $accion,
            'usuario_id' => currentUser(),
            'datos_anteriores' => $datos_anteriores ? json_encode($datos_anteriores) : null,
            'datos_nuevos' => $datos_nuevos ? json_encode($datos_nuevos) : null,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        logMessage('Error en auditoría: ' . $e->getMessage(), 'ERROR');
    }
}

/**
 * Validar DNI peruano
 */
function isValidDNI($dni) {
    return preg_match('/^[0-9]{8}$/', $dni);
}

/**
 * Validar RUC peruano
 */
function isValidRUC($ruc) {
    return preg_match('/^[0-9]{11}$/', $ruc);
}

/**
 * Obtener edad
 */
function getAge($birthDate) {
    $birth = new DateTime($birthDate);
    $today = new DateTime();
    return $birth->diff($today)->y;
}

/**
 * Generar número de recibo
 */
function generateReciboNumber() {
    return 'REC-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

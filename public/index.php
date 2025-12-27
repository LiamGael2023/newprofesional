<?php
/**
 * Punto de Entrada del Sistema
 * Sistema de Gestión de Colegio Profesional
 */

// Iniciar sesión
session_start();

// Cargar configuración
require_once __DIR__ . '/../config/config.php';

// Cargar helpers
require_once APP_PATH . '/helpers/functions.php';

// Iniciar el enrutador
new Router();

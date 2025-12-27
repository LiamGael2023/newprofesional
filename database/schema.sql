-- ============================================
-- Sistema de Gestión de Colegio Profesional
-- Base de Datos MySQL 8.0
-- ============================================

CREATE DATABASE IF NOT EXISTS colegio_profesional CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE colegio_profesional;

-- Tabla de Roles
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    permisos JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de Usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol_id INT NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    ultimo_acceso TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
) ENGINE=InnoDB;

-- Tabla de Personas
CREATE TABLE personas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_documento ENUM('DNI', 'PASAPORTE', 'CE') NOT NULL,
    numero_documento VARCHAR(20) NOT NULL UNIQUE,
    nombres VARCHAR(100) NOT NULL,
    apellido_paterno VARCHAR(100) NOT NULL,
    apellido_materno VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    genero ENUM('M', 'F', 'OTRO') NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    celular VARCHAR(20),
    direccion TEXT,
    distrito VARCHAR(100),
    provincia VARCHAR(100),
    departamento VARCHAR(100),
    pais VARCHAR(100) DEFAULT 'Perú',
    foto VARCHAR(255),
    estado ENUM('ACTIVO', 'INACTIVO') DEFAULT 'ACTIVO',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_documento (numero_documento),
    INDEX idx_nombres (nombres, apellido_paterno, apellido_materno)
) ENGINE=InnoDB;

-- Tabla de Colegiados
CREATE TABLE colegiados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    persona_id INT NOT NULL,
    codigo_colegiado VARCHAR(20) NOT NULL UNIQUE,
    especialidad VARCHAR(100) NOT NULL,
    universidad VARCHAR(200) NOT NULL,
    fecha_graduacion DATE NOT NULL,
    fecha_colegiatura DATE NOT NULL,
    numero_titulo VARCHAR(50),
    tipo_colegiatura ENUM('ORDINARIO', 'VITALICIO', 'HONORARIO') DEFAULT 'ORDINARIO',
    estado ENUM('ACTIVO', 'SUSPENDIDO', 'RETIRADO', 'INHABILITADO') DEFAULT 'ACTIVO',
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (persona_id) REFERENCES personas(id) ON DELETE RESTRICT,
    INDEX idx_codigo (codigo_colegiado),
    INDEX idx_estado (estado)
) ENGINE=InnoDB;

-- Tabla de Tipos de Aportación
CREATE TABLE tipos_aportacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    monto_base DECIMAL(10, 2) NOT NULL,
    periodicidad ENUM('MENSUAL', 'TRIMESTRAL', 'SEMESTRAL', 'ANUAL', 'UNICO') DEFAULT 'MENSUAL',
    obligatorio TINYINT(1) DEFAULT 1,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de Aportaciones/Cuotas
CREATE TABLE aportaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    colegiado_id INT NOT NULL,
    tipo_aportacion_id INT NOT NULL,
    periodo VARCHAR(7) NOT NULL COMMENT 'Formato: YYYY-MM',
    monto DECIMAL(10, 2) NOT NULL,
    mora DECIMAL(10, 2) DEFAULT 0.00,
    descuento DECIMAL(10, 2) DEFAULT 0.00,
    monto_total DECIMAL(10, 2) NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    estado ENUM('PENDIENTE', 'PAGADO', 'VENCIDO', 'ANULADO') DEFAULT 'PENDIENTE',
    fecha_generacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (colegiado_id) REFERENCES colegiados(id) ON DELETE RESTRICT,
    FOREIGN KEY (tipo_aportacion_id) REFERENCES tipos_aportacion(id),
    UNIQUE KEY unique_aportacion (colegiado_id, tipo_aportacion_id, periodo),
    INDEX idx_periodo (periodo),
    INDEX idx_estado (estado),
    INDEX idx_vencimiento (fecha_vencimiento)
) ENGINE=InnoDB;

-- Tabla de Caja (Pagos)
CREATE TABLE pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    colegiado_id INT NOT NULL,
    numero_recibo VARCHAR(20) NOT NULL UNIQUE,
    fecha_pago DATETIME NOT NULL,
    monto_total DECIMAL(10, 2) NOT NULL,
    metodo_pago ENUM('EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'DEPOSITO', 'YAPE', 'PLIN') NOT NULL,
    numero_operacion VARCHAR(50),
    usuario_id INT NOT NULL,
    observaciones TEXT,
    estado ENUM('PROCESADO', 'ANULADO') DEFAULT 'PROCESADO',
    fecha_anulacion DATETIME NULL,
    motivo_anulacion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (colegiado_id) REFERENCES colegiados(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    INDEX idx_fecha (fecha_pago),
    INDEX idx_recibo (numero_recibo)
) ENGINE=InnoDB;

-- Tabla de Detalle de Pagos
CREATE TABLE detalle_pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pago_id INT NOT NULL,
    aportacion_id INT NOT NULL,
    monto_pagado DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pago_id) REFERENCES pagos(id) ON DELETE CASCADE,
    FOREIGN KEY (aportacion_id) REFERENCES aportaciones(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Tabla de Auditoría
CREATE TABLE auditoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tabla VARCHAR(50) NOT NULL,
    registro_id INT NOT NULL,
    accion ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    usuario_id INT,
    datos_anteriores JSON,
    datos_nuevos JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_tabla (tabla, registro_id),
    INDEX idx_fecha (created_at),
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB;

-- Tabla de Configuración del Sistema
CREATE TABLE configuracion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) NOT NULL UNIQUE,
    valor TEXT,
    descripcion TEXT,
    tipo ENUM('STRING', 'NUMBER', 'BOOLEAN', 'JSON') DEFAULT 'STRING',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- DATOS INICIALES
-- ============================================

-- Insertar roles por defecto
INSERT INTO roles (nombre, descripcion, permisos) VALUES
('ADMINISTRADOR', 'Acceso total al sistema', '["all"]'),
('CONTADOR', 'Gestión de caja y reportes financieros', '["caja", "reportes", "aportaciones"]'),
('SECRETARIO', 'Gestión de personas y colegiados', '["personas", "colegiados", "reportes"]'),
('CONSULTA', 'Solo lectura', '["view"]');

-- Insertar usuario administrador por defecto
-- Password: admin123 (hash bcrypt)
INSERT INTO usuarios (username, email, password, rol_id) VALUES
('admin', 'admin@colegio.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Insertar tipos de aportación por defecto
INSERT INTO tipos_aportacion (nombre, descripcion, monto_base, periodicidad, obligatorio) VALUES
('Cuota Ordinaria', 'Cuota mensual obligatoria para colegiados activos', 50.00, 'MENSUAL', 1),
('Derecho de Incorporación', 'Pago único al momento de la colegiatura', 300.00, 'UNICO', 1),
('Certificado de Habilidad', 'Emisión de certificado de habilidad', 80.00, 'UNICO', 0);

-- Insertar configuraciones del sistema
INSERT INTO configuracion (clave, valor, descripcion, tipo) VALUES
('nombre_colegio', 'Colegio Profesional', 'Nombre del colegio', 'STRING'),
('ruc', '20123456789', 'RUC del colegio', 'STRING'),
('direccion', 'Av. Principal 123, Lima', 'Dirección principal', 'STRING'),
('telefono', '01-1234567', 'Teléfono principal', 'STRING'),
('email', 'contacto@colegio.com', 'Email principal', 'STRING'),
('mora_diaria', '2.00', 'Monto de mora diaria por aportación vencida', 'NUMBER'),
('dias_vencimiento', '30', 'Días para el vencimiento de aportaciones', 'NUMBER'),
('generar_cuotas_auto', 'true', 'Generar cuotas automáticamente cada mes', 'BOOLEAN'),
('moneda', 'PEN', 'Moneda del sistema', 'STRING');

-- ============================================
-- TRIGGERS PARA AUDITORÍA
-- ============================================

DELIMITER $$

-- Trigger para auditar INSERT en personas
CREATE TRIGGER audit_personas_insert AFTER INSERT ON personas
FOR EACH ROW
BEGIN
    INSERT INTO auditoria (tabla, registro_id, accion, datos_nuevos)
    VALUES ('personas', NEW.id, 'INSERT', JSON_OBJECT(
        'numero_documento', NEW.numero_documento,
        'nombres', NEW.nombres,
        'apellido_paterno', NEW.apellido_paterno,
        'apellido_materno', NEW.apellido_materno
    ));
END$$

-- Trigger para auditar UPDATE en personas
CREATE TRIGGER audit_personas_update AFTER UPDATE ON personas
FOR EACH ROW
BEGIN
    INSERT INTO auditoria (tabla, registro_id, accion, datos_anteriores, datos_nuevos)
    VALUES ('personas', NEW.id, 'UPDATE',
        JSON_OBJECT('numero_documento', OLD.numero_documento, 'nombres', OLD.nombres),
        JSON_OBJECT('numero_documento', NEW.numero_documento, 'nombres', NEW.nombres)
    );
END$$

-- Trigger para auditar cambios de estado en aportaciones
CREATE TRIGGER audit_aportaciones_update AFTER UPDATE ON aportaciones
FOR EACH ROW
BEGIN
    IF OLD.estado != NEW.estado THEN
        INSERT INTO auditoria (tabla, registro_id, accion, datos_anteriores, datos_nuevos)
        VALUES ('aportaciones', NEW.id, 'UPDATE',
            JSON_OBJECT('estado', OLD.estado, 'monto_total', OLD.monto_total),
            JSON_OBJECT('estado', NEW.estado, 'monto_total', NEW.monto_total)
        );
    END IF;
END$$

-- Trigger para actualizar estado de aportación al registrar pago
CREATE TRIGGER update_aportacion_on_payment AFTER INSERT ON detalle_pagos
FOR EACH ROW
BEGIN
    UPDATE aportaciones SET estado = 'PAGADO' WHERE id = NEW.aportacion_id;
END$$

DELIMITER ;

-- ============================================
-- VISTAS ÚTILES
-- ============================================

-- Vista de colegiados con datos completos
CREATE VIEW v_colegiados_completo AS
SELECT
    c.id,
    c.codigo_colegiado,
    c.especialidad,
    c.fecha_colegiatura,
    c.estado AS estado_colegiatura,
    p.numero_documento,
    CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', p.apellido_materno) AS nombre_completo,
    p.email,
    p.celular,
    c.tipo_colegiatura
FROM colegiados c
INNER JOIN personas p ON c.persona_id = p.id;

-- Vista de aportaciones pendientes
CREATE VIEW v_aportaciones_pendientes AS
SELECT
    a.id,
    c.codigo_colegiado,
    CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', p.apellido_materno) AS nombre_completo,
    ta.nombre AS tipo_aportacion,
    a.periodo,
    a.monto_total,
    a.fecha_vencimiento,
    DATEDIFF(CURDATE(), a.fecha_vencimiento) AS dias_vencidos,
    a.estado
FROM aportaciones a
INNER JOIN colegiados c ON a.colegiado_id = c.id
INNER JOIN personas p ON c.persona_id = p.id
INNER JOIN tipos_aportacion ta ON a.tipo_aportacion_id = ta.id
WHERE a.estado IN ('PENDIENTE', 'VENCIDO');

-- Vista de resumen de caja diaria
CREATE VIEW v_resumen_caja_diaria AS
SELECT
    DATE(fecha_pago) AS fecha,
    metodo_pago,
    COUNT(*) AS cantidad_pagos,
    SUM(monto_total) AS total_recaudado
FROM pagos
WHERE estado = 'PROCESADO'
GROUP BY DATE(fecha_pago), metodo_pago;

-- ============================================
-- STORED PROCEDURES
-- ============================================

DELIMITER $$

-- Procedimiento para generar cuotas mensuales automáticamente
CREATE PROCEDURE sp_generar_cuotas_mensuales(IN p_periodo VARCHAR(7))
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_colegiado_id INT;
    DECLARE v_tipo_id INT;
    DECLARE v_monto DECIMAL(10,2);
    DECLARE v_dias_vencimiento INT;

    DECLARE cur_colegiados CURSOR FOR
        SELECT id FROM colegiados WHERE estado = 'ACTIVO';
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Obtener configuración
    SELECT CAST(valor AS UNSIGNED) INTO v_dias_vencimiento
    FROM configuracion WHERE clave = 'dias_vencimiento';

    -- Obtener tipo de aportación mensual
    SELECT id, monto_base INTO v_tipo_id, v_monto
    FROM tipos_aportacion
    WHERE periodicidad = 'MENSUAL' AND obligatorio = 1
    LIMIT 1;

    OPEN cur_colegiados;

    read_loop: LOOP
        FETCH cur_colegiados INTO v_colegiado_id;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Insertar aportación si no existe
        INSERT IGNORE INTO aportaciones (
            colegiado_id, tipo_aportacion_id, periodo, monto, monto_total, fecha_vencimiento
        ) VALUES (
            v_colegiado_id,
            v_tipo_id,
            p_periodo,
            v_monto,
            v_monto,
            DATE_ADD(CONCAT(p_periodo, '-01'), INTERVAL v_dias_vencimiento DAY)
        );
    END LOOP;

    CLOSE cur_colegiados;
END$$

-- Procedimiento para calcular mora
CREATE PROCEDURE sp_calcular_mora()
BEGIN
    DECLARE v_mora_diaria DECIMAL(10,2);

    SELECT CAST(valor AS DECIMAL(10,2)) INTO v_mora_diaria
    FROM configuracion WHERE clave = 'mora_diaria';

    UPDATE aportaciones
    SET
        mora = v_mora_diaria * GREATEST(0, DATEDIFF(CURDATE(), fecha_vencimiento)),
        monto_total = monto + mora,
        estado = CASE
            WHEN CURDATE() > fecha_vencimiento AND estado = 'PENDIENTE' THEN 'VENCIDO'
            ELSE estado
        END
    WHERE estado IN ('PENDIENTE', 'VENCIDO');
END$$

DELIMITER ;

<?php
/**
 * Modelo Pago
 * Gestiona los pagos registrados en caja
 */
class Pago extends Model {
    protected $table = 'pagos';

    /**
     * Obtener todos los pagos con datos relacionados
     */
    public function getAllWithRelations() {
        $sql = "SELECT p.*,
                c.codigo_colegiado,
                CONCAT(per.nombres, ' ', per.apellido_paterno, ' ', per.apellido_materno) as nombre_completo,
                u.username as usuario_nombre
                FROM pagos p
                INNER JOIN colegiados c ON p.colegiado_id = c.id
                INNER JOIN personas per ON c.persona_id = per.id
                INNER JOIN usuarios u ON p.usuario_id = u.id
                ORDER BY p.fecha_pago DESC";

        return $this->query($sql);
    }

    /**
     * Buscar pagos con filtros
     */
    public function search($filters = []) {
        $sql = "SELECT p.*,
                c.codigo_colegiado,
                CONCAT(per.nombres, ' ', per.apellido_paterno, ' ', per.apellido_materno) as nombre_completo,
                u.username as usuario_nombre
                FROM pagos p
                INNER JOIN colegiados c ON p.colegiado_id = c.id
                INNER JOIN personas per ON c.persona_id = per.id
                INNER JOIN usuarios u ON p.usuario_id = u.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['numero_recibo'])) {
            $sql .= " AND p.numero_recibo LIKE :recibo";
            $params['recibo'] = '%' . $filters['numero_recibo'] . '%';
        }

        if (!empty($filters['codigo_colegiado'])) {
            $sql .= " AND c.codigo_colegiado LIKE :codigo";
            $params['codigo'] = '%' . $filters['codigo_colegiado'] . '%';
        }

        if (!empty($filters['fecha_desde'])) {
            $sql .= " AND DATE(p.fecha_pago) >= :fecha_desde";
            $params['fecha_desde'] = $filters['fecha_desde'];
        }

        if (!empty($filters['fecha_hasta'])) {
            $sql .= " AND DATE(p.fecha_pago) <= :fecha_hasta";
            $params['fecha_hasta'] = $filters['fecha_hasta'];
        }

        if (!empty($filters['metodo_pago'])) {
            $sql .= " AND p.metodo_pago = :metodo";
            $params['metodo'] = $filters['metodo_pago'];
        }

        if (!empty($filters['estado'])) {
            $sql .= " AND p.estado = :estado";
            $params['estado'] = $filters['estado'];
        }

        $sql .= " ORDER BY p.fecha_pago DESC";

        return $this->query($sql, $params);
    }

    /**
     * Obtener detalle de un pago
     */
    public function getDetalle($pagoId) {
        $sql = "SELECT dp.*, a.periodo, ta.nombre as tipo_aportacion
                FROM detalle_pagos dp
                INNER JOIN aportaciones a ON dp.aportacion_id = a.id
                INNER JOIN tipos_aportacion ta ON a.tipo_aportacion_id = ta.id
                WHERE dp.pago_id = :id";

        return $this->query($sql, ['id' => $pagoId]);
    }

    /**
     * Registrar pago completo
     */
    public function registrarPago($dataPago, $aportaciones) {
        $this->beginTransaction();

        try {
            // Insertar pago
            $pagoId = $this->insert($dataPago);

            if (!$pagoId) {
                throw new Exception('Error al insertar el pago');
            }

            // Insertar detalles y actualizar aportaciones
            foreach ($aportaciones as $aportacionId => $monto) {
                // Insertar detalle
                $this->execute(
                    "INSERT INTO detalle_pagos (pago_id, aportacion_id, monto_pagado) VALUES (?, ?, ?)",
                    [$pagoId, $aportacionId, $monto]
                );

                // Actualizar estado de aportación
                $this->execute(
                    "UPDATE aportaciones SET estado = 'PAGADO' WHERE id = ?",
                    [$aportacionId]
                );
            }

            $this->commit();
            return $pagoId;

        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Anular pago
     */
    public function anularPago($pagoId, $motivo) {
        $this->beginTransaction();

        try {
            // Obtener aportaciones del pago
            $detalles = $this->getDetalle($pagoId);

            // Actualizar estado de las aportaciones a PENDIENTE
            foreach ($detalles as $detalle) {
                $this->execute(
                    "UPDATE aportaciones SET estado = 'PENDIENTE' WHERE id = ?",
                    [$detalle['aportacion_id']]
                );
            }

            // Actualizar estado del pago
            $this->execute(
                "UPDATE pagos SET estado = 'ANULADO', fecha_anulacion = NOW(), motivo_anulacion = ? WHERE id = ?",
                [$motivo, $pagoId]
            );

            $this->commit();
            return true;

        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Obtener totales por método de pago
     */
    public function getTotalesPorMetodo($fechaDesde = null, $fechaHasta = null) {
        $sql = "SELECT metodo_pago, COUNT(*) as cantidad, SUM(monto_total) as total
                FROM pagos
                WHERE estado = 'PROCESADO'";

        $params = [];

        if ($fechaDesde) {
            $sql .= " AND DATE(fecha_pago) >= :fecha_desde";
            $params['fecha_desde'] = $fechaDesde;
        }

        if ($fechaHasta) {
            $sql .= " AND DATE(fecha_pago) <= :fecha_hasta";
            $params['fecha_hasta'] = $fechaHasta;
        }

        $sql .= " GROUP BY metodo_pago";

        return $this->query($sql, $params);
    }

    /**
     * Obtener recaudación por periodo
     */
    public function getRecaudacionPorPeriodo($year, $month = null) {
        $sql = "SELECT
                DATE(fecha_pago) as fecha,
                COUNT(*) as cantidad_pagos,
                SUM(monto_total) as total_recaudado
                FROM pagos
                WHERE estado = 'PROCESADO' AND YEAR(fecha_pago) = :year";

        $params = ['year' => $year];

        if ($month) {
            $sql .= " AND MONTH(fecha_pago) = :month";
            $params['month'] = $month;
        }

        $sql .= " GROUP BY DATE(fecha_pago) ORDER BY fecha DESC";

        return $this->query($sql, $params);
    }
}

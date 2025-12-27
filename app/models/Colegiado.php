<?php
/**
 * Modelo Colegiado
 * Gestiona los colegiados del sistema
 */
class Colegiado extends Model {
    protected $table = 'colegiados';

    /**
     * Obtener todos los colegiados con datos de persona
     */
    public function getAllWithPersona() {
        $sql = "SELECT c.*,
                CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', p.apellido_materno) as nombre_completo,
                p.numero_documento, p.email, p.celular
                FROM colegiados c
                INNER JOIN personas p ON c.persona_id = p.id
                ORDER BY c.created_at DESC";

        return $this->query($sql);
    }

    /**
     * Obtener colegiado con datos de persona
     */
    public function getWithPersona($id) {
        $sql = "SELECT c.*, p.*,
                c.id as colegiado_id,
                CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', p.apellido_materno) as nombre_completo
                FROM colegiados c
                INNER JOIN personas p ON c.persona_id = p.id
                WHERE c.id = :id
                LIMIT 1";

        $result = $this->query($sql, ['id' => $id]);
        return $result[0] ?? null;
    }

    /**
     * Buscar por código
     */
    public function findByCodigo($codigo) {
        return $this->findWhere('codigo_colegiado', $codigo);
    }

    /**
     * Verificar si existe código
     */
    public function codigoExists($codigo, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM colegiados WHERE codigo_colegiado = :codigo";
        if ($excludeId) {
            $sql .= " AND id != :id";
        }

        $stmt = $this->db->prepare($sql);
        $params = ['codigo' => $codigo];
        if ($excludeId) {
            $params['id'] = $excludeId;
        }

        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    /**
     * Generar código de colegiado
     */
    public function generarCodigo() {
        $year = date('Y');

        // Obtener el último código del año
        $sql = "SELECT codigo_colegiado FROM colegiados
                WHERE codigo_colegiado LIKE :pattern
                ORDER BY codigo_colegiado DESC
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['pattern' => "CP{$year}%"]);
        $ultimo = $stmt->fetch();

        if ($ultimo) {
            // Extraer el número secuencial
            $numero = (int) substr($ultimo['codigo_colegiado'], -4);
            $numero++;
        } else {
            $numero = 1;
        }

        return "CP{$year}" . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Buscar colegiados con filtros
     */
    public function search($filters = []) {
        $sql = "SELECT c.*,
                CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', p.apellido_materno) as nombre_completo,
                p.numero_documento, p.email
                FROM colegiados c
                INNER JOIN personas p ON c.persona_id = p.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['codigo_colegiado'])) {
            $sql .= " AND c.codigo_colegiado LIKE :codigo";
            $params['codigo'] = '%' . $filters['codigo_colegiado'] . '%';
        }

        if (!empty($filters['nombres'])) {
            $sql .= " AND (p.nombres LIKE :nombres OR p.apellido_paterno LIKE :nombres OR p.apellido_materno LIKE :nombres)";
            $params['nombres'] = '%' . $filters['nombres'] . '%';
        }

        if (!empty($filters['especialidad'])) {
            $sql .= " AND c.especialidad LIKE :especialidad";
            $params['especialidad'] = '%' . $filters['especialidad'] . '%';
        }

        if (!empty($filters['estado'])) {
            $sql .= " AND c.estado = :estado";
            $params['estado'] = $filters['estado'];
        }

        $sql .= " ORDER BY c.codigo_colegiado DESC";

        return $this->query($sql, $params);
    }

    /**
     * Obtener colegiados activos
     */
    public function getActivos() {
        return $this->where('estado', 'ACTIVO');
    }

    /**
     * Cambiar estado
     */
    public function cambiarEstado($id, $estado) {
        return $this->update($id, ['estado' => $estado]);
    }

    /**
     * Obtener estadísticas de un colegiado
     */
    public function getEstadisticas($colegiadoId) {
        $sql = "SELECT
                (SELECT COUNT(*) FROM aportaciones WHERE colegiado_id = :id AND estado = 'PENDIENTE') as cuotas_pendientes,
                (SELECT COUNT(*) FROM aportaciones WHERE colegiado_id = :id AND estado = 'VENCIDO') as cuotas_vencidas,
                (SELECT COUNT(*) FROM aportaciones WHERE colegiado_id = :id AND estado = 'PAGADO') as cuotas_pagadas,
                (SELECT COALESCE(SUM(monto_total), 0) FROM aportaciones WHERE colegiado_id = :id AND estado IN ('PENDIENTE', 'VENCIDO')) as total_deuda,
                (SELECT COALESCE(SUM(p.monto_total), 0) FROM pagos p WHERE p.colegiado_id = :id AND p.estado = 'PROCESADO') as total_pagado";

        $result = $this->query($sql, ['id' => $colegiadoId]);
        return $result[0] ?? null;
    }
}

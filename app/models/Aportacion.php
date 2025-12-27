<?php
/**
 * Modelo Aportacion
 * Gestiona las aportaciones/cuotas de los colegiados
 */
class Aportacion extends Model {
    protected $table = 'aportaciones';

    /**
     * Obtener aportaciones con datos relacionados
     */
    public function getAllWithRelations() {
        $sql = "SELECT a.*,
                c.codigo_colegiado,
                CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', p.apellido_materno) as nombre_completo,
                ta.nombre as tipo_nombre
                FROM aportaciones a
                INNER JOIN colegiados c ON a.colegiado_id = c.id
                INNER JOIN personas p ON c.persona_id = p.id
                INNER JOIN tipos_aportacion ta ON a.tipo_aportacion_id = ta.id
                ORDER BY a.created_at DESC";

        return $this->query($sql);
    }

    /**
     * Buscar aportaciones con filtros
     */
    public function search($filters = []) {
        $sql = "SELECT a.*,
                c.codigo_colegiado,
                CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', p.apellido_materno) as nombre_completo,
                ta.nombre as tipo_nombre
                FROM aportaciones a
                INNER JOIN colegiados c ON a.colegiado_id = c.id
                INNER JOIN personas p ON c.persona_id = p.id
                INNER JOIN tipos_aportacion ta ON a.tipo_aportacion_id = ta.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['codigo_colegiado'])) {
            $sql .= " AND c.codigo_colegiado LIKE :codigo";
            $params['codigo'] = '%' . $filters['codigo_colegiado'] . '%';
        }

        if (!empty($filters['periodo'])) {
            $sql .= " AND a.periodo = :periodo";
            $params['periodo'] = $filters['periodo'];
        }

        if (!empty($filters['estado'])) {
            $sql .= " AND a.estado = :estado";
            $params['estado'] = $filters['estado'];
        }

        $sql .= " ORDER BY a.periodo DESC, a.fecha_vencimiento DESC";

        return $this->query($sql, $params);
    }

    /**
     * Obtener aportaciones pendientes
     */
    public function getPendientes() {
        return $this->where('estado', 'PENDIENTE');
    }

    /**
     * Obtener aportaciones vencidas
     */
    public function getVencidas() {
        return $this->where('estado', 'VENCIDO');
    }

    /**
     * Generar cuotas mensuales
     */
    public function generarCuotasMensuales($periodo) {
        $sql = "CALL sp_generar_cuotas_mensuales(:periodo)";
        return $this->execute($sql, ['periodo' => $periodo]);
    }

    /**
     * Calcular moras
     */
    public function calcularMoras() {
        $sql = "CALL sp_calcular_mora()";
        return $this->execute($sql);
    }

    /**
     * Obtener aportaciones por colegiado
     */
    public function getByColegiado($colegiadoId) {
        $sql = "SELECT a.*, ta.nombre as tipo_nombre
                FROM aportaciones a
                INNER JOIN tipos_aportacion ta ON a.tipo_aportacion_id = ta.id
                WHERE a.colegiado_id = :id
                ORDER BY a.periodo DESC";

        return $this->query($sql, ['id' => $colegiadoId]);
    }

    /**
     * Obtener aportaciones pendientes por colegiado
     */
    public function getPendientesByColegiado($colegiadoId) {
        $sql = "SELECT a.*, ta.nombre as tipo_nombre
                FROM aportaciones a
                INNER JOIN tipos_aportacion ta ON a.tipo_aportacion_id = ta.id
                WHERE a.colegiado_id = :id AND a.estado IN ('PENDIENTE', 'VENCIDO')
                ORDER BY a.fecha_vencimiento ASC";

        return $this->query($sql, ['id' => $colegiadoId]);
    }

    /**
     * Marcar como pagado
     */
    public function marcarComoPagado($id) {
        return $this->update($id, ['estado' => 'PAGADO']);
    }

    /**
     * Anular aportación
     */
    public function anular($id) {
        return $this->update($id, ['estado' => 'ANULADO']);
    }
}

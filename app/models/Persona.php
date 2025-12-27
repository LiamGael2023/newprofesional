<?php
/**
 * Modelo Persona
 * Gestiona el registro y mantenimiento de personas
 */
class Persona extends Model {
    protected $table = 'personas';

    /**
     * Obtener todas las personas activas
     */
    public function getActivas() {
        return $this->where('estado', 'ACTIVO');
    }

    /**
     * Buscar por documento
     */
    public function findByDocumento($numeroDocumento) {
        return $this->findWhere('numero_documento', $numeroDocumento);
    }

    /**
     * Verificar si existe documento
     */
    public function documentoExists($numeroDocumento, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM personas WHERE numero_documento = :numero_documento";
        if ($excludeId) {
            $sql .= " AND id != :id";
        }

        $stmt = $this->db->prepare($sql);
        $params = ['numero_documento' => $numeroDocumento];
        if ($excludeId) {
            $params['id'] = $excludeId;
        }

        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    /**
     * Buscar personas (con filtros)
     */
    public function search($filters = []) {
        $sql = "SELECT * FROM personas WHERE 1=1";
        $params = [];

        if (!empty($filters['numero_documento'])) {
            $sql .= " AND numero_documento LIKE :numero_documento";
            $params['numero_documento'] = '%' . $filters['numero_documento'] . '%';
        }

        if (!empty($filters['nombres'])) {
            $sql .= " AND (nombres LIKE :nombres OR apellido_paterno LIKE :nombres OR apellido_materno LIKE :nombres)";
            $params['nombres'] = '%' . $filters['nombres'] . '%';
        }

        if (!empty($filters['estado'])) {
            $sql .= " AND estado = :estado";
            $params['estado'] = $filters['estado'];
        }

        $sql .= " ORDER BY apellido_paterno, apellido_materno, nombres";

        return $this->query($sql, $params);
    }

    /**
     * Obtener personas que no son colegiadas
     */
    public function getNoCollegiadas() {
        $sql = "SELECT p.* FROM personas p
                LEFT JOIN colegiados c ON p.id = c.persona_id
                WHERE c.id IS NULL AND p.estado = 'ACTIVO'
                ORDER BY p.apellido_paterno, p.apellido_materno, p.nombres";

        return $this->query($sql);
    }

    /**
     * Cambiar estado
     */
    public function cambiarEstado($id, $estado) {
        return $this->update($id, ['estado' => $estado]);
    }

    /**
     * Obtener nombre completo
     */
    public function getNombreCompleto($persona) {
        return trim($persona['nombres'] . ' ' . $persona['apellido_paterno'] . ' ' . $persona['apellido_materno']);
    }
}

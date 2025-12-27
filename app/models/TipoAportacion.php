<?php
/**
 * Modelo TipoAportacion
 * Gestiona los tipos de aportaciones/cuotas
 */
class TipoAportacion extends Model {
    protected $table = 'tipos_aportacion';

    /**
     * Obtener tipos activos
     */
    public function getActivos() {
        return $this->where('activo', 1);
    }

    /**
     * Obtener tipos mensuales obligatorios
     */
    public function getMensualesObligatorios() {
        $sql = "SELECT * FROM tipos_aportacion
                WHERE periodicidad = 'MENSUAL' AND obligatorio = 1 AND activo = 1";
        return $this->query($sql);
    }
}

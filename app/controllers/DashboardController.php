<?php
/**
 * Controlador del Dashboard
 */
class DashboardController extends Controller {

    public function __construct() {
        // Requerir autenticación para todas las acciones
    }

    /**
     * Dashboard principal
     */
    public function index() {
        $this->requireAuth();

        // Obtener estadísticas generales
        $db = Database::getInstance()->getConnection();

        // Total de personas
        $stmt = $db->query("SELECT COUNT(*) as total FROM personas WHERE estado = 'ACTIVO'");
        $totalPersonas = $stmt->fetch()['total'];

        // Total de colegiados
        $stmt = $db->query("SELECT COUNT(*) as total FROM colegiados WHERE estado = 'ACTIVO'");
        $totalColegiados = $stmt->fetch()['total'];

        // Aportaciones pendientes
        $stmt = $db->query("SELECT COUNT(*) as total FROM aportaciones WHERE estado IN ('PENDIENTE', 'VENCIDO')");
        $aportacionesPendientes = $stmt->fetch()['total'];

        // Total recaudado este mes
        $stmt = $db->query("SELECT COALESCE(SUM(monto_total), 0) as total FROM pagos
                           WHERE MONTH(fecha_pago) = MONTH(CURDATE())
                           AND YEAR(fecha_pago) = YEAR(CURDATE())
                           AND estado = 'PROCESADO'");
        $totalRecaudadoMes = $stmt->fetch()['total'];

        // Últimos pagos
        $stmt = $db->query("SELECT p.*,
                           CONCAT(per.nombres, ' ', per.apellido_paterno, ' ', per.apellido_materno) as colegiado_nombre,
                           c.codigo_colegiado
                           FROM pagos p
                           INNER JOIN colegiados c ON p.colegiado_id = c.id
                           INNER JOIN personas per ON c.persona_id = per.id
                           WHERE p.estado = 'PROCESADO'
                           ORDER BY p.fecha_pago DESC
                           LIMIT 10");
        $ultimosPagos = $stmt->fetchAll();

        // Colegiados con aportaciones vencidas
        $stmt = $db->query("SELECT DISTINCT c.codigo_colegiado,
                           CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', p.apellido_materno) as nombre_completo,
                           COUNT(a.id) as cuotas_vencidas,
                           SUM(a.monto_total) as total_deuda
                           FROM aportaciones a
                           INNER JOIN colegiados c ON a.colegiado_id = c.id
                           INNER JOIN personas p ON c.persona_id = p.id
                           WHERE a.estado = 'VENCIDO'
                           GROUP BY c.id, c.codigo_colegiado, p.nombres, p.apellido_paterno, p.apellido_materno
                           ORDER BY total_deuda DESC
                           LIMIT 10");
        $deudores = $stmt->fetchAll();

        $this->view('dashboard/index', [
            'totalPersonas' => $totalPersonas,
            'totalColegiados' => $totalColegiados,
            'aportacionesPendientes' => $aportacionesPendientes,
            'totalRecaudadoMes' => $totalRecaudadoMes,
            'ultimosPagos' => $ultimosPagos,
            'deudores' => $deudores
        ]);
    }
}

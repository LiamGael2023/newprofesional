<?php
/**
 * Controlador de Reportes
 */
class ReportesController extends Controller {

    public function __construct() {
        $this->requirePermission('reportes');
    }

    /**
     * Página principal de reportes
     */
    public function index() {
        $this->view('reportes/index');
    }

    /**
     * Reporte de colegiados
     */
    public function colegiados() {
        $db = Database::getInstance()->getConnection();

        $estado = $this->get('estado');
        $especialidad = $this->get('especialidad');

        $sql = "SELECT c.*,
                CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', p.apellido_materno) as nombre_completo,
                p.numero_documento, p.email, p.celular
                FROM colegiados c
                INNER JOIN personas p ON c.persona_id = p.id
                WHERE 1=1";

        $params = [];

        if ($estado) {
            $sql .= " AND c.estado = :estado";
            $params['estado'] = $estado;
        }

        if ($especialidad) {
            $sql .= " AND c.especialidad LIKE :especialidad";
            $params['especialidad'] = '%' . $especialidad . '%';
        }

        $sql .= " ORDER BY c.codigo_colegiado";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $colegiados = $stmt->fetchAll();

        // Estadísticas
        $stmt = $db->query("SELECT
            COUNT(*) as total,
            SUM(CASE WHEN estado = 'ACTIVO' THEN 1 ELSE 0 END) as activos,
            SUM(CASE WHEN estado = 'SUSPENDIDO' THEN 1 ELSE 0 END) as suspendidos,
            SUM(CASE WHEN estado = 'INHABILITADO' THEN 1 ELSE 0 END) as inhabilitados
            FROM colegiados");
        $estadisticas = $stmt->fetch();

        $this->view('reportes/colegiados', [
            'colegiados' => $colegiados,
            'estadisticas' => $estadisticas,
            'filtro_estado' => $estado,
            'filtro_especialidad' => $especialidad
        ]);
    }

    /**
     * Reporte de aportaciones
     */
    public function aportaciones() {
        $db = Database::getInstance()->getConnection();

        $periodo = $this->get('periodo', date('Y-m'));
        $estado = $this->get('estado');

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

        if ($periodo) {
            $sql .= " AND a.periodo = :periodo";
            $params['periodo'] = $periodo;
        }

        if ($estado) {
            $sql .= " AND a.estado = :estado";
            $params['estado'] = $estado;
        }

        $sql .= " ORDER BY c.codigo_colegiado, a.periodo DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $aportaciones = $stmt->fetchAll();

        // Totales
        $totalMonto = 0;
        $totalMora = 0;
        foreach ($aportaciones as $apt) {
            $totalMonto += $apt['monto'];
            $totalMora += $apt['mora'];
        }

        $this->view('reportes/aportaciones', [
            'aportaciones' => $aportaciones,
            'filtro_periodo' => $periodo,
            'filtro_estado' => $estado,
            'totalMonto' => $totalMonto,
            'totalMora' => $totalMora
        ]);
    }

    /**
     * Reporte de pagos/recaudación
     */
    public function pagos() {
        $db = Database::getInstance()->getConnection();

        $fechaDesde = $this->get('fecha_desde', date('Y-m-01'));
        $fechaHasta = $this->get('fecha_hasta', date('Y-m-d'));
        $metodoPago = $this->get('metodo_pago');

        $sql = "SELECT p.*,
                c.codigo_colegiado,
                CONCAT(per.nombres, ' ', per.apellido_paterno, ' ', per.apellido_materno) as nombre_completo,
                u.username as usuario_nombre
                FROM pagos p
                INNER JOIN colegiados c ON p.colegiado_id = c.id
                INNER JOIN personas per ON c.persona_id = per.id
                INNER JOIN usuarios u ON p.usuario_id = u.id
                WHERE p.estado = 'PROCESADO'
                AND DATE(p.fecha_pago) BETWEEN :fecha_desde AND :fecha_hasta";

        $params = [
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta
        ];

        if ($metodoPago) {
            $sql .= " AND p.metodo_pago = :metodo";
            $params['metodo'] = $metodoPago;
        }

        $sql .= " ORDER BY p.fecha_pago DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $pagos = $stmt->fetchAll();

        // Totales por método de pago
        $sqlTotales = "SELECT metodo_pago, COUNT(*) as cantidad, SUM(monto_total) as total
                      FROM pagos
                      WHERE estado = 'PROCESADO'
                      AND DATE(fecha_pago) BETWEEN :fecha_desde AND :fecha_hasta
                      GROUP BY metodo_pago";

        $stmt = $db->prepare($sqlTotales);
        $stmt->execute(['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta]);
        $totalesPorMetodo = $stmt->fetchAll();

        // Total general
        $totalGeneral = array_sum(array_column($totalesPorMetodo, 'total'));

        $this->view('reportes/pagos', [
            'pagos' => $pagos,
            'totalesPorMetodo' => $totalesPorMetodo,
            'totalGeneral' => $totalGeneral,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'metodoPago' => $metodoPago
        ]);
    }

    /**
     * Reporte de morosidad
     */
    public function morosidad() {
        $db = Database::getInstance()->getConnection();

        $sql = "SELECT
                c.codigo_colegiado,
                CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', p.apellido_materno) as nombre_completo,
                p.email, p.celular,
                COUNT(a.id) as cuotas_vencidas,
                SUM(a.monto_total) as total_deuda,
                MIN(a.fecha_vencimiento) as primera_cuota_vencida,
                DATEDIFF(CURDATE(), MIN(a.fecha_vencimiento)) as dias_mora
                FROM aportaciones a
                INNER JOIN colegiados c ON a.colegiado_id = c.id
                INNER JOIN personas p ON c.persona_id = p.id
                WHERE a.estado = 'VENCIDO' AND c.estado = 'ACTIVO'
                GROUP BY c.id, c.codigo_colegiado, p.nombres, p.apellido_paterno, p.apellido_materno, p.email, p.celular
                ORDER BY total_deuda DESC";

        $stmt = $db->query($sql);
        $deudores = $stmt->fetchAll();

        // Totales
        $totalDeudores = count($deudores);
        $totalDeuda = array_sum(array_column($deudores, 'total_deuda'));

        $this->view('reportes/morosidad', [
            'deudores' => $deudores,
            'totalDeudores' => $totalDeudores,
            'totalDeuda' => $totalDeuda
        ]);
    }

    /**
     * Reporte de auditoría
     */
    public function auditoria() {
        $db = Database::getInstance()->getConnection();

        $fechaDesde = $this->get('fecha_desde', date('Y-m-01'));
        $fechaHasta = $this->get('fecha_hasta', date('Y-m-d'));
        $tabla = $this->get('tabla');
        $accion = $this->get('accion');

        $sql = "SELECT a.*, u.username
                FROM auditoria a
                LEFT JOIN usuarios u ON a.usuario_id = u.id
                WHERE DATE(a.created_at) BETWEEN :fecha_desde AND :fecha_hasta";

        $params = [
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta
        ];

        if ($tabla) {
            $sql .= " AND a.tabla = :tabla";
            $params['tabla'] = $tabla;
        }

        if ($accion) {
            $sql .= " AND a.accion = :accion";
            $params['accion'] = $accion;
        }

        $sql .= " ORDER BY a.created_at DESC LIMIT 1000";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $registros = $stmt->fetchAll();

        $this->view('reportes/auditoria', [
            'registros' => $registros,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'filtroTabla' => $tabla,
            'filtroAccion' => $accion
        ]);
    }

    /**
     * Reporte estadístico general
     */
    public function estadisticas() {
        $db = Database::getInstance()->getConnection();

        // Estadísticas de colegiados
        $stmt = $db->query("SELECT
            COUNT(*) as total,
            SUM(CASE WHEN estado = 'ACTIVO' THEN 1 ELSE 0 END) as activos,
            SUM(CASE WHEN tipo_colegiatura = 'ORDINARIO' THEN 1 ELSE 0 END) as ordinarios,
            SUM(CASE WHEN tipo_colegiatura = 'VITALICIO' THEN 1 ELSE 0 END) as vitalicios
            FROM colegiados");
        $statsColeg = $stmt->fetch();

        // Estadísticas de aportaciones
        $stmt = $db->query("SELECT
            COUNT(*) as total,
            SUM(CASE WHEN estado = 'PENDIENTE' THEN 1 ELSE 0 END) as pendientes,
            SUM(CASE WHEN estado = 'VENCIDO' THEN 1 ELSE 0 END) as vencidas,
            SUM(CASE WHEN estado = 'PAGADO' THEN 1 ELSE 0 END) as pagadas,
            SUM(CASE WHEN estado IN ('PENDIENTE', 'VENCIDO') THEN monto_total ELSE 0 END) as monto_pendiente
            FROM aportaciones");
        $statsAport = $stmt->fetch();

        // Recaudación mensual del año actual
        $stmt = $db->prepare("SELECT
            MONTH(fecha_pago) as mes,
            COUNT(*) as cantidad,
            SUM(monto_total) as total
            FROM pagos
            WHERE YEAR(fecha_pago) = YEAR(CURDATE()) AND estado = 'PROCESADO'
            GROUP BY MONTH(fecha_pago)
            ORDER BY mes");
        $stmt->execute();
        $recaudacionMensual = $stmt->fetchAll();

        // Colegiados por especialidad
        $stmt = $db->query("SELECT especialidad, COUNT(*) as cantidad
                           FROM colegiados
                           WHERE estado = 'ACTIVO'
                           GROUP BY especialidad
                           ORDER BY cantidad DESC
                           LIMIT 10");
        $porEspecialidad = $stmt->fetchAll();

        $this->view('reportes/estadisticas', [
            'statsColeg' => $statsColeg,
            'statsAport' => $statsAport,
            'recaudacionMensual' => $recaudacionMensual,
            'porEspecialidad' => $porEspecialidad
        ]);
    }
}

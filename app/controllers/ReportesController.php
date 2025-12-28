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
            SUM(CASE WHEN estado = 'INACTIVO' THEN 1 ELSE 0 END) as inactivos,
            SUM(CASE WHEN estado = 'SUSPENDIDO' THEN 1 ELSE 0 END) as suspendidos
            FROM colegiados");
        $stats = $stmt->fetch();

        $this->view('reportes/colegiados', [
            'colegiados' => $colegiados,
            'totalColegiados' => $stats['total'],
            'totalActivos' => $stats['activos'],
            'totalInactivos' => $stats['inactivos'],
            'totalSuspendidos' => $stats['suspendidos'],
            'filters' => [
                'estado' => $estado,
                'anio' => $this->get('anio'),
                'busqueda' => $this->get('busqueda')
            ]
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
        $totalAportaciones = count($aportaciones);
        $montoTotal = 0;
        $totalPagadas = 0;
        $totalPendientes = 0;

        foreach ($aportaciones as $apt) {
            $montoTotal += $apt['monto_total'];
            if ($apt['estado'] == 'PAGADO') {
                $totalPagadas++;
            } elseif ($apt['estado'] == 'PENDIENTE' || $apt['estado'] == 'VENCIDO') {
                $totalPendientes++;
            }
        }

        // Obtener tipos de aportación
        $tipos = $db->query("SELECT * FROM tipos_aportacion ORDER BY nombre")->fetchAll();

        $this->view('reportes/aportaciones', [
            'aportaciones' => $aportaciones,
            'totalAportaciones' => $totalAportaciones,
            'montoTotal' => $montoTotal,
            'totalPagadas' => $totalPagadas,
            'totalPendientes' => $totalPendientes,
            'tipos' => $tipos,
            'filters' => [
                'periodo' => $periodo,
                'estado' => $estado,
                'tipo' => $this->get('tipo')
            ]
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

        // Totales
        $totalPagos = count($pagos);
        $montoTotal = array_sum(array_column($pagos, 'monto_total'));

        // Totales por método de pago
        $sqlTotales = "SELECT metodo_pago, COUNT(*) as cantidad, SUM(monto_total) as total
                      FROM pagos
                      WHERE estado = 'PROCESADO'
                      AND DATE(fecha_pago) BETWEEN :fecha_desde AND :fecha_hasta
                      GROUP BY metodo_pago";

        $stmt = $db->prepare($sqlTotales);
        $stmt->execute(['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta]);
        $detalleMetodos = $stmt->fetchAll();

        // Calcular totales por tipo de pago
        $montoEfectivo = 0;
        $montoDigital = 0;

        foreach ($detalleMetodos as $metodo) {
            if ($metodo['metodo_pago'] == 'EFECTIVO') {
                $montoEfectivo = $metodo['total'];
            } else {
                $montoDigital += $metodo['total'];
            }
        }

        // Obtener usuarios para filtro
        $usuarios = $db->query("SELECT id, username FROM usuarios ORDER BY username")->fetchAll();

        $this->view('reportes/pagos', [
            'pagos' => $pagos,
            'totalPagos' => $totalPagos,
            'montoTotal' => $montoTotal,
            'montoEfectivo' => $montoEfectivo,
            'montoDigital' => $montoDigital,
            'detalleMetodos' => $detalleMetodos,
            'usuarios' => $usuarios,
            'filters' => [
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
                'metodo_pago' => $metodoPago,
                'usuario_id' => $this->get('usuario_id')
            ]
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
        $operacion = $this->get('operacion');
        $usuarioId = $this->get('usuario_id');

        $sql = "SELECT a.*, u.username as usuario_nombre
                FROM auditoria a
                LEFT JOIN usuarios u ON a.usuario_id = u.id
                WHERE DATE(a.fecha_operacion) BETWEEN :fecha_desde AND :fecha_hasta";

        $params = [
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta
        ];

        if ($tabla) {
            $sql .= " AND a.tabla_afectada = :tabla";
            $params['tabla'] = $tabla;
        }

        if ($operacion) {
            $sql .= " AND a.operacion = :operacion";
            $params['operacion'] = $operacion;
        }

        if ($usuarioId) {
            $sql .= " AND a.usuario_id = :usuario_id";
            $params['usuario_id'] = $usuarioId;
        }

        $sql .= " ORDER BY a.fecha_operacion DESC LIMIT 1000";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $auditoria = $stmt->fetchAll();

        // Obtener estadísticas
        $sqlStats = "SELECT
                    SUM(CASE WHEN operacion = 'INSERT' THEN 1 ELSE 0 END) as total_insert,
                    SUM(CASE WHEN operacion = 'UPDATE' THEN 1 ELSE 0 END) as total_update,
                    SUM(CASE WHEN operacion = 'DELETE' THEN 1 ELSE 0 END) as total_delete
                    FROM auditoria
                    WHERE DATE(fecha_operacion) BETWEEN :fecha_desde AND :fecha_hasta";

        $stmt = $db->prepare($sqlStats);
        $stmt->execute(['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta]);
        $stats = $stmt->fetch();

        // Obtener usuarios para filtro
        $usuarios = $db->query("SELECT id, username FROM usuarios ORDER BY username")->fetchAll();

        $this->view('reportes/auditoria', [
            'auditoria' => $auditoria,
            'stats' => $stats,
            'usuarios' => $usuarios,
            'filters' => [
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
                'tabla' => $tabla,
                'operacion' => $operacion,
                'usuario_id' => $usuarioId
            ]
        ]);
    }

    /**
     * Reporte estadístico general
     */
    public function estadisticas() {
        $db = Database::getInstance()->getConnection();

        // Estadísticas generales
        $stmt = $db->query("SELECT
            COUNT(*) as total_colegiados,
            SUM(CASE WHEN estado = 'ACTIVO' THEN 1 ELSE 0 END) as colegiados_activos
            FROM colegiados");
        $statsColeg = $stmt->fetch();

        // Recaudación
        $stmt = $db->query("SELECT
            SUM(CASE WHEN YEAR(fecha_pago) = YEAR(CURDATE()) THEN monto_total ELSE 0 END) as recaudacion_total,
            SUM(CASE WHEN MONTH(fecha_pago) = MONTH(CURDATE()) AND YEAR(fecha_pago) = YEAR(CURDATE()) THEN monto_total ELSE 0 END) as recaudacion_mes
            FROM pagos WHERE estado = 'PROCESADO'");
        $statsRecaudacion = $stmt->fetch();

        // Cuotas pendientes
        $stmt = $db->query("SELECT
            COUNT(*) as cuotas_pendientes,
            SUM(monto_total) as monto_pendiente
            FROM aportaciones
            WHERE estado IN ('PENDIENTE', 'VENCIDO')");
        $statsCuotas = $stmt->fetch();

        // Tasa de morosidad
        $stmt = $db->query("SELECT
            COUNT(DISTINCT colegiado_id) as total_morosos
            FROM aportaciones
            WHERE estado = 'VENCIDO'");
        $statsMora = $stmt->fetch();
        $tasaMorosidad = $statsColeg['total_colegiados'] > 0
            ? ($statsMora['total_morosos'] / $statsColeg['total_colegiados']) * 100
            : 0;

        // Colegiados por estado
        $stmt = $db->query("SELECT estado, COUNT(*) as total FROM colegiados GROUP BY estado");
        $estadosColegiados = $stmt->fetchAll();

        // Recaudación mensual
        $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $stmt = $db->query("SELECT
            MONTH(fecha_pago) as mes,
            SUM(monto_total) as total
            FROM pagos
            WHERE YEAR(fecha_pago) = YEAR(CURDATE()) AND estado = 'PROCESADO'
            GROUP BY MONTH(fecha_pago)");
        $recaudacionData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $recaudacionMensual = [];
        for ($i = 1; $i <= 12; $i++) {
            $recaudacionMensual[] = [
                'mes' => $meses[$i - 1],
                'total' => $recaudacionData[$i] ?? 0
            ];
        }

        // Métodos de pago
        $stmt = $db->query("SELECT metodo_pago, SUM(monto_total) as total
            FROM pagos
            WHERE YEAR(fecha_pago) = YEAR(CURDATE()) AND estado = 'PROCESADO'
            GROUP BY metodo_pago");
        $metodosPago = $stmt->fetchAll();

        // Evolución de colegiados (últimos 12 meses)
        $stmt = $db->query("SELECT
            DATE_FORMAT(fecha_colegiacion, '%Y-%m') as mes,
            COUNT(*) as total
            FROM colegiados
            WHERE fecha_colegiacion >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(fecha_colegiacion, '%Y-%m')
            ORDER BY mes");
        $evolucionData = $stmt->fetchAll();

        // Formatear evolución con totales acumulados
        $totalAcumulado = $statsColeg['total_colegiados'] - count($evolucionData);
        $evolucionColegiados = [];
        foreach ($evolucionData as $dato) {
            $totalAcumulado += $dato['total'];
            $evolucionColegiados[] = [
                'mes' => date('M Y', strtotime($dato['mes'] . '-01')),
                'total' => $totalAcumulado
            ];
        }

        // Top 10 deudores
        $stmt = $db->query("SELECT
            c.codigo_colegiado,
            CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', p.apellido_materno) as nombre_completo,
            COUNT(a.id) as cuotas_vencidas,
            SUM(a.monto_total) as total_deuda
            FROM aportaciones a
            INNER JOIN colegiados c ON a.colegiado_id = c.id
            INNER JOIN personas p ON c.persona_id = p.id
            WHERE a.estado = 'VENCIDO'
            GROUP BY c.id
            ORDER BY total_deuda DESC
            LIMIT 10");
        $topDeudores = $stmt->fetchAll();

        // Resumen por tipo de aportación
        $stmt = $db->query("SELECT
            ta.nombre,
            COUNT(a.id) as total_generadas,
            SUM(CASE WHEN a.estado = 'PAGADO' THEN 1 ELSE 0 END) as total_pagadas,
            SUM(a.monto) as monto_total
            FROM aportaciones a
            INNER JOIN tipos_aportacion ta ON a.tipo_aportacion_id = ta.id
            WHERE YEAR(a.created_at) = YEAR(CURDATE())
            GROUP BY ta.id");
        $resumenTipos = $stmt->fetchAll();

        $this->view('reportes/estadisticas', [
            'stats' => [
                'total_colegiados' => $statsColeg['total_colegiados'],
                'colegiados_activos' => $statsColeg['colegiados_activos'],
                'recaudacion_total' => $statsRecaudacion['recaudacion_total'] ?? 0,
                'recaudacion_mes' => $statsRecaudacion['recaudacion_mes'] ?? 0,
                'cuotas_pendientes' => $statsCuotas['cuotas_pendientes'] ?? 0,
                'monto_pendiente' => $statsCuotas['monto_pendiente'] ?? 0,
                'tasa_morosidad' => $tasaMorosidad,
                'total_morosos' => $statsMora['total_morosos']
            ],
            'estadosColegiados' => $estadosColegiados,
            'recaudacionMensual' => $recaudacionMensual,
            'metodosPago' => $metodosPago,
            'evolucionColegiados' => $evolucionColegiados,
            'topDeudores' => $topDeudores,
            'resumenTipos' => $resumenTipos
        ]);
    }
}

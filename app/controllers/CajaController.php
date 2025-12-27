<?php
/**
 * Controlador de Caja
 * Gestiona el procesamiento de pagos
 */
class CajaController extends Controller {

    private $pagoModel;
    private $colegiadoModel;
    private $aportacionModel;

    public function __construct() {
        $this->pagoModel = $this->model('Pago');
        $this->colegiadoModel = $this->model('Colegiado');
        $this->aportacionModel = $this->model('Aportacion');
    }

    /**
     * Listar pagos
     */
    public function index() {
        $this->requirePermission('caja');

        $filters = [
            'numero_recibo' => $this->get('numero_recibo'),
            'codigo_colegiado' => $this->get('codigo_colegiado'),
            'fecha_desde' => $this->get('fecha_desde'),
            'fecha_hasta' => $this->get('fecha_hasta'),
            'metodo_pago' => $this->get('metodo_pago'),
            'estado' => $this->get('estado')
        ];

        $pagos = $this->pagoModel->search($filters);

        $this->view('caja/index', [
            'pagos' => $pagos,
            'filters' => $filters
        ]);
    }

    /**
     * Formulario de nuevo pago
     */
    public function create($colegiadoId = null) {
        $this->requirePermission('caja');

        $colegiado = null;
        $aportacionesPendientes = [];

        if ($colegiadoId) {
            $colegiado = $this->colegiadoModel->getWithPersona($colegiadoId);
            $aportacionesPendientes = $this->aportacionModel->getPendientesByColegiado($colegiadoId);
        }

        $colegiados = $this->colegiadoModel->getActivos();

        $this->view('caja/create', [
            'colegiado' => $colegiado,
            'aportacionesPendientes' => $aportacionesPendientes,
            'colegiados' => $colegiados
        ]);
    }

    /**
     * Obtener aportaciones pendientes de un colegiado (AJAX)
     */
    public function getAportacionesPendientes($colegiadoId) {
        $this->requirePermission('caja');

        $aportaciones = $this->aportacionModel->getPendientesByColegiado($colegiadoId);
        $this->json(['success' => true, 'aportaciones' => $aportaciones]);
    }

    /**
     * Procesar pago
     */
    public function store() {
        $this->requirePermission('caja');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('caja');
        }

        $colegiadoId = $this->post('colegiado_id');
        $aportaciones = $this->post('aportaciones', []);
        $metodo_pago = $this->post('metodo_pago');
        $numero_operacion = $this->post('numero_operacion');
        $observaciones = $this->post('observaciones');

        // Validar
        if (empty($colegiadoId)) {
            $this->setFlash('error', 'Debe seleccionar un colegiado');
            $this->redirect('caja/create');
        }

        if (empty($aportaciones)) {
            $this->setFlash('error', 'Debe seleccionar al menos una aportación');
            $this->redirect('caja/create/' . $colegiadoId);
        }

        // Calcular total
        $montoTotal = 0;
        $aportacionesData = [];

        foreach ($aportaciones as $aportacionId) {
            $aportacion = $this->aportacionModel->find($aportacionId);
            if ($aportacion && $aportacion['estado'] != 'PAGADO') {
                $montoTotal += $aportacion['monto_total'];
                $aportacionesData[$aportacionId] = $aportacion['monto_total'];
            }
        }

        if ($montoTotal == 0) {
            $this->setFlash('error', 'No hay aportaciones válidas para procesar');
            $this->redirect('caja/create/' . $colegiadoId);
        }

        // Generar número de recibo
        $numeroRecibo = generateReciboNumber();

        // Preparar datos del pago
        $dataPago = [
            'colegiado_id' => $colegiadoId,
            'numero_recibo' => $numeroRecibo,
            'fecha_pago' => date('Y-m-d H:i:s'),
            'monto_total' => $montoTotal,
            'metodo_pago' => $metodo_pago,
            'numero_operacion' => $numero_operacion,
            'usuario_id' => currentUser(),
            'observaciones' => $observaciones
        ];

        try {
            $pagoId = $this->pagoModel->registrarPago($dataPago, $aportacionesData);

            logAudit('pagos', $pagoId, 'INSERT', null, $dataPago);

            $this->setFlash('success', 'Pago procesado correctamente. Recibo N° ' . $numeroRecibo);
            $this->redirect('caja/recibo/' . $pagoId);

        } catch (Exception $e) {
            $this->setFlash('error', 'Error al procesar el pago: ' . $e->getMessage());
            $this->redirect('caja/create/' . $colegiadoId);
        }
    }

    /**
     * Ver recibo
     */
    public function recibo($pagoId) {
        $this->requirePermission('caja');

        $db = Database::getInstance()->getConnection();

        // Obtener datos del pago
        $stmt = $db->prepare("SELECT p.*,
                             c.codigo_colegiado,
                             CONCAT(per.nombres, ' ', per.apellido_paterno, ' ', per.apellido_materno) as nombre_completo,
                             per.numero_documento,
                             u.username as usuario_nombre
                             FROM pagos p
                             INNER JOIN colegiados c ON p.colegiado_id = c.id
                             INNER JOIN personas per ON c.persona_id = per.id
                             INNER JOIN usuarios u ON p.usuario_id = u.id
                             WHERE p.id = ?");
        $stmt->execute([$pagoId]);
        $pago = $stmt->fetch();

        if (!$pago) {
            $this->setFlash('error', 'Pago no encontrado');
            $this->redirect('caja');
        }

        // Obtener detalle
        $detalle = $this->pagoModel->getDetalle($pagoId);

        $this->view('caja/recibo', [
            'pago' => $pago,
            'detalle' => $detalle
        ]);
    }

    /**
     * Anular pago
     */
    public function anular($pagoId) {
        $this->requirePermission('caja');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $pago = $this->pagoModel->find($pagoId);

        if (!$pago) {
            $this->json(['success' => false, 'message' => 'Pago no encontrado'], 404);
        }

        if ($pago['estado'] === 'ANULADO') {
            $this->json(['success' => false, 'message' => 'El pago ya está anulado'], 400);
        }

        $motivo = $this->post('motivo');

        if (empty($motivo)) {
            $this->json(['success' => false, 'message' => 'Debe especificar el motivo de anulación'], 400);
        }

        try {
            $this->pagoModel->anularPago($pagoId, $motivo);
            logAudit('pagos', $pagoId, 'UPDATE', ['estado' => 'PROCESADO'], ['estado' => 'ANULADO', 'motivo' => $motivo]);
            $this->json(['success' => true, 'message' => 'Pago anulado correctamente']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Resumen de caja
     */
    public function resumen() {
        $this->requirePermission('caja');

        $fechaDesde = $this->get('fecha_desde', date('Y-m-01'));
        $fechaHasta = $this->get('fecha_hasta', date('Y-m-d'));

        // Totales por método de pago
        $totalesPorMetodo = $this->pagoModel->getTotalesPorMetodo($fechaDesde, $fechaHasta);

        // Total general
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) as cantidad, COALESCE(SUM(monto_total), 0) as total
                             FROM pagos
                             WHERE estado = 'PROCESADO'
                             AND DATE(fecha_pago) BETWEEN ? AND ?");
        $stmt->execute([$fechaDesde, $fechaHasta]);
        $totalGeneral = $stmt->fetch();

        $this->view('caja/resumen', [
            'totalesPorMetodo' => $totalesPorMetodo,
            'totalGeneral' => $totalGeneral,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta
        ]);
    }
}

<?php
/**
 * Controlador de Aportaciones
 */
class AportacionesController extends Controller {

    private $aportacionModel;
    private $tipoAportacionModel;
    private $colegiadoModel;

    public function __construct() {
        $this->aportacionModel = $this->model('Aportacion');
        $this->tipoAportacionModel = $this->model('TipoAportacion');
        $this->colegiadoModel = $this->model('Colegiado');
    }

    /**
     * Listar aportaciones
     */
    public function index() {
        $this->requirePermission('aportaciones');

        $filters = [
            'codigo_colegiado' => $this->get('codigo_colegiado'),
            'periodo' => $this->get('periodo'),
            'estado' => $this->get('estado')
        ];

        $aportaciones = $this->aportacionModel->search($filters);

        $this->view('aportaciones/index', [
            'aportaciones' => $aportaciones,
            'filters' => $filters
        ]);
    }

    /**
     * Generar cuotas mensuales
     */
    public function generar() {
        $this->requirePermission('aportaciones');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $periodo = $this->post('periodo', date('Y-m'));

            try {
                // Verificar si ya existen cuotas para este periodo
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("SELECT COUNT(*) as count FROM aportaciones WHERE periodo = ?");
                $stmt->execute([$periodo]);
                $exists = $stmt->fetch();

                if ($exists['count'] > 0) {
                    $this->setFlash('warning', 'Ya existen cuotas generadas para el periodo ' . $periodo);
                } else {
                    $this->aportacionModel->generarCuotasMensuales($periodo);
                    logAudit('aportaciones', 0, 'INSERT', null, ['periodo' => $periodo, 'accion' => 'generacion_masiva']);
                    $this->setFlash('success', 'Cuotas generadas correctamente para el periodo ' . $periodo);
                }
            } catch (Exception $e) {
                $this->setFlash('error', 'Error al generar cuotas: ' . $e->getMessage());
            }

            $this->redirect('aportaciones');
        }

        $this->view('aportaciones/generar');
    }

    /**
     * Calcular moras
     */
    public function calcularMoras() {
        $this->requirePermission('aportaciones');

        try {
            $this->aportacionModel->calcularMoras();
            logAudit('aportaciones', 0, 'UPDATE', null, ['accion' => 'calculo_moras']);
            $this->json(['success' => true, 'message' => 'Moras calculadas correctamente']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Ver detalle de aportación
     */
    public function view($id) {
        $this->requirePermission('aportaciones');

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT a.*,
                             c.codigo_colegiado,
                             CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', p.apellido_materno) as nombre_completo,
                             ta.nombre as tipo_nombre,
                             ta.descripcion as tipo_descripcion
                             FROM aportaciones a
                             INNER JOIN colegiados c ON a.colegiado_id = c.id
                             INNER JOIN personas p ON c.persona_id = p.id
                             INNER JOIN tipos_aportacion ta ON a.tipo_aportacion_id = ta.id
                             WHERE a.id = ?");
        $stmt->execute([$id]);
        $aportacion = $stmt->fetch();

        if (!$aportacion) {
            $this->setFlash('error', 'Aportación no encontrada');
            $this->redirect('aportaciones');
        }

        $this->view('aportaciones/view', [
            'aportacion' => $aportacion
        ]);
    }

    /**
     * Crear aportación manual
     */
    public function create($colegiadoId = null) {
        $this->requirePermission('aportaciones');

        $colegiado = null;
        if ($colegiadoId) {
            $colegiado = $this->colegiadoModel->getWithPersona($colegiadoId);
        }

        $colegiados = $this->colegiadoModel->getActivos();
        $tiposAportacion = $this->tipoAportacionModel->getActivos();

        $this->view('aportaciones/create', [
            'colegiado' => $colegiado,
            'colegiados' => $colegiados,
            'tiposAportacion' => $tiposAportacion
        ]);
    }

    /**
     * Guardar aportación manual
     */
    public function store() {
        $this->requirePermission('aportaciones');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('aportaciones');
        }

        $data = [
            'colegiado_id' => $this->post('colegiado_id'),
            'tipo_aportacion_id' => $this->post('tipo_aportacion_id'),
            'periodo' => $this->post('periodo'),
            'monto' => $this->post('monto'),
            'descuento' => $this->post('descuento', 0),
            'fecha_vencimiento' => $this->post('fecha_vencimiento'),
            'observaciones' => $this->post('observaciones')
        ];

        // Calcular monto total
        $data['monto_total'] = $data['monto'] - $data['descuento'];

        // Validar
        $errors = $this->validate($data, [
            'colegiado_id' => 'required|numeric',
            'tipo_aportacion_id' => 'required|numeric',
            'periodo' => 'required',
            'monto' => 'required|numeric',
            'fecha_vencimiento' => 'required'
        ]);

        if (!empty($errors)) {
            $this->view('aportaciones/create', [
                'errors' => $errors,
                'data' => $data,
                'colegiados' => $this->colegiadoModel->getActivos(),
                'tiposAportacion' => $this->tipoAportacionModel->getActivos()
            ]);
            return;
        }

        // Verificar si ya existe
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM aportaciones
                             WHERE colegiado_id = ? AND tipo_aportacion_id = ? AND periodo = ?");
        $stmt->execute([$data['colegiado_id'], $data['tipo_aportacion_id'], $data['periodo']]);
        $exists = $stmt->fetch();

        if ($exists['count'] > 0) {
            $this->setFlash('error', 'Ya existe una aportación de este tipo para el periodo seleccionado');
            $this->redirect('aportaciones/create');
        }

        // Guardar
        $id = $this->aportacionModel->insert($data);

        if ($id) {
            logAudit('aportaciones', $id, 'INSERT', null, $data);
            $this->setFlash('success', 'Aportación creada correctamente');
            $this->redirect('aportaciones');
        } else {
            $this->setFlash('error', 'Error al crear la aportación');
            $this->redirect('aportaciones/create');
        }
    }

    /**
     * Anular aportación
     */
    public function anular($id) {
        $this->requirePermission('aportaciones');

        $aportacion = $this->aportacionModel->find($id);

        if (!$aportacion) {
            $this->json(['success' => false, 'message' => 'Aportación no encontrada'], 404);
        }

        if ($aportacion['estado'] === 'PAGADO') {
            $this->json(['success' => false, 'message' => 'No se puede anular una aportación pagada'], 400);
        }

        if ($this->aportacionModel->anular($id)) {
            logAudit('aportaciones', $id, 'UPDATE', ['estado' => $aportacion['estado']], ['estado' => 'ANULADO']);
            $this->json(['success' => true, 'message' => 'Aportación anulada correctamente']);
        } else {
            $this->json(['success' => false, 'message' => 'Error al anular la aportación'], 500);
        }
    }
}

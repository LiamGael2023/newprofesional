<?php
/**
 * Controlador de Colegiados
 */
class ColegiadosController extends Controller {

    private $colegiadoModel;
    private $personaModel;

    public function __construct() {
        $this->colegiadoModel = $this->model('Colegiado');
        $this->personaModel = $this->model('Persona');
    }

    /**
     * Listar colegiados
     */
    public function index() {
        $this->requirePermission('colegiados');

        $filters = [
            'codigo_colegiado' => $this->get('codigo_colegiado'),
            'nombres' => $this->get('nombres'),
            'especialidad' => $this->get('especialidad'),
            'estado' => $this->get('estado')
        ];

        $colegiados = $this->colegiadoModel->search($filters);

        $this->view('colegiados/index', [
            'colegiados' => $colegiados,
            'filters' => $filters
        ]);
    }

    /**
     * Mostrar formulario de creación (conversión de persona a colegiado)
     */
    public function create($personaId = null) {
        $this->requirePermission('colegiados');

        $persona = null;
        if ($personaId) {
            $persona = $this->personaModel->find($personaId);
        }

        // Obtener personas no colegiadas
        $personasDisponibles = $this->personaModel->getNoCollegiadas();

        $this->view('colegiados/create', [
            'persona' => $persona,
            'personasDisponibles' => $personasDisponibles
        ]);
    }

    /**
     * Guardar nuevo colegiado
     */
    public function store() {
        $this->requirePermission('colegiados');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('colegiados');
        }

        $personaId = $this->post('persona_id');

        // Verificar que la persona existe
        $persona = $this->personaModel->find($personaId);
        if (!$persona) {
            $this->setFlash('error', 'Persona no encontrada');
            $this->redirect('colegiados/create');
        }

        // Verificar que no sea ya un colegiado
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM colegiados WHERE persona_id = ?");
        $stmt->execute([$personaId]);
        $exists = $stmt->fetch();

        if ($exists['count'] > 0) {
            $this->setFlash('error', 'La persona ya es un colegiado');
            $this->redirect('colegiados/create');
        }

        // Generar código
        $codigo = $this->colegiadoModel->generarCodigo();

        $data = [
            'persona_id' => $personaId,
            'codigo_colegiado' => $codigo,
            'especialidad' => $this->post('especialidad'),
            'universidad' => $this->post('universidad'),
            'fecha_graduacion' => $this->post('fecha_graduacion'),
            'fecha_colegiatura' => $this->post('fecha_colegiatura'),
            'numero_titulo' => $this->post('numero_titulo'),
            'tipo_colegiatura' => $this->post('tipo_colegiatura', 'ORDINARIO'),
            'observaciones' => $this->post('observaciones')
        ];

        // Validar
        $errors = $this->validate($data, [
            'especialidad' => 'required|min:3',
            'universidad' => 'required|min:3',
            'fecha_graduacion' => 'required',
            'fecha_colegiatura' => 'required'
        ]);

        if (!empty($errors)) {
            $this->view('colegiados/create', [
                'errors' => $errors,
                'data' => $data,
                'persona' => $persona,
                'personasDisponibles' => $this->personaModel->getNoCollegiadas()
            ]);
            return;
        }

        // Guardar
        $this->colegiadoModel->beginTransaction();

        try {
            $id = $this->colegiadoModel->insert($data);

            if ($id) {
                // Generar derecho de incorporación
                $tipoAportacionModel = $this->model('TipoAportacion');
                $incorporacion = $tipoAportacionModel->findWhere('periodicidad', 'UNICO');

                if ($incorporacion) {
                    $aportacionModel = $this->model('Aportacion');
                    $aportacionModel->insert([
                        'colegiado_id' => $id,
                        'tipo_aportacion_id' => $incorporacion['id'],
                        'periodo' => date('Y-m'),
                        'monto' => $incorporacion['monto_base'],
                        'monto_total' => $incorporacion['monto_base'],
                        'fecha_vencimiento' => date('Y-m-d', strtotime('+30 days'))
                    ]);
                }

                logAudit('colegiados', $id, 'INSERT', null, $data);

                $this->colegiadoModel->commit();
                $this->setFlash('success', 'Colegiado registrado correctamente con código: ' . $codigo);
                $this->redirect('colegiados');
            } else {
                $this->colegiadoModel->rollback();
                $this->setFlash('error', 'Error al registrar el colegiado');
                $this->redirect('colegiados/create');
            }
        } catch (Exception $e) {
            $this->colegiadoModel->rollback();
            $this->setFlash('error', 'Error: ' . $e->getMessage());
            $this->redirect('colegiados/create');
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id) {
        $this->requirePermission('colegiados');

        $colegiado = $this->colegiadoModel->getWithPersona($id);

        if (!$colegiado) {
            $this->setFlash('error', 'Colegiado no encontrado');
            $this->redirect('colegiados');
        }

        $this->view('colegiados/edit', [
            'colegiado' => $colegiado
        ]);
    }

    /**
     * Actualizar colegiado
     */
    public function update($id) {
        $this->requirePermission('colegiados');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('colegiados');
        }

        $colegiadoAnterior = $this->colegiadoModel->find($id);

        if (!$colegiadoAnterior) {
            $this->setFlash('error', 'Colegiado no encontrado');
            $this->redirect('colegiados');
        }

        $data = [
            'especialidad' => $this->post('especialidad'),
            'universidad' => $this->post('universidad'),
            'fecha_graduacion' => $this->post('fecha_graduacion'),
            'numero_titulo' => $this->post('numero_titulo'),
            'tipo_colegiatura' => $this->post('tipo_colegiatura'),
            'estado' => $this->post('estado'),
            'observaciones' => $this->post('observaciones')
        ];

        // Validar
        $errors = $this->validate($data, [
            'especialidad' => 'required|min:3',
            'universidad' => 'required|min:3',
            'fecha_graduacion' => 'required'
        ]);

        if (!empty($errors)) {
            $this->view('colegiados/edit', [
                'errors' => $errors,
                'colegiado' => array_merge($colegiadoAnterior, $data)
            ]);
            return;
        }

        // Actualizar
        if ($this->colegiadoModel->update($id, $data)) {
            logAudit('colegiados', $id, 'UPDATE', $colegiadoAnterior, $data);
            $this->setFlash('success', 'Colegiado actualizado correctamente');
            $this->redirect('colegiados');
        } else {
            $this->setFlash('error', 'Error al actualizar el colegiado');
            $this->redirect('colegiados/edit/' . $id);
        }
    }

    /**
     * Ver detalle de colegiado
     */
    public function show($id) {
        $this->requirePermission('colegiados');

        $colegiado = $this->colegiadoModel->getWithPersona($id);

        if (!$colegiado) {
            $this->setFlash('error', 'Colegiado no encontrado');
            $this->redirect('colegiados');
        }

        // Obtener estadísticas
        $estadisticas = $this->colegiadoModel->getEstadisticas($id);

        // Obtener aportaciones
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT a.*, ta.nombre as tipo_nombre
                             FROM aportaciones a
                             INNER JOIN tipos_aportacion ta ON a.tipo_aportacion_id = ta.id
                             WHERE a.colegiado_id = ?
                             ORDER BY a.periodo DESC");
        $stmt->execute([$id]);
        $aportaciones = $stmt->fetchAll();

        // Obtener pagos
        $stmt = $db->prepare("SELECT p.*, u.username as usuario_nombre
                             FROM pagos p
                             INNER JOIN usuarios u ON p.usuario_id = u.id
                             WHERE p.colegiado_id = ?
                             ORDER BY p.fecha_pago DESC");
        $stmt->execute([$id]);
        $pagos = $stmt->fetchAll();

        $this->view('colegiados/view', [
            'colegiado' => $colegiado,
            'estadisticas' => $estadisticas,
            'aportaciones' => $aportaciones,
            'pagos' => $pagos
        ]);
    }

    /**
     * Cambiar estado
     */
    public function changeStatus($id) {
        $this->requirePermission('colegiados');

        $colegiado = $this->colegiadoModel->find($id);

        if (!$colegiado) {
            $this->json(['success' => false, 'message' => 'Colegiado no encontrado'], 404);
        }

        $nuevoEstado = $this->post('estado');

        if ($this->colegiadoModel->cambiarEstado($id, $nuevoEstado)) {
            logAudit('colegiados', $id, 'UPDATE', ['estado' => $colegiado['estado']], ['estado' => $nuevoEstado]);
            $this->json(['success' => true, 'message' => 'Estado actualizado correctamente']);
        } else {
            $this->json(['success' => false, 'message' => 'Error al actualizar el estado'], 500);
        }
    }
}

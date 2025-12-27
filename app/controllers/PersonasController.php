<?php
/**
 * Controlador de Personas
 */
class PersonasController extends Controller {

    private $personaModel;

    public function __construct() {
        $this->personaModel = $this->model('Persona');
    }

    /**
     * Listar personas
     */
    public function index() {
        $this->requirePermission('personas');

        $filters = [
            'numero_documento' => $this->get('numero_documento'),
            'nombres' => $this->get('nombres'),
            'estado' => $this->get('estado')
        ];

        $personas = $this->personaModel->search($filters);

        $this->view('personas/index', [
            'personas' => $personas,
            'filters' => $filters
        ]);
    }

    /**
     * Mostrar formulario de creación
     */
    public function create() {
        $this->requirePermission('personas');

        $this->view('personas/create');
    }

    /**
     * Guardar nueva persona
     */
    public function store() {
        $this->requirePermission('personas');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('personas');
        }

        $data = [
            'tipo_documento' => $this->post('tipo_documento'),
            'numero_documento' => $this->post('numero_documento'),
            'nombres' => $this->post('nombres'),
            'apellido_paterno' => $this->post('apellido_paterno'),
            'apellido_materno' => $this->post('apellido_materno'),
            'fecha_nacimiento' => $this->post('fecha_nacimiento'),
            'genero' => $this->post('genero'),
            'email' => $this->post('email'),
            'telefono' => $this->post('telefono'),
            'celular' => $this->post('celular'),
            'direccion' => $this->post('direccion'),
            'distrito' => $this->post('distrito'),
            'provincia' => $this->post('provincia'),
            'departamento' => $this->post('departamento'),
            'pais' => $this->post('pais', 'Perú')
        ];

        // Validar
        $errors = $this->validate($data, [
            'numero_documento' => 'required|min:8|max:20',
            'nombres' => 'required|min:2|max:100',
            'apellido_paterno' => 'required|min:2|max:100',
            'apellido_materno' => 'required|min:2|max:100',
            'fecha_nacimiento' => 'required',
            'genero' => 'required',
            'email' => 'required|email'
        ]);

        // Verificar si el documento ya existe
        if ($this->personaModel->documentoExists($data['numero_documento'])) {
            $errors['numero_documento'][] = 'El número de documento ya está registrado';
        }

        if (!empty($errors)) {
            $this->view('personas/create', [
                'errors' => $errors,
                'data' => $data
            ]);
            return;
        }

        // Guardar
        $id = $this->personaModel->insert($data);

        if ($id) {
            logAudit('personas', $id, 'INSERT', null, $data);
            $this->setFlash('success', 'Persona registrada correctamente');
            $this->redirect('personas');
        } else {
            $this->setFlash('error', 'Error al registrar la persona');
            $this->redirect('personas/create');
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id) {
        $this->requirePermission('personas');

        $persona = $this->personaModel->find($id);

        if (!$persona) {
            $this->setFlash('error', 'Persona no encontrada');
            $this->redirect('personas');
        }

        $this->view('personas/edit', [
            'persona' => $persona
        ]);
    }

    /**
     * Actualizar persona
     */
    public function update($id) {
        $this->requirePermission('personas');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('personas');
        }

        $personaAnterior = $this->personaModel->find($id);

        if (!$personaAnterior) {
            $this->setFlash('error', 'Persona no encontrada');
            $this->redirect('personas');
        }

        $data = [
            'tipo_documento' => $this->post('tipo_documento'),
            'numero_documento' => $this->post('numero_documento'),
            'nombres' => $this->post('nombres'),
            'apellido_paterno' => $this->post('apellido_paterno'),
            'apellido_materno' => $this->post('apellido_materno'),
            'fecha_nacimiento' => $this->post('fecha_nacimiento'),
            'genero' => $this->post('genero'),
            'email' => $this->post('email'),
            'telefono' => $this->post('telefono'),
            'celular' => $this->post('celular'),
            'direccion' => $this->post('direccion'),
            'distrito' => $this->post('distrito'),
            'provincia' => $this->post('provincia'),
            'departamento' => $this->post('departamento'),
            'pais' => $this->post('pais', 'Perú')
        ];

        // Validar
        $errors = $this->validate($data, [
            'numero_documento' => 'required|min:8|max:20',
            'nombres' => 'required|min:2|max:100',
            'apellido_paterno' => 'required|min:2|max:100',
            'apellido_materno' => 'required|min:2|max:100',
            'fecha_nacimiento' => 'required',
            'genero' => 'required',
            'email' => 'required|email'
        ]);

        // Verificar si el documento ya existe (excluyendo el actual)
        if ($this->personaModel->documentoExists($data['numero_documento'], $id)) {
            $errors['numero_documento'][] = 'El número de documento ya está registrado';
        }

        if (!empty($errors)) {
            $this->view('personas/edit', [
                'errors' => $errors,
                'persona' => array_merge(['id' => $id], $data)
            ]);
            return;
        }

        // Actualizar
        if ($this->personaModel->update($id, $data)) {
            logAudit('personas', $id, 'UPDATE', $personaAnterior, $data);
            $this->setFlash('success', 'Persona actualizada correctamente');
            $this->redirect('personas');
        } else {
            $this->setFlash('error', 'Error al actualizar la persona');
            $this->redirect('personas/edit/' . $id);
        }
    }

    /**
     * Eliminar persona
     */
    public function delete($id) {
        $this->requirePermission('personas');

        $persona = $this->personaModel->find($id);

        if (!$persona) {
            $this->json(['success' => false, 'message' => 'Persona no encontrada'], 404);
        }

        // Verificar si está asociada a un colegiado
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM colegiados WHERE persona_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        if ($result['count'] > 0) {
            $this->json(['success' => false, 'message' => 'No se puede eliminar. La persona está asociada a un colegiado'], 400);
        }

        // Cambiar estado a INACTIVO en lugar de eliminar
        if ($this->personaModel->cambiarEstado($id, 'INACTIVO')) {
            logAudit('personas', $id, 'DELETE', $persona);
            $this->json(['success' => true, 'message' => 'Persona eliminada correctamente']);
        } else {
            $this->json(['success' => false, 'message' => 'Error al eliminar la persona'], 500);
        }
    }

    /**
     * Ver detalle de persona
     */
    public function view($id) {
        $this->requirePermission('personas');

        $persona = $this->personaModel->find($id);

        if (!$persona) {
            $this->setFlash('error', 'Persona no encontrada');
            $this->redirect('personas');
        }

        // Verificar si es colegiado
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM colegiados WHERE persona_id = ?");
        $stmt->execute([$id]);
        $colegiado = $stmt->fetch();

        $this->view('personas/view', [
            'persona' => $persona,
            'colegiado' => $colegiado
        ]);
    }
}

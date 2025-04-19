<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InventarioModel;
use App\Models\ArticulosModel;
// Quita Dompdf si no lo usas aquí
// use Dompdf\Dompdf;

class Existencias extends BaseController
{
    protected $inventarioModel;
    protected $articulosModel;
    protected $helpers = ['form', 'number']; // Cargar helpers para formularios y formato de números

    public function __construct()
    {
        // Cargar la instancia de la base de datos
        $this->db = \Config\Database::connect();
        $this->inventarioModel = new InventarioModel();
        $this->articulosModel = new ArticulosModel();
    }

    public function index()
    {
        // Obtener inventario con detalles del artículo usando el método del modelo
        $listaInventario = $this->inventarioModel->getInventarioConArticulos();

        // Calcular los valores para el dashboard
        $valorTotalInventario = 0; // Basado en precio público
        $valorNetoInventario = 0;  // Basado en precio proveedor (costo)
        $valorUtilidades = 0;      // Diferencia

        foreach ($listaInventario as $item) {
            $valorTotalItem = ($item['precio_pub'] ?? 0) * ($item['cantidad'] ?? 0);
            $valorNetoItem = ($item['precio_prov'] ?? 0) * ($item['cantidad'] ?? 0);

            $valorTotalInventario += $valorTotalItem;
            $valorNetoInventario += $valorNetoItem;
        }

        // La utilidad es la diferencia entre el valor total (venta) y el valor neto (costo)
        $valorUtilidades = $valorTotalInventario - $valorNetoInventario;

        $data = [
            'lista' => $listaInventario,
            'valor_total_inventario' => $valorTotalInventario,
            'valor_neto_inventario'  => $valorNetoInventario,
            'valor_utilidades'       => $valorUtilidades,
            'titulo_pagina' => 'Gestión de Existencias' // Ejemplo de título
        ];

        return view('Panel/existencias', $data);
    }

    // Muestra el formulario para añadir una nueva entrada de inventario
    public function nuevo()
    {
        // Obtener IDs de artículos YA en inventario
        $articulosEnInventarioIds = array_column($this->inventarioModel->select('id_articulo')->findAll(), 'id_articulo');

        // Iniciar la consulta para artículos disponibles
        $query = $this->articulosModel->where('stock', 1); // Solo los marcados para inventario

        // --- INICIO DE LA CORRECCIÓN ---
        // Aplicar el filtro whereNotIn SÓLO si hay IDs para excluir
        if (!empty($articulosEnInventarioIds)) {
            $query->whereNotIn('id_articulo', $articulosEnInventarioIds);
        }
        // Si $articulosEnInventarioIds está vacío, simplemente no se añade esta condición,
        // lo cual es correcto, ya que no necesitamos excluir ningún artículo.
        // --- FIN DE LA CORRECCIÓN ---

        // Continuar construyendo la consulta y ejecutarla
        $articulosDisponibles = $query->orderBy('nombre', 'ASC')->findAll();

        // Mensaje si no hay artículos disponibles (ya sea porque no hay con stock=1 o porque todos están en inventario)
        if (empty($articulosDisponibles)) {
             session()->setFlashdata('warning', 'No hay artículos nuevos (con stock habilitado) disponibles para añadir al inventario, o todos los artículos habilitados ya están registrados.');
            // Puedes decidir si aún quieres mostrar el formulario vacío o redirigir
            // return redirect()->to('/admin/existencias')->with('warning', 'Mensaje...'); // Ejemplo de redirección
        }

        $data = [
            'articulos' => $articulosDisponibles,
            'validation' => $this->validator, // Pasar el objeto de validación
            'titulo_pagina' => 'Añadir Artículo al Inventario'
        ];
        return view('Panel/inventario_form', $data); // Usaremos una vista de formulario reutilizable
    }

    // Procesa el formulario de creación
    public function crear()
    {
        $rules = [
            'id_articulo' => 'required|is_natural_no_zero|is_not_unique[sellopro_articulos.id_articulo]', // Asegura que el artículo exista
            'cantidad'    => 'required|is_natural_no_zero|max_length[10]' // Cantidad inicial debe ser > 0
        ];

        // Mensajes personalizados (opcional)
        $messages = [
            'id_articulo' => [
                'required' => 'Debe seleccionar un artículo.',
                'is_not_unique' => 'El artículo seleccionado no es válido.',
                'is_natural_no_zero' => 'Debe seleccionar un artículo.'
            ],
            'cantidad' => [
                'required' => 'La cantidad es obligatoria.',
                'is_natural_no_zero' => 'La cantidad debe ser un número mayor que cero.',
                'max_length' => 'La cantidad es demasiado grande.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            // Si la validación falla, volvemos al formulario mostrando errores
            // Usamos withInput() para repoblar el formulario con los datos enviados
            // return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
             // Mejor llamar a nuevo() directamente para pasarle los datos necesarios y la validación
             return $this->nuevo();
        }

        // Validación pasada, proceder a guardar
        $data = [
            'id_articulo' => $this->request->getPost('id_articulo'),
            'cantidad'    => $this->request->getPost('cantidad')
        ];

        // Verificamos si ya existe (aunque la lógica de 'nuevo' intenta prevenirlo, es una doble seguridad)
        $existe = $this->inventarioModel->findByArticuloId($data['id_articulo']);

        if ($existe) {
             session()->setFlashdata('error', 'Este artículo ya tiene un registro de inventario. Edite la cantidad existente.');
             return redirect()->to('/admin/existencias');
        }

        if ($this->inventarioModel->insert($data)) {
            session()->setFlashdata('success', 'Artículo añadido al inventario correctamente.');
        } else {
            session()->setFlashdata('error', 'No se pudo añadir el artículo al inventario.');
        }

        return redirect()->to('/existencias/existencias_admin');
    }


    // Muestra el formulario para editar la cantidad de un artículo en inventario
    public function editar($id_inventario = null)
    {
         if ($id_inventario === null) {
            session()->setFlashdata('error', 'ID de inventario no proporcionado.');
            return redirect()->to('/admin/existencias');
         }

        $inventario = $this->inventarioModel->find($id_inventario);

        if (!$inventario) {
            session()->setFlashdata('error', 'Registro de inventario no encontrado.');
            return redirect()->to('/admin/existencias');
        }

        // Obtener datos del artículo asociado para mostrarlos
        $articulo = $this->articulosModel->find($inventario['id_articulo']);

        $data = [
            'inventario' => $inventario,
            'articulo'   => $articulo, // Pasar datos del artículo a la vista
            'validation' => $this->validator, // Pasar objeto de validación
            'titulo_pagina' => 'Editar Cantidad en Inventario'
        ];
        return view('Panel/inventario_form', $data); // Reutilizamos la vista del formulario
    }

    // Procesa el formulario de actualización
    public function actualizar($id_inventario = null)
    {
        if ($id_inventario === null) {
            session()->setFlashdata('error', 'ID de inventario no proporcionado.');
            return redirect()->to('/admin/existencias');
         }

        // Regla de validación solo para la cantidad
        $rules = [
             // Permitimos 0 para poder poner un artículo a stock cero si se desea
            'cantidad' => 'required|is_natural|max_length[10]'
        ];
         $messages = [
            'cantidad' => [
                'required' => 'La cantidad es obligatoria.',
                'is_natural' => 'La cantidad debe ser un número igual o mayor que cero.',
                'max_length' => 'La cantidad es demasiado grande.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
             // Si falla, redirigir de vuelta al formulario de edición con errores
             // return redirect()->to('/admin/existencias/editar/'.$id_inventario)->withInput()->with('errors', $this->validator->getErrors());
             // Mejor llamar a editar() para pasar datos necesarios y la validación
             return $this->editar($id_inventario);
        }

        // Validación pasada
        $data = [
            'cantidad' => $this->request->getPost('cantidad')
        ];

        // Antes de actualizar, verificamos si el registro realmente existe
        $inventario = $this->inventarioModel->find($id_inventario);
        if (!$inventario) {
            session()->setFlashdata('error', 'Registro de inventario no encontrado para actualizar.');
            return redirect()->to('/admin/existencias');
        }


        if ($this->inventarioModel->update($id_inventario, $data)) {
            session()->setFlashdata('success', 'Cantidad de inventario actualizada correctamente.');
        } else {
            session()->setFlashdata('error', 'No se pudo actualizar la cantidad de inventario.');
        }

        return redirect()->to('/admin/existencias');
    }

    // Procesa la eliminación (AJUSTADO A POST)
    public function eliminar($id_entrada = null)
    {
        if ($id_entrada === null) {
            session()->setFlashdata('error', 'ID de inventario no proporcionado.');
            return redirect()->to('/existencias/existencias_admin');
         }

        // Verificar que la solicitud sea POST para seguridad (previene CSRF simple)
        if (!$this->request->is('post')) {
             return redirect()->to('/existencias/existencias_admin')->with('error', 'Acción no permitida.');
        }

        // Podrías añadir una verificación CSRF más robusta si es necesario aquí

        $inventario = $this->inventarioModel->find($id_entrada);
        if (!$inventario) {
            session()->setFlashdata('error', 'Registro de inventario no encontrado para eliminar.');
            return redirect()->to('/existencias/existencias_admin');
        }


        if ($this->inventarioModel->delete($id_entrada)) {
            session()->setFlashdata('success', 'Registro de inventario eliminado correctamente.');
        } else {
            // Comprobar si hay errores específicos del modelo, si los hubiera
            session()->setFlashdata('error', 'No se pudo eliminar el registro de inventario.');
        }

        return redirect()->to('/existencias/existencias_admin');
    }
    public function edicion_rapida($id)
    {
        // Obtener el registro de inventario
        $inventario = $this->inventarioModel->where('id_entrada', $id)->first();
        
        if (empty($inventario)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se encontró el registro de inventario',
                'flag' => 0
            ]);
        }

        // Obtener el artículo relacionado
        $articulo = $this->articulosModel->where('id_articulo', $inventario['id_articulo'])->first();
        
        if (empty($articulo)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se encontró el artículo relacionado',
                'flag' => 0
            ]);
        }

        // Retornar los datos necesarios
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Consulta realizada con éxito',
            'flag' => 1,
            'data' => [
                'id_entrada' => $inventario['id_entrada'],
                'cantidad' => $inventario['cantidad'],
                'minimo' => $articulo['minimo'],
                'id_articulo' => $inventario['id_articulo'] // Opcional, por si lo necesitas
            ]
        ]);
    }
    public function guardar_rapido() {
        
        $id_inventario     = $this->request->getVar('id_entrada');
        $data['cantidad']  = $this->request->getVar('cantidad');

        //guardar en la tabla articulos
        $actualizar_inventario  = $this->inventarioModel->update($id_inventario,$data);

        if ($actualizar_inventario == true){
             return $this->response->setJSON([
                 'status'=>'success',
                 'message'=>'Se actualizaron los datos correctamente',
                 'flag'=>1
             ]);
         } 


    }
}
<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="container mt-3" id="app">

    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mt-2" id="alert-envio" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show mt-2" id="alert-envio" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="my-card d-flex justify-content-between">
        <h2>Lista de Cotizaciones</h2>
        <!-- Button trigger modal -->
        <button type="button" class="btn-my" data-bs-toggle="modal" data-bs-target="#exampleModal"><span class="bi bi-file-earmark-plus"></span> Crear Cotización</button>
    </div>
    <div class="responsive-table-container">
        <table class="advanced-responsive-table" id="example">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Whatsapp</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cotizaciones as $data): ?>
                <tr>
                    <td data-label="ID"><?php echo $data['id_cotizacion'] ?></td>
                    <td data-label="Nombre"><?php echo $data['nombre'] ?></td>
                    <td data-label="Email"><?php echo $data['correo'] ?></td>
                    <td data-label="Whatsapp"><?php echo $data['telefono'] ?></td>
                    <td data-label="Monto"><?php echo $data['total'] ?></td>
                    <td data-label="Estado">
                        <?php if (!empty($data['factura_id'])): ?>
                            <span class="badge bg-success">Facturada</span>
                        <?php else: switch($data['estatus'] ?? 0):
                            case 1: ?>
                                <span class="badge bg-primary">Enviada</span>
                            <?php break; case 2: ?>
                                <span class="badge bg-success">Pagada</span>
                            <?php break; case 3: ?>
                                <span class="badge bg-warning text-dark">Entregada</span>
                            <?php break; default: ?>
                                <span class="badge bg-secondary">Pendiente</span>
                        <?php endswitch; endif; ?>
                    </td>
                    <td data-label="Acciones">
                        <!-- ver la cotización -->
                        <a href="<?php echo base_url('pagina_cotizador/'.$data['slug']); ?>" class="btn btn-view"><i class="bi bi-eye"></i></a>
                        <a href="<?php echo base_url('enviar_pdf/'.$data['id_cotizacion']); ?>" class="btn btn-edit" onclick="return confirm('¿Deseas enviar esta cotización?');" ><i class="bi bi-send"></i></a>
                        <a href="<?php echo base_url('eliminar_cotizacion/'.$data['id_cotizacion']); ?>" class="btn btn-delete" onclick="return confirm('Esta eliminación no se puede revertir, ¿Deseas continuar?');"><i class="bi bi-trash3"></i></a>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<!-- El resto de tu código modal permanece igual -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog rounded-0">
    <div class="modal-content rounded-0">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Agregar Cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table id="modal" class="table table-bordered w-100">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Tipo Venta</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                <tr class="w-100">
                    <td><?php echo $cliente['nombre'] ?></td>
                    <td>
                        <select class="form-select tipo-venta-select" data-cliente="<?php echo $cliente['id_cliente'] ?>">
                            <option value="1">Sello completo</option>
                            <option value="2">Mecanismos</option>
                        </select>
                    </td>
                    <td>
                        <a class="btn-my crear-cotizacion" href="#" 
                           data-cliente="<?php echo $cliente['id_cliente'] ?>" 
                           class="my-btn-primary p-1">
                            <span class="bi bi-check"></span>
                        </a>
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="my-btn-danger p-2" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar el click en el botón de crear cotización
    document.querySelectorAll('.crear-cotizacion').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const clienteId = this.getAttribute('data-cliente');
            const row = this.closest('tr');
            const tipoVenta = row.querySelector('.tipo-venta-select').value;
            
            // Redirigir al controlador con ambos parámetros
            window.location.href = `/nueva_cotizacion/${clienteId}?tipo_venta=${tipoVenta}`;
        });
    });
});
$( document ).ready(function() {
    new DataTable('#modal');
    new DataTable('#example');
});
</script>
<script type="" src="<?php echo base_url('public/js/cotizaciones.js'); ?>"></script>
<script>
    const alertEnvio = document.getElementById('alert-envio');
    if (alertEnvio) setTimeout(() => bootstrap.Alert.getOrCreateInstance(alertEnvio).close(), 4000);
</script>
<?php echo $this->endSection(); ?>
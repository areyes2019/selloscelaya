<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="container mt-3" id="app">
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
                    <th>Whhatsapp</th>
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
                    <td data-label="Whhatsapp"><?php echo $data['telefono'] ?></td>
                    <td data-label="Monto"><?php echo $data['total'] ?></td>
                    <td data-label="Estado">Enviada</td>
                    <td data-label="Acciones">
                        <!-- ver la cotización -->
                        <a href="<?php echo base_url('pagina_cotizador/'.$data['slug']); ?>" class="btn btn-view"><i class="fas fa-eye"></i></a>
                        <a href="<?php echo base_url('enviar_pdf/'.$data['id_cotizacion']); ?>" class="btn btn-edit" onclick="return confirm('¿Deseas enviar esta cotización?');" ><i class="bi bi-send"></i></a>
                        <a href="<?php echo base_url('eliminar_cotizacion/'.$data['id_cotizacion']); ?>" class="btn btn-delete" onclick="return confirm('Esta eliminación no se puede revertir, ¿Deseas continuar?');"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>


<!-- Modal -->
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
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                <tr class="w-100">
                    <td><?php echo $cliente['nombre'] ?></td>
                    <td>
                        <a class="btn-my" href="/nueva_cotizacion/<?php echo $cliente['id_cliente']?>"  class="my-btn-primary p-1"><span class="bi bi-check"></span></a>
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
    $( document ).ready(function() {
        new DataTable('#modal');
        new DataTable('#example');
    });
</script>
<script type="" src="<?php echo base_url('public/js/cotizaciones.js'); ?>"></script>
<?php echo $this->endSection(); ?>
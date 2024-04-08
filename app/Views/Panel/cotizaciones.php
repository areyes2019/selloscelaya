<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="container mt-3">
	<div class="my-card d-flex justify-content-between">
		<h2>Lista de Cotizaciones</h2>
        <!-- Button trigger modal -->
        <button type="button" class="my-btn-primary p-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
          Nueva Cotización
        </button>
       
	</div>
	<div class="my-card mt-3">
		<table id="example" class="table table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Numero WhatsApp</th>
                    <th>Fecha</th>
                    <th>Estatus</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Shad Roland</td>
                    <td>461 256 2563</td>
                    <td>5 de marzo de 2024</td>
                    <td>Enviada</td>
                    <td>
                        <a href="#" class="btn btn-sm rounded-0 my-btn-danger"><span class="bi bi-trash3"></span></a>
                        <a href="<?php echo base_url('editar_cotizacion'); ?>" class="btn btn-sm rounded-0 my-btn-success"><span class="bi bi-pencil"></span></a>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th>Nombre</th>
                    <th>Numero WhatsApp</th>
                    <th>Fecha</th>
                    <th>Estatus</th>
                    <th>Acción</th>
                </tr>
            </tfoot>
        </table>
	</div>
</div>

<div id="app"></div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog rounded-0">
    <div class="modal-content rounded-0">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Agregar Cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table id="modal" class="table table-bordered table-responsive" style="width:100%">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>WhatsApp</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td class="w-50"><?php echo $cliente['nombre'] ?></td>
                        <td><?php echo $cliente['telefono'] ?></td>
                        <td>
                            <a href="/nueva_cotizacion/<?php echo $cliente['idCliente']?>"  class="my-btn-primary p-1"><span class="bi bi-check"></span></a>
                        </td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="my-btn-primary p-2">Guardar</button>
        <button type="button" class="my-btn-danger p-2" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<script>
    new DataTable('#modal');
</script>
<script type="" src="<?php echo base_url('public/js/cotizaciones.js'); ?>"></script>
<?php echo $this->endSection(); ?>
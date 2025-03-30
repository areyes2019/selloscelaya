<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="container mt-3">
	<div class="my-card d-flex justify-content-between">
		<h2>Ordenes de Compra</h2>
        <!-- Button trigger modal -->
        <button  class="btn btn-primary btn-icon-split" data-toggle="modal" data-target="#exampleModal">
            <span class="icon text-white-50">
                <i class="bi bi-plus-circle"></i>
            </span>
            <span class="text">Nueva Orden de Compra</span>
        </button>
       
	</div>
	<div class="my-card mt-3">
		<table id="example" class="table table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Proveedor</th>
                    <th>Fecha</th>
                    <th>Estatus</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $data): ?>
                <tr>
                    <td><?php echo $data['pedidos_id'] ?></td>
                    <td><?php echo $data['empresa'] ?></td>
                    <td><?php echo $data['fecha'] ?></td>
                    <td>
                        <?php if ( $data['pagado'] == "0" ) : ?>
                        <p class="m-0"><span class="badge badge-primary">Por pagar</span></p>
                        <?php elseif ($data['pagado']==1 && $data['recibido']==0):?>
                        <p class="m-0"><span class="badge badge-danger">En Curso</span></p>
                        <?php elseif($data['recibido']==1):?>
                        <p class="m-0"><span class="badge badge-success">Recibido</span></p>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a class="btn btn-primary btn-circle btn-sm" href="<?php echo base_url('pagina_orden/'.$data['slug']); ?>" class="btn btn-sm rounded-0 my-btn-success"><span class="bi bi-pencil"></span></a>
                        <a class="btn btn-danger btn-circle btn-sm" href="<?php echo base_url('eliminar_cotizacion/'.$data['pedidos_id']); ?>" class="btn btn-sm rounded-0 my-btn-danger" onclick="return confirm('Esta eliminación no se puede revertir, ¿Deseas continuar?');"><span class="bi bi-trash3"></span></a>
                    </td>
                </tr>
                <?php endforeach ?>
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
        <h5 class="modal-title" id="exampleModalLabel">Seleccionar Proveedor</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="modal" class="table table-bordered w-100" style="width:100%">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>WhatsApp</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($proveedor as $prov): ?>
                    <tr>
                        <td class="w-50"><?php echo $prov['empresa'] ?></td>
                        <td><?php echo $prov['telefono'] ?></td>
                        <td>
                            <a href="/nueva_compra/<?php echo $prov['id_proveedor']?>"  class="btn btn-circle btn-primary btn-sm"><span class="bi bi-check"></span></a>
                        </td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-icon-split" data-dismiss="modal">
            <span class="icon text-white-50">
                <i class="bi bi-box-arrow-right"></i>
            </span>
            <span class="text">Cerrar</span>
        </button>
      </div>
    </div>
  </div>
</div>
<script>
    new DataTable('#modal');
</script>
<script type="" src="<?php echo base_url('public/js/cotizaciones.js'); ?>"></script>
<?php echo $this->endSection(); ?>
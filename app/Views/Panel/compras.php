<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="container mt-3">
    <?php 
    $request = \Config\Services::request();
    if ($request->getGet('alert_type') && $request->getGet('alert_message')): 
    ?>
    <div class="alert alert-<?= esc($request->getGet('alert_type')) ?> alert-dismissible fade show" role="alert">
        <?= esc($request->getGet('alert_message')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
	<div class="card rounded-0 shadow-sm">
        <div class="car-body d-flex align-items-center justify-content-between">
    		<h2>Ordenes de Compra</h2>
            <!-- Button trigger modal -->
            <button  class="btn btn-primary btn-icon-split" data-bs-toggle="modal" data-bs-target="#exampleModal">
                <span class="icon text-white-50">
                    <i class="bi bi-plus-circle"></i>
                </span>
                <span class="text">Nueva Orden de Compra</span>
            </button>
        </div>
       
	</div>
	<div class="card mt-3 rounded-0 shadow-sm">
		<table id="example" class="table table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Proveedor</th>
                    <th>Fecha</th>
                    <th>Estatus</th>
                    <th>Monto</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $data): ?>
                <tr>
                    <td><?php echo $data['id_pedido'] ?></td>
                    <td><?php echo $data['empresa'] ?></td>
                    <td><?php echo date('d-m-Y', strtotime($data['created_at'])); ?></td>
                    <td>
                        <?php if ($data['pagado'] == "0" && $data['entregada'] == "0") : ?>
                            <span class="badge bg-secondary">Borrador</span>
                        <?php elseif ($data['pagado'] == "1" && $data['entregada'] == "0") : ?>
                            <span class="badge bg-warning text-dark">Pagada</span>
                        <?php elseif ($data['entregada'] == "1") : ?>
                            <span class="badge bg-success">Entregada</span>
                        <?php else : ?>
                            <span class="badge bg-danger">Estado desconocido</span>
                        <?php endif; ?>
                    </td>
                    <td>$<?php echo $data['total'] ?></td>
                    <td>
                        <a class="btn btn-primary btn-circle btn-sm" href="<?php echo base_url('pagina_orden/'.$data['slug']); ?>">
                            <span class="bi bi-pencil"></span>
                        </a>
                        <?php if ($data['pagado'] == "0"): // Solo mostrar botón eliminar si NO está pagado ?>
                            <a class="btn btn-danger btn-circle btn-sm" href="<?php echo base_url('eliminar_compra/'.$data['id_pedido']); ?>" onclick="return confirm('Esta eliminación no se puede revertir, ¿Deseas continuar?');">
                                <span class="bi bi-trash3"></span>
                            </a>
                        <?php endif; ?>
                        <?php if ($data['pagado'] == 1 && $data['entregada'] == 0): ?>
                            <form action="<?php echo base_url('recibido_compras'); ?>" method="post" class="d-inline">
                                <input type="hidden" name="pedido" value="<?= $data['id_pedido'] ?>">
                                <button type="submit" class="btn btn-success btn-circle btn-sm" onclick="return confirm('¿Confirmas que has recibido esta orden de compra?');">
                                    <span class="bi bi-box-arrow-down"></span>
                                </button>
                            </form>
                        <?php endif; ?>
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
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
        <button type="button" class="btn btn-danger btn-icon-split" data-bs-dismiss="modal">
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
    new DataTable('#example');
</script>
<script type="" src="<?php echo base_url('public/js/cotizaciones.js'); ?>"></script>
<?php echo $this->endSection(); ?>
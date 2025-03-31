<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="midde_cont">
    <div class="container-fluid">
        <div class="row column_title">
            <div class="col-md-12">
                <div class="page_title">
                    <h2>Lista de precios</h2>
                </div>
                <!-- Mensajes Flash -->
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- row -->
        <div class="row">
            <!-- table section -->
            <div class="col-md-12">
                <div class="white_shd full margin_bottom_30">
                    <div class="full graph_head">
                        <div class="heading1 margin_0">
                            <button class="btn btn-danger rounded-0" data-bs-toggle="modal" data-bs-target="#nuevo_cliente">Nuevo Artículo</button>
                        </div>
                        <form class="mb-3" action="<?= site_url('import_masivo') ?>" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="archivo_excel" class="form-label">Importar Masivo</label>
                                <input class="form-control" type="file" id="archivo_excel" name="archivo_excel" accept=".xlsx,.xls" required>
                                <div class="form-text">El archivo debe tener las columnas: Nombre, Modelo, Precio Proveedor, Precio Público, Mínimo, Stock y clave de producto</div>
                            </div>
                            <button type="submit" class="btn btn-primary">Importar</button>
                        </form>
                        <div class="modal fade" id="nuevo_cliente">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                <form action="<?php echo base_url('nuevo_articulo'); ?>" method="post">
                                    <label for="">Nombre</label>
                                    <input type="text" class="my-input w-100" name="nombre">
                                    <label for="">Modelo</label>
                                    <input type="text" class="my-input w-100" name="modelo">
                                    <label for="">Precio Proveedor</label>
                                    <input type="text" class="my-input w-100" name="precio_prov">
                                    <label for="">Precio Público</label>
                                    <input type="text" class="my-input w-100" name="precio_pub">
                                    <input type="submit" value="Guardar" class="my-btn-primary p-2 mt-2 w-100">
                                </form>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary">Save changes</button>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                    <div class="table_section padding_infor_info">
                        <div class="table-responsive-sm">
                            <table id="example" class="table table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nombre</th>
                                        <th>Modelo</th>
                                        <th>Costo</th>
                                        <th>Precio Distribuidor</th>
                                        <th>Precio Público</th>
                                        <th>Beneficio</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($articulos as $articulo): ?>
                                    <tr>
                                        <td><?= $articulo['id_articulo'];?></td>
                                        <td><?= $articulo['nombre'];?></td>
                                        <td><?= $articulo['modelo'];?></td>
                                        <td>$<?= $articulo['precio_prov'];?></td>
                                        <td><strong class="text-primary">$<?= $articulo['precio_dist'];?></strong></td>
                                        <td><strong>$<?= $articulo['precio_pub'];?></strong></td>
                                        <td>$<?= $articulo['precio_pub'] - $articulo['precio_prov'];?></td>
                                        <td>
                                            <a href="eliminar_articulo/<?php echo $articulo['id_articulo']  ?>" onclick="return confirm('¿Seguro que quieres eliminar este registro?')" class="btn btn-sm rounded-0 my-btn-danger">Eliminar</a>
                                            <a href="editar_articulo/<?php echo $articulo['id_articulo'] ?>" class="btn btn-sm rounded-0 my-btn-success">Editar</a>
                                        </td>
                                    </tr>
                                    <?php endforeach;?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Modelo</th>
                                        <th>Precio Proveedor</th>
                                        <th>Precio Público</th>
                                        <th>Beneficio</th>
                                        <th>Acción</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    $( document ).ready(function() {
        new DataTable('#example');
    });
    </script>
<?php echo $this->endSection()?>
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
                            <a class="btn btn-danger rounded-0" href="<?php echo base_url('nuevo_art_vista'); ?>">Agregar Articulo</a>
                            <button class="btn btn-danger rounded-0" data-bs-toggle="modal" data-bs-target="#nuevo_cliente">Nuevo Artículo Rápido</button>
                        </div>
                        <h4 class="mt-3">Importacion maisva</h4>
                        <form action="<?= base_url('import_masivo') ?>" method="post" enctype="multipart/form-data">
        
                            <div class="drop-zone mb-3" id="dropZone">
                                <p id="dropZoneText">Arrastra y suelta tu archivo aquí o haz clic para seleccionarlo</p>
                                <input type="file" name="archivo_excel" class="d-none" id="fileInput" accept=".xlsx,.xls" required>
                            </div>

                            <div id="filePreview" class="text-success fw-bold"></div>

                            <small class="form-text text-muted d-block mb-3">
                                Debe contener exactamente 8 columnas en este orden:<br>
                                Nombre, Modelo, Precio Proveedor, Mínimo, Stock, Clave, Nombre Imagen, Disponible
                            </small>

                            <!-- Botón alineado a la izquierda -->
                            <button type="submit" class="btn btn-primary mt-3">Importar</button>
                        </form>

                    </div>
                    <div class="table_section padding_infor_info">
                        <div class="table-responsive-sm">
                            <table id="example" class="table table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Img</th>
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
                                        <td><img src="<?= base_url('ver_imagen/'.$articulo['img']) ?>" alt="Imagen" width="30"></td>
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
</div>
<script>
$( document ).ready(function() {
    new DataTable('#example');
});
</script>
<script type="text/javascript" src="<?php echo base_url('public/js/drag.js'); ?>"></script>
<?php echo $this->endSection()?>
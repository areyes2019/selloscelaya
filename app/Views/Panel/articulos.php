<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="container mt-3">
    <div class="row">
        <div class="col-md-3">
            <div class="my-card">
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
        </div>
        <div class="col-md-9 my-card">
            <h2>Lista de Artículos</h2>
            <table id="example" class="table table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Modelo</th>
                        <th>Precio Proveedor</th>
                        <th>Precio Público</th>
                        <th>Beneficio</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articulos as $articulo): ?>
                    <tr>
                        <td><?= $articulo['nombre'];?></td>
                        <td><?= $articulo['modelo'];?></td>
                        <td>$<?= $articulo['precio_prov'];?></td>
                        <td><strong>$<?= $articulo['precio_pub'];?></strong></td>
                        <td>$<?= $articulo['precio_pub'] - $articulo['precio_prov'];?></td>
                        <td>
                            <a href="eliminar_articulo/<?php echo $articulo['idArticulo']  ?>" onclick="return confirm('¿Seguro que quieres eliminar este registro?')" class="btn btn-sm rounded-0 my-btn-danger"><span class="bi bi-trash3"></span></a>
                            <a href="editar_articulo/<?php echo $articulo['idArticulo'] ?>" class="btn btn-sm rounded-0 my-btn-success"><span class="bi bi-pencil"></span></a>
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
<?php echo $this->endSection()?>
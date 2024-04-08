<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="container mt-3">
    <div class="row">
        <div class="col-md-3">
            <div class="my-card">
                <form action="<?php echo base_url('nuevo_cliente');?>" method="post">
                    <label for="">Nombre</label>
                    <input type="text" class="my-input w-100" name="nombre">
                    <label for="">Numero WhatsApp</label>
                    <input type="text" class="my-input w-100" name="telefono">
                    <input type="submit" value="Guardar" class="my-btn-primary p-2 mt-2 w-100">
                </form>
            </div>
        </div>
        <div class="col-md-9 my-card">
            <h3>Lista de Clientes</h3>
            <table id="example" class="table table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Numero WhatsApp</th>
                        <th>Correo</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?php echo $cliente['nombre'] ?></td>
                        <td><?php echo $cliente['telefono'] ?></td>
                        <?php if ($cliente['correo'] == null):?>
                        <td>No registrado</td>
                        <?php else:?>
                        <td><?php echo $cliente['correo'] ?></td>
                        <?php endif; ?>
                        <td>
                            <a href="eliminar_cliente/<?php echo $cliente['idCliente']  ?>" onclick="return confirm('¿Seguro que quieres eliminar este registro?')" class="btn btn-sm rounded-0 my-btn-danger"><span class="bi bi-trash3"></span></a>
                            <a href="editar_cliente/<?php echo $cliente['idCliente'] ?>" class="btn btn-sm rounded-0 my-btn-success"><span class="bi bi-pencil"></span></a>
                        </td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Nombre</th>
                        <th>Numero WhatsApp</th>
                        <th>Correo</th>
                        <th>Acción</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
</div>
<?php echo $this->endSection()?>

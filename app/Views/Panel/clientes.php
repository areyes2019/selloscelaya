<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="midde_cont">
    <div class="container-fluid">
        <div class="row column_title">
            <div class="col-md-12">
                <div class="page_title">
                    <h2>Clientes</h2>
                </div>
            </div>
        </div>
        <!-- row -->
        <div class="row">
            <!-- table section -->
            <div class="col-md-12">
                <div class="white_shd full margin_bottom_30">
                    <div class="full graph_head">
                        <div class="heading1 margin_0">
                            <h2>Lista de Clientes</h2>
                            <button class="btn btn-danger rounded-0" data-bs-toggle="modal" data-bs-target="#nuevo_cliente">Nuevo Cliente</button>
                        </div>
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
                                <form action="<?php echo base_url('nuevo_cliente');?>" method="post">
                                    <label for="">Nombre</label>
                                    <input type="text" class="my-input w-100" name="nombre">
                                    <label for="">Numero WhatsApp</label>
                                    <input type="text" class="my-input w-100" name="telefono">
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
                                            <a href="eliminar_cliente/<?php echo $cliente['id_cliente']  ?>" onclick="return confirm('¿Seguro que quieres eliminar este registro?')">Eliminar</a>
                                            <a href="editar_cliente/<?php echo $cliente['id_cliente'] ?>">Editar</a>
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
            </div>
        </div>
    </div>
<?php echo $this->endSection()?>

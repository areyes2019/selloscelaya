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
                                <h5 class="modal-title">Nuevo Cliente</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                <form action="<?php echo base_url('nuevo_cliente');?>" method="post">
                                    <?= csrf_field() ?>
                                    <label for="">Nombre</label>
                                    <input type="text" class="my-input w-100" name="nombre" required>
                                    <label for="">Tipo</label>
                                    <select class="my-input w-100" name="tipo" required>
                                        <option value="1">Cliente</option>
                                        <option value="2">Distribuidor</option>
                                    </select>
                                    <label for="">Número WhatsApp</label>
                                    <input type="text" class="my-input w-100" name="telefono">
                                    <input type="submit" value="Guardar" class="my-btn-primary p-2 mt-2 w-100">
                                </form>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                    <div class="table_section padding_infor_info">
                        <div class="table-responsive-sm">
                            <table id="tabla-clientes" class="table table-bordered" style="width:100%">
                                <thead>
                                    <!-- Fila 1: títulos de columna (usada para ordenar) -->
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th>Número WhatsApp</th>
                                        <th>Correo</th>
                                        <th>Acción</th>
                                    </tr>
                                    <!-- Fila 2: filtros por columna -->
                                    <tr>
                                        <th>
                                            <input type="text"
                                                   id="filtro-nombre"
                                                   class="form-control form-control-sm"
                                                   placeholder="Buscar nombre...">
                                        </th>
                                        <th></th>
                                        <th>
                                            <input type="text"
                                                   id="filtro-whatsapp"
                                                   class="form-control form-control-sm"
                                                   placeholder="Buscar número...">
                                        </th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($clientes as $cliente): ?>
                                    <tr>
                                        <td><?php echo esc($cliente['nombre']) ?></td>
                                        <td>
                                            <?php if($cliente['tipo'] == 1): ?>
                                                <span class="badge bg-primary">Cliente</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Distribuidor</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo esc($cliente['telefono']) ?></td>
                                        <td><?php echo $cliente['correo'] ? esc($cliente['correo']) : 'No registrado' ?></td>
                                        <td>
                                            <a href="#"
                                               data-delete-url="<?= base_url('eliminar_cliente/' . $cliente['id_cliente']) ?>"
                                               data-confirm="¿Seguro que quieres eliminar este cliente?"
                                               class="text-danger me-2">Eliminar</a>
                                            <a href="<?= base_url('editar_cliente/' . $cliente['id_cliente']) ?>">Editar</a>
                                        </td>
                                    </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {

    var tabla = $('#tabla-clientes').DataTable({
        orderCellsTop: true,   // ordena por la fila 1 del thead, no por los inputs
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        language: {
            lengthMenu:  'Mostrar _MENU_ registros',
            zeroRecords: 'No se encontraron clientes',
            info:        'Mostrando _START_ a _END_ de _TOTAL_ clientes',
            infoEmpty:   'Sin clientes registrados',
            infoFiltered:'(filtrado de _MAX_ clientes en total)',
            search:      'Búsqueda global:',
            paginate: {
                first:    '«',
                last:     '»',
                next:     '›',
                previous: '‹'
            }
        },
        columnDefs: [
            { orderable: false, targets: [1, 4] }   // Tipo y Acción no son ordenables
        ]
    });

    // Filtro columna Nombre (índice 0)
    $('#filtro-nombre').on('keyup change clear', function () {
        tabla.column(0).search(this.value).draw();
    });

    // Filtro columna Número WhatsApp (índice 2)
    $('#filtro-whatsapp').on('keyup change clear', function () {
        tabla.column(2).search(this.value).draw();
    });

});
</script>

<?php echo $this->endSection()?>

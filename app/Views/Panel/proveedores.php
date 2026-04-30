<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div id="app">
<div class="container-fluid">
    <div class="row column_title">
        <div class="col-md-12">
            <div class="page_title">
                <h2><span class="bi bi-person"></span>Proveedores</h2>
            </div>
        </div>
    </div>
    <!-- row -->
    <div class="row mt-4">
        <!-- table section -->
        <div class="col-md-12">
            <div class="white_shd full margin_bottom_30">
                <div class="full graph_head">
                    <div class="heading1 margin_0 mb-2">
                        <button class="btn btn-primary btn-icon-split" data-bs-toggle="modal" data-bs-target="#nuevo_cliente">
                            <span class="icon text-white-50">
                                <i class="fas fa-flag"></i>
                            </span>
                            <span class="text">Nuevo Proveedor</span>
                            
                        </button>
                    </div>
                    <div class="modal fade" id="nuevo_cliente">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content shadow-lg border-0">

                            <!-- Header -->
                            <div class="modal-header bg-light border-0">
                                <h5 class="modal-title fw-semibold">
                                <i class="bi bi-person-plus me-2"></i>Nuevo Proveedor
                                </h5>
                                <button type="button" class="btn-close" data-dismiss="modal"></button>
                            </div>

                            <!-- Body -->
                            <div class="modal-body px-4 py-3">
                                <form action="<?php echo base_url('nuevo_proveedor');?>" method="post">
                                <?= csrf_field() ?>
                                <div class="row g-3">

                                    <!-- Empresa -->
                                    <div class="col-md-6">
                                    <label class="form-label small text-muted">Empresa</label>
                                    <input type="text" class="form-control" name="empresa" placeholder="Ej. Sellos Express">
                                    </div>

                                    <!-- Contacto -->
                                    <div class="col-md-6">
                                    <label class="form-label small text-muted">Contacto</label>
                                    <input type="text" class="form-control" name="contacto" placeholder="Nombre del contacto">
                                    </div>

                                    <!-- Teléfono -->
                                    <div class="col-md-6">
                                    <label class="form-label small text-muted">WhatsApp</label>
                                    <input type="text" class="form-control" name="telefono" placeholder="Ej. 4611234567">
                                    </div>

                                    <!-- Correo -->
                                    <div class="col-md-6">
                                    <label class="form-label small text-muted">Correo</label>
                                    <input type="email" class="form-control" name="correo" placeholder="correo@ejemplo.com">
                                    </div>

                                    <!-- Descuento -->
                                    <div class="col-md-6">
                                    <label class="form-label small text-muted">Descuento (%)</label>
                                    <input type="number" step="0.01" class="form-control" name="descuento" placeholder="Ej. 10">
                                    </div>

                                    <!-- IVA -->
                                    <div class="col-md-6">
                                    <label class="form-label small text-muted">Tipo de precio</label>
                                    <select class="form-select" name="incluye_iva">
                                        <option value="0">Precio + IVA</option>
                                        <option value="1">Precio ya incluye IVA</option>
                                    </select>
                                    </div>

                                </div>

                                <!-- Botón -->
                                <div class="d-flex justify-content-end mt-4">
                                    <button class="btn btn-primary px-4">
                                    <i class="bi bi-save me-1"></i> Guardar proveedor
                                    </button>
                                </div>

                                </form>
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
                                    <th>Empresa</th>
                                    <th>Contacto</th>
                                    <th>Telefono</th>
                                    <th>Correo</th>
                                    <th>Descuento</th>
                                    <th>IVA</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($proveedores as $proveedor): ?>
                                <tr>
                                    <td><?php echo $proveedor['empresa'] ?></td>
                                    <td><?php echo $proveedor['contacto'] ?></td>
                                    <td><?php echo $proveedor['telefono'] ?></td>
                                    <td><?php echo $proveedor['correo'] ?></td>
                                    <td><?php echo $proveedor['descuento'] ?>%</td>
                                    <td>
                                        <?php echo $proveedor['incluye_iva'] ? 'Incluye IVA' : '+ IVA'; ?>
                                    </td>
                                    <td>
                                        <a class="btn btn-primary btn-circle btn-sm" href="editar_proveedor/<?php echo $proveedor['id_proveedor'] ?>"><span class="bi bi-pencil"></span></a>
                                        <a class="btn btn-danger btn-circle btn-sm" href="#"
                                           data-delete-url="<?= base_url('eliminar_proveedor/' . $proveedor['id_proveedor']) ?>"
                                           data-confirm="¿Seguro que quieres eliminar este proveedor?"><span class="bi bi-trash3"></span></a>
                                        <a href="#" class="btn btn-success btn-circle btn-sm" @click.prevent="ver_familias('<?php echo $proveedor['id_proveedor'] ?>')" ><span class="bi bi-share"></span></a>
                                    </td>
                                </tr>
                                <?php endforeach;?>
                            </tbody>
                        </table>
                        <!--Modal Familias-->
                        <div class="modal fade" id="familias" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                          <div class="modal-dialog">
                            <p ref="proveedor">{{proveedor}}</p>
                            <div class="modal-content rounded-0">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Familias</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                <button class="btn btn-primary btn-icon-split mb-3" data-toggle="collapse" data-target="#fam">
                                    <span class="icon text-white-50">
                                        <i class="bi bi-plus-circle"></i>
                                    </span>
                                    <span class="text">Agregar Familia</span>
                                </button>
                                <div class="collapse" id="fam">
                                    <div class="d-flex align-items-center">
                                        <input type="text" placeholder="Nombre de la familia" v-model="nombre">
                                        <input type="text" placeholder="Descuento %" v-model="descuento">
                                        <button type="submit" @click="agregar_familia()" class="btn btn-primary btn-sm rounded-0"><span class="bi bi-check"></span></button>
                                    </div>
                                </div>
                                <table class="table">
                                    <tr>
                                        <th>Nomre</th>
                                        <th>Descuento</th>
                                    </tr>
                                    <tr v-for = "familia in familias">
                                        <td>{{familia.nombre}}</td>
                                        <td>{{familia.descuento}}%</td>
                                    </tr>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
<script type="" src="<?php echo base_url('public/js/proveedores.js'); ?>"></script>
<?php echo $this->endSection()?>

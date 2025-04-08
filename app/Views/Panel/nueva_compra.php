<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div id="app">
    <div class="card shadow mb-4 rounded-0">
    <!-- Card Header - Dropdown -->
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between rounded-0">
        <h6 class="m-0 font-weight-bold text-primary">Orden de Compra: <span ref="pedido"><?php echo $pedidos_id?></span></h6>
        <div class="dropdown no-arrow">
            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink" style="">
                <div class="dropdown-header"></div>
                <a class="dropdown-item" href="<?php echo base_url('/descargar_orden/'.$pedidos_id); ?>"><span class="bi bi-download"></span> Descargar</a>
                <a class="dropdown-item" href="<?php echo base_url('/enviar_pdf/'.$pedidos_id); ?>"><span class="bi bi-send"></span> Enviar</a>
                <a :class="['dropdown-item']" v-if="display_pagado == 0" href="#" @click.prevent="agregar_pago"><span class="bi bi-credit-card"></span> Marcar pagado </a>
                <a :class="['dropdown-item']" v-if="display_recibido == 0" href="#" @click.prevent="recibida"><span class="bi bi-truck"></span> Marcar Recibido </a>
                <span class="d-none" ref="monto_total"><?= $suma_total?></span>
                <span class="d-none" ref="pedido" ><?= $pedidos_id ?></span>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?php echo base_url('/eliminar_cotizacion/'.$pedidos_id); ?>" onclick="return confirm('¿Estas seguro de querer eliminar esta cotización?');"><span class="bi bi-trash3"></span> Eliminar Cotización</a>
            </div>
        </div>
    </div>
    <!-- Card Body -->
    <div class="card-body rounded-0">
        <?php foreach ($proveedor as $data): ?>
        <p class="m-0"><strong>Para:</strong></p>
        <p class="m-0 d-none" ref="proveedor"><?php echo $data['id_proveedor']?></p>
        <p class="m-0"><?php echo $data['empresa']?></p>
        <p class="m-0">Tel: <?php echo $data['telefono']?></p>
        <p class="m-0"><?php echo $data['correo']?></p>
        <?php endforeach ?>
        <?php foreach ($pedido as $orden): ?>
        <?php endforeach ?>
    </div>
</div>
<div class="card shadow mb-4 rounded-0">
    <div class="card-body" v-if="display_pagado == 0">
        <!-- <button :class="['btn btn-primary', 'btn-icon-split']" data-bs-toggle="modal" data-bs-target="#agregar_articulo">
            <span class="icon text-white-50">
                <i class="bi bi-list-check"></i>
            </span>
            <span class="text">Articulo de Lista</span>
        </button> -->
        <div class="row">
            <div class="col-md-6">      
                <p class="mb-2">Agregar Artículos</p>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <select id="selectElement" class="form-control" placeholder="Seleccione un articulo" v-model="selectedArticulo">
                        <option value="" disabled selected>Seleccione un artículo</option>
                        <?php foreach ($articulos as $articulo): ?>
                        <option value="<?= $articulo['id_articulo'] ?>">
                            <?= $articulo['nombre'] ?> - <?= $articulo['modelo'] ?>
                        </option>
                        <?php endforeach ?>
                    </select>
                    <input type="number" class="form-control w-25" v-model="cantidad" min="1">
                    <button class="btn btn-primary rounded-0 btn-sm" @click="agregarArticulo">OK</button>
                </div>
            </div> 
        </div>
    </div>
</div>
<div class="card shadow mb-4 rounded-0">
    <div class="card-body">
        <table class="table mt-4">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Modelo</th>
                <th>Cantidad</th>
                <th>PU</th>
                <th>Total</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for = "dato in articulos ">
                <td>{{dato.nombre}}</td>
                <td>{{dato.modelo}}</td>
                <td v-if="display_pagado==0">
                    <input type="number" min="1" :value="dato.cantidad" style="width:90px" @change="modificar_cantidad(dato.id_detalle_pedido, $event)">
                </td>
                <td v-else>
                    {{dato.cantidad}}
                </td>
                <td>${{dato.p_unitario}}</td>
                <td>${{dato.total}}</td>
                <td><a v-if="display_pagado==0" href="#" @click.prevent="borrar_linea(dato.id_detalle_pedido)"><span class="bi bi-x-lg"></span></a></td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <th colspan="3"></th>
                <td><strong>Sub-Total</strong></th>
                <td>${{sub_total}}</td> 
                <td></td>
              </tr>
              <tr>
                <th colspan="3"></th>
                <td><strong>IVA</strong></th>
                <td>${{iva}}</td>
                <td></td>
              </tr>
              <tr>
                <th colspan="3"></th>
                <td><strong>Total</strong></th>
                <td>${{total}}</td>
                <td></td>
              </tr>
            </tfoot>
        </table>
         <!--  Modal agregar articulos -->  
        <div class="modal fade" id="agregar_articulo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content rounded-0">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Seleecione un artículo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table w-100" id="example">
                            <thead>
                                <tr>
                                    <th>Artículo</th>
                                    <th>Modelo</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($articulos as $articulo): ?>
                                <tr>
                                    <td><?php echo $articulo['nombre'] ?></td>
                                    <td><?php echo $articulo['modelo'] ?></td>
                                    <td>
                                        <button class="btn btn-primary btn-circle" @click="add_articulo(<?= $articulo['id_articulo'] ?>)"><span class="bi bi-check"></span></button>
                                    </td>
                                </tr>
                                <?php endforeach ?>
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
    </div>
</div>
</div>
<script src="<?php echo base_url('public/js/compras.js');?>"></script> 
<script src="<?php echo base_url('public/js/select.js');?>"></script> 
<?php echo $this->endSection()?>
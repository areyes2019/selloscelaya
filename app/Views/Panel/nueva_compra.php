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
                <a class="dropdown-item" href="<?php echo base_url('/enviar_pdf_orden/'.$pedidos_id); ?>"><span class="bi bi-send"></span> Enviar</a>
                <a href="" v-if="display_pagado == 0" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#misCuentas"><span class="bi bi-credit-card"></span> Marcar como pagado</a>
                <a 
                  :class="['dropdown-item']" 
                  v-if="display_recibido==0" 
                  href="#" 
                  @click.prevent="recibida"
                >
                  <span class="bi bi-truck"></span> Marcar como recibido
                </a>
                <span class="d-none" ref="monto_total"><?= $suma_total?></span>
                <span class="d-none" ref="pedido" ><?= $pedidos_id ?></span>
                <a class="dropdown-item" v-if="display_pagado == 0" href="<?php echo base_url('/eliminar_compra/'.$pedidos_id); ?>" onclick="return confirm('¿Estas seguro de querer eliminar esta cotización?');"><span class="bi bi-trash3"></span> Eliminar Cotización</a>
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
        <div class="row">
            <div class="col-md-6">      
                <p class="mb-2">Agregar Artículos</p>
                <div class="d-inline-flex">
                    <autocomplete-select
                      :options="lista"
                      v-model="selectedArticulo"
                      placeholder="Escribe para buscar..."
                    ></autocomplete-select>                
                    <input type="number" class="form-control w-25 m-0 ml-2 " v-model="cantidad" min="1">
                    <button class="btn btn-primary rounded-0 btn-sm" @click="agregarArticulo">OK</button>
                </div>
            </div>
            <div class="col-md-6">
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
        <!-- Botón para abrir el modal -->

        <!-- Modal -->
        <div class="modal fade" id="misCuentas" tabindex="-1">
          <div class="modal-dialog modal-sm">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="miModalLabel">Seleccionar el Banco</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="d-flex align-items-center">
                  <!-- Select -->
                    <select class="form-select flex-grow-1 me-2" v-model="cuentaSeleccionada">
                        <option disabled selected>Seleccione una cuenta...</option>
                        <?php foreach($cuentas_bancarias as $cuenta): ?>
                            <option :value="<?= $cuenta['id_cuenta'] ?>">
                                <?= esc($cuenta['banco']) ?> - (Saldo: $<?= number_format($cuenta['saldo'], 2) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger rounded-0"  @click="agregar_pago">Pagar</button>
              </div>
            </div>
          </div>
        </div>
    </div>
</div>
</div>
<script type="module" src="<?php echo base_url('public/js/compras.js');?>"></script>
<?php echo $this->endSection()?>
<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div id="app">
    <div class="card shadow mb-4 rounded-0">
    <!-- Card Header - Dropdown -->
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between rounded-0">
        <h6 class="m-0 font-weight-bold text-primary">Cotización <span ref="id_cotizacion"><?php echo $id_cotizacion?></span></h6>
        <div class="dropdown no-arrow">
            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink" style="">
                <div class="dropdown-header"></div>
                <a class="dropdown-item" href="<?php echo base_url('/descargar_cotizacion/'.$id_cotizacion); ?>"><span class="bi bi-download"></span> Descargar</a>
                <a class="dropdown-item" href="<?php echo base_url('/enviar_pdf/'.$id_cotizacion); ?>"><span class="bi bi-send"></span> Enviar</a>
                <a class = "dropdown-item"  href="#" @click.prevent="entregada('<?php echo $id_cotizacion ?>')"><span class="bi bi-truck"></span> Marcar Entregado</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?php echo base_url('/eliminar_cotizacion/'.$id_cotizacion); ?>" onclick="return confirm('¿Estas seguro de querer eliminar esta cotización?');"><span class="bi bi-trash3"></span> Eliminar Cotización</a>
            </div>
        </div>
    </div>
    <!-- Card Body -->
    <div class="card-body rounded-0">
        <?php foreach ($cliente as $data): ?>
        <p class="m-0"><strong>Para:</strong></p>
        <p class="m-0"><?php echo $data['nombre']?></p>
        <p class="m-0">Tel: <?php echo $data['telefono']?></p>
        <p class="m-0"><?php echo $data['correo']?></p>
        <p class="m-0">Descuento asignado a este cliente: <span ref="descuento"><?php echo $data['descuento']?></span>%</p>
        <?php endforeach ?>
        <h5 class="mt-2">Anticipo sugerido: ${{sugerido}}</h5>
        <h5 class="mt-0">Utilidad: ${{utilidad}}</h5>
    </div>
</div>
<div class="card shadow mb-4 rounded-0">
    <div class="card-body">
        <div class="row d-flex align-items-center">
            <div class="col-md-2">
                <button class="btn-my" data-bs-toggle="modal" data-bs-target="#agregar_articulo"><span class="btn-icon bi bi-plus-circle"></span>
                Agregar Artículo</button>
            </div>
            <div class="col-md-3">
                <div class="d-flex align-items-center gap-3">
                    <label for="">Descuento</label>
                    <select name="" id="" v-model="descuento" class="form-control-my shadow-none w-50">
                        <option value="" disabled>Seleccione descuento</option>
                        <option value="0">0%</option>
                        <option value="10">10%</option>
                        <option value="15">15%</option>
                        <option value="20">20%</option>
                        <option value="25">25%</option>
                    </select>
                    <button class="btn-my" @click = "aplicar_descuento">Ok</button>
                </div>
            </div>
        </div>
        <!-- <button class="btn btn-primary btn-icon-split mb-3 mb-sm-0 mr-2" data-bs-toggle="collapse" data-bs-target="#independiente">
            <span class="icon text-white-50">
                <i class="bi bi-cart"></i>
            </span>
            <span class="text">Articulo Independiente</span>
        </button> -->
        
        <!--  Modal agregar articulos -->  
        <div class="modal fade" id="agregar_articulo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content rounded-0">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Seleecione un artículo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table w-100" id="articulos">
                            <thead>
                                <tr>
                                    <th>Artículo</th>
                                    <th>Modelo</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for = "articulo in lista">
                                    <td>{{articulo.nombre}}</td>
                                    <td>{{articulo.modelo}}</td>
                                    <td>
                                        <input type="number" value="1" min="1" style="width: 50px;" :ref="articulo.id_articulo">
                                    </td>
                                    <td>
                                        <button class="btn-my" @click="add_articulo(articulo.id_articulo)"><span class="bi bi-check"></span></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-my" data-bs-dismiss="modal">
                            <span class="btn-icon bi bi-box-arrow-right"></span>
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="collapse mt-4" id="independiente">
          <div class="d-flex justify-content-between align-items-center">
            <input type="text" class="form-control rounded-0 shadow-none" placeholder="Artículo" v-model="articulo_ind">
            <input type="text" class="form-control rounded-0 shadow-none w-25" placeholder="Cant." v-model="cantidad_ind">
            <input type="text" class="form-control rounded-0 shadow-none w-25" placeholder="Precio" v-model="precio_ind">
            <button class="btn btn-primary ml-2" @click="agregar_ind"><span class="bi bi-check"></span></button>
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
                <td>
                    <input type="number" min="1" :value="dato.cantidad" style="width:50px" @change="modificar_cantidad(dato.idDetalle)" :ref="dato.idDetalle" :disabled="disabled">
                </td>
                <td>${{dato.p_unitario}}</td>
                <td>${{dato.total}}</td>
                <td :class="[display]"><a href="#" @click.prevent="borrar_linea(dato.id_detalle)"><span class="bi bi-x-lg"></span></a></td>
              </tr>
              <tr v-for="data in independiente">
                <td>{{data.descripcion}}</td>
                <td>S/M</td>
                <td>{{data.cantidad}}</td>
                <td>${{data.p_unitario}}</td>
                <td>${{data.total}}</td>
                <td :class="[display]"><a href="#" @click.prevent="borrar_linea(data.id_detalle)"><span class="bi bi-x-lg"></span></a></td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <th colspan="3"></th>
                <td><strong>Sub-Total</strong></th>
                <td>${{totales.sub_total}}</td> 
                <td></td>
              </tr>
              <tr>
                <th colspan="3"></th>
                <td><strong>Descuento</strong></th>
                <td>${{totales.descuento}}</td>
                <td></td>
              </tr>
              <tr>
                <th colspan="3"></th>
                <td><strong>IVA</strong></th>
                <td>${{totales.iva}}</td>
                <td></td>
              </tr>
              <tr>
                <th colspan="3"></th>
                <td><strong>Total</strong></th>
                <td>
                    ${{totales.total}}
                </td>
                <td>
                    
                </td>
              </tr>
              <tr>
                <th colspan="3"></th>
                <td>
                    <strong>Anticipo</strong>

                </th>
                <td>
                    ${{totales.anticipo}}
                    <button v-if="totales.anticipo == 0" class="btn-my bg-success" data-bs-toggle="modal" data-bs-target="#modalPago">
                        <span class="bi bi-cash"></span>
                    </button>
                </td>
                <td>   
                </td>
              </tr>
              <tr>
                <th colspan="3"></th>
                <td><strong>Saldo</strong></th>
                <td>${{totales.saldo}}</td>
                <td></td>
              </tr>
            </tfoot>
        </table>
    </div>
    <!-- Modal Mini pagos-->
    <div class="modal fade mini-modal" id="modalPago" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered rounded-0">
            <div class="modal-content rounded-0">
                <div class="modal-body p-3">
                    <div class="mb-2">
                        <input type="text" class="w-100" placeholder="Monto" required autofocus v-model="anticipo">
                        <div ref="errorFeedback" class="text-danger d-none mb-2"></div>
                    </div>
                    <button type="btn" class="btn-my w-100" @click.prevent="agregar_pago">
                        <i class="bi bi-check-circle"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script src="<?php echo base_url('public/js/cotizaciones.js');?>"></script> 
<?php echo $this->endSection()?>
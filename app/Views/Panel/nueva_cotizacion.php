<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div id="app">
    <?php if (session('alert')) : ?>
        <div class="alert alert-<?= session('alert')['type'] ?> alert-dismissible fade show fixed-top mx-3 mt-3" role="alert">
            <?= session('alert')['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <script>
            // Cierra automáticamente después de 5 segundos (opcional)
            document.addEventListener('DOMContentLoaded', function() {
                var alert = bootstrap.Alert.getOrCreateInstance(document.querySelector('.alert'));
                setTimeout(() => alert.close(), 5000);
            });
        </script>
    <?php endif; ?>
    <?php foreach ($data as $cotizacion): ?>        
    <!-- panel izquierdo-->
    <div class="row">
        <div class="col-3">
            <div class="card shadow mb-4 rounded-0">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between rounded-0">
                    <h6 class="m-0 font-weight-bold text-primary">Cotización <span ref="id_cotizacion"><?php echo $cotizacion['id_cotizacion']?></span></h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink" style="">
                            <div class="dropdown-header"></div>
                            <a class="dropdown-item" href="<?php echo base_url('/descargar_cotizacion/'.$cotizacion['id_cotizacion']); ?>"><span class="bi bi-download"></span> Descargar</a>
                            <a class="dropdown-item" href="<?php echo base_url('/enviar_pdf/'.$cotizacion['id_cotizacion']); ?>"><span class="bi bi-send"></span> Enviar</a>
                            <?php if ($cotizacion['estado_comercial'] === 'borrador'): ?>
                            <a class = "dropdown-item"  href="#" @click.prevent="descontar_inventario"><span class="bi bi-truck"></span> Marcar Entregado</a>                                   
                            <?php endif ?>   
                            <a class="dropdown-item" href="<?php echo base_url('/eliminar_cotizacion/'.$cotizacion['id_cotizacion']); ?>" onclick="return confirm('¿Estas seguro de querer eliminar esta cotización?');"><span class="bi bi-trash3"></span> Eliminar Cotización</a>
                            <a href="#" class="dropdown-item" @click.prevent = "generar_factura">
                                <span class="bi bi-filetype-pdf">Facturar</span>
                            </a>
                            <!-- Solo si hay orden de trabajo -->
                            <?php 
                                $puedeCrearOrden = (!$existeOrden) && ($cotizacion['anticipo'] > 0 || $cotizacion['pago'] == 1);
                            ?>
                            <?php if ($puedeCrearOrden): ?>
                                <a class="dropdown-item" href="<?= base_url('ordenes/crear_desde_cotizacion/'.$cotizacion['id_cotizacion']) ?>" onclick="return confirm('¿Crear orden de trabajo para esta cotización?')">
                                    <span class="bi bi-clipboard-check"></span> Agregar orden de trabajo
                                </a>
                            <?php endif; ?>
                            <!-- Solo si hay orden de trabajo -->
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body rounded-0">
                    <p class="m-0"><strong>Para:</strong></p>
                    <p class="m-0"><?php echo $cliente['nombre']?></p>
                    <p class="m-0">Tel: <?php echo $cliente['telefono']?></p>
                    <p class="m-0"><?php echo $cliente['correo']?></p>
                    <p class="m-0">Descuento asignado a este cliente: <span ref="descuento"><?php echo $cliente['descuento']?></span>%</p>
                    <h5 class="mt-2">Anticipo sugerido: ${{totales.total / 2}}</h5>
                </div>
            </div>
            <div class="card shadow mb-4 rounded-0">
                <div class="card-body">
                    <div class="row d-flex align-items-center">
                        <?php if ($cotizacion['pago'] == 0): ?>
                        <button class="btn-my"  data-bs-toggle="modal" data-bs-target="#agregar_articulo"><span class="btn-icon bi bi-plus-circle"></span>
                        Agregar Artículo</button>
                        <label for="" class="form-label mt-4">Descuento %</label>
                        <div class="d-flex align-items-center p-0">
                            <select name="" id="" v-model="descuento" class="form-control shadow-none form-control-sm m-0 rounded-0" :disabled="!hayArticulos">
                                <option value="" disabled>Seleccione descuento</option>
                                <option value="0">0%</option>
                                <option value="10">10%</option>
                                <option value="15">15%</option>
                                <option value="20">20%</option>
                                <option value="25">25%</option>
                            </select>
                            <button class="btn btn-dark btn-lg rounded-0" @click = "aplicar_descuento">Ok</button>
                        </div>
                        <label for="" class="form-label mt-1">Descuento $</label>
                        <div class="d-flex align-items-center p-0">
                            <input type="text" v-model="dinero_descuento" class="form-control form-control-sm rounded-0 shadow-none m-0" :disabled="!hayArticulos">
                            <button class="btn btn-dark btn-lg rounded-0" @click = "aplicar_descuento_dinero">Ok</button>
                        </div>
                            <button class="btn btn-dark mt-2"  data-bs-toggle="modal" data-bs-target="#modalPago">Marcar Pagado</button>
                        <?php endif ?>
                    </div>
                </div>
            </div>  
        </div>
        <!-- panel izquierdo-->
        <div class="col-9">
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
                            <td class="text-center">
                                <input type="number" min="1" v-model.number="dato.cantidad" 
                                       style="width:50px" 
                                       @change="modificar_cantidad(dato.id_detalle, dato.cantidad)" v-show="pagado ==0">
                                <span v-show="pagado != 0">{{dato.cantidad}}</span>
                            </td>
                            <td>${{dato.p_unitario}}</td>
                            <td>${{dato.total}}</td>
                            <td :class="[display]">
                                <?php if ($cotizacion['pago']==0): ?>
                                <a href="#" @click.prevent="borrar_linea(dato.id_detalle)"><span class="bi bi-x-lg"></span></a>
                                <?php endif ?>
                            </td>
                          </tr>
                        </tbody>
                        <tfoot>
                          <tr>
                            <th colspan="3"></th>
                            <td><strong>Sub-Total</strong></th>
                            <td>${{totales.subtotal}}</td> 
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
                                <button v-if="totales.anticipo == 0 && pagado == 0" class="btn-my bg-success" data-bs-toggle="modal" data-bs-target="#pagoModal">
                                    <span class="bi bi-cash"></span>
                                </button>
                            </td>
                            <td>   
                            </td>
                          </tr>
                          <tr>
                            <th colspan="3"></th>
                            <td><strong>Saldo</strong></th>
                            <td>
                                <span v-show="pagado == 0">${{saldo}}</span>
                                <span v-show="pagado != 0"><strong>Pagado</strong></span>
                            </td>
                            <td></td>
                          </tr>
                        </tfoot>
                    </table>                    
                </div>
            </div>
        </div>
    </div>
    <!--  Modal pagos -->
    <div class="modal fade" id="pagoModal" tabindex="-1" aria-labelledby="pagoModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm"> <!-- Añadida clase modal-sm para tamaño pequeño -->
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title fs-6" id="pagoModalLabel">Registrar Pago</h5> <!-- fs-6 para texto más pequeño -->
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form>
              <div class="mb-2"> <!-- mb-2 para menos margen -->
                <label for="bancoSelect" class="form-label small">Banco</label> <!-- small para texto más pequeño -->
                <select class="form-select form-select-sm" id="bancoSelect" v-model="bancoSeleccionado" required> <!-- form-select-sm para select pequeño -->
                    <option value="" selected disabled>Seleccione banco</option>
                    <?php foreach ($bancos as $banco): ?>
                    <option value="<?= $banco['id_cuenta'] ?>"><?= $banco['banco'] ?></option>        
                    <?php endforeach ?>
                    <!-- Menos opciones para mantenerlo compacto -->
                </select>
              </div>
              <div class="mb-2">
                <label for="cantidadPago" class="form-label small">Cantidad a pagar</label>
                <input type="number" class="form-control form-control-sm" id="cantidadPago" placeholder="Monto" required v-model="anticipo"> <!-- form-control-sm para input pequeño -->
              </div>
            </form>
          </div>
          <div class="modal-footer py-2"> <!-- py-2 para menos padding vertical -->
            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button> <!-- btn-sm para botón pequeño -->
            <button type="button" class="btn btn-sm btn-primary" @click = "agregar_pago">Guardar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal Mini pagos-->
    <div class="modal fade" id="modalPago" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered rounded-0 modal-sm">
            <div class="modal-content rounded-0">
                <div class="modal-body p-3">
                    <h4>Seleccione una cuenta</h4>
                    <select name="" id="" class="form-control form-control-sm rounded-0 shadow-none" v-model="bancoSeleccionado">
                        <option value="">Seleccione un banco...</option>
                        <?php foreach ($bancos as $banco): ?>
                        <option value="<?= $banco['id_cuenta'] ?>"><?= $banco['banco'] ?></option>        
                        <?php endforeach ?>
                    </select>
                    <button class="btn-block btn btn-primary rounded-0 float-right" @click = "marcar_pagado">Pagar</button>
                </div>
            </div>
        </div>
    </div>
    <!--  Modal agregar articulos -->  
    <div class="modal fade" id="agregar_articulo" tabindex="-1" aria-labelledby="exampleModalLabel">
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
    <!-- Modal Facturación: Selección de Método de Pago y Uso de CFDI -->
    <div class="modal fade" id="modalFacturacion" tabindex="-1" aria-labelledby="modalFacturacionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFacturacionLabel">Configurar Factura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formFacturacion">
                        <div class="mb-3">
                            <label for="metodoPagoSelect" class="form-label">Método de pago</label>
                            <select class="form-select" id="metodoPagoSelect" v-model="metodoPago">
                                <option value="" disabled>Seleccione método de pago</option>
                                <option value="PUE">Pago Único (PUE)</option>
                                <option value="PPD">Pago en Parcialidades (PPD)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="usoCfdiSelect" class="form-label">Uso de CFDI</label>
                            <select class="form-select" id="usoCfdiSelect" v-model="usoCfdi">
                                <option value="" disabled>Seleccione uso de CFDI</option>
                                <option value="G01">G01 - Adquisición de mercancías</option>
                                <option value="G02">G02 - Devoluciones, descuentos o bonificaciones</option>
                                <option value="G03">G03 - Gastos en general</option>
                                <option value="I01">I01 - Construcciones</option>
                                <option value="I02">I02 - Mobiliario y equipo de oficina</option>
                                <option value="I03">I03 - Equipo de transporte</option>
                                <option value="I04">I04 - Equipo de cómputo y accesorios</option>
                                <option value="I05">I05 - Dados, troqueles, moldes, matrices y herramental</option>
                                <option value="I06">I06 - Comunicaciones telefónicas</option>
                                <option value="I07">I07 - Comunicaciones satelitales</option>
                                <option value="I08">I08 - Otra maquinaria y equipo</option>
                                <option value="D01">D01 - Honorarios médicos, dentales y gastos hospitalarios</option>
                                <option value="D02">D02 - Gastos médicos por incapacidad o discapacidad</option>
                                <option value="D03">D03 - Gastos funerales</option>
                                <option value="D04">D04 - Donativos</option>
                                <option value="D05">D05 - Intereses reales efectivamente pagados por créditos hipotecarios</option>
                                <option value="D06">D06 - Aportaciones voluntarias al SAR</option>
                                <option value="D07">D07 - Primas por seguros de gastos médicos</option>
                                <option value="D08">D08 - Gastos de transportación escolar obligatoria</option>
                                <option value="D09">D09 - Depósitos en cuentas para el ahorro</option>
                                <option value="D10">D10 - Pagos por servicios educativos (colegiaturas)</option>
                                <option value="S01">S01 - Sin efectos fiscales</option>
                                <option value="CP01">CP01 - Pagos</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="formaPagoSelect" class="form-label">Forma de pago</label>
                            <select class="form-select" id="formaPagoSelect" v-model="formaPago">
                                <option value="" disabled>Seleccione forma de pago</option>
                                <option value="01">01 - Efectivo</option>
                                <option value="02">02 - Cheque nominativo</option>
                                <option value="03">03 - Transferencia electrónica de fondos</option>
                                <option value="04">04 - Tarjeta de crédito</option>
                                <option value="05">05 - Monedero electrónico</option>
                                <option value="06">06 - Dinero electrónico</option>
                                <option value="08">08 - Vales de despensa</option>
                                <option value="12">12 - Dación en pago</option>
                                <option value="13">13 - Pago por subrogación</option>
                                <option value="14">14 - Pago por consignación</option>
                                <option value="15">15 - Condonación</option>
                                <option value="17">17 - Compensación</option>
                                <option value="23">23 - Novación</option>
                                <option value="24">24 - Confusión</option>
                                <option value="25">25 - Remisión de deuda</option>
                                <option value="26">26 - Prescripción o caducidad</option>
                                <option value="27">27 - A satisfacción del acreedor</option>
                                <option value="28">28 - Tarjeta de débito</option>
                                <option value="29">29 - Tarjeta de servicios</option>
                                <option value="30">30 - Aplicación de anticipos</option>
                                <option value="31">31 - Intermediario pagos</option>
                                <option value="99">99 - Por definir</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-sm btn-primary" @click="confirmarFacturacion">Confirmar y Facturar</button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach ?>
</div>
<script src="<?php echo base_url('public/js/cotizaciones.js');?>"></script> 
<script src="<?php echo base_url('public/js/notify.js');?>"></script> 
<?php echo $this->endSection()?>

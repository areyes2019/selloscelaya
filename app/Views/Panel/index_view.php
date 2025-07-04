<?= $this->extend('Panel/panel_template') ?>

<?= $this->section('contenido') ?>
<div id="app">
<h1><?= esc($titulo) ?></h1>
<!-- Mostrar errores de validación -->
<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Mostrar mensajes de éxito/error generales -->
<?php if (session()->has('success')): ?>
    <div class="alert alert-success"><?= session('success') ?></div>
<?php endif; ?>
<?php if (session()->has('error')): ?>
    <div class="alert alert-danger"><?= session('error') ?></div>
<?php endif; ?>

<form action="<?= site_url('ventas/create') ?>" method="post">
    <div class="row">
        <!-- Columna Izquierda: Formulario -->
        <div class="col-md-7">
            <h2>Datos del Pedido</h2>

            <div class="mb-3">
                <label for="cliente_nombre" class="form-label">Nombre del Cliente:</label>
                <input type="text" class="form-control" id="cliente_nombre" name="cliente_nombre" v-model="nombre" required>
            </div>

            <div class="mb-3">
                <label for="cliente_telefono" class="form-label">Teléfono del Cliente (Opcional):</label>
                <input type="tel" class="form-control" id="cliente_telefono" name="cliente_telefono" v-model="telefono" required>
            </div>
            <!-- Grupo de inputs solicitado -->
            <div class="row g-3 align-items-end mb-3">
                <div class="col-md-4 ">
                    <label for="autocomplete_input" class="form-label">Artículo:</label>
                    <input 
                        id="autocomplete_input"
                        class="form-control autocomplete-container"
                        type="text"
                        placeholder="Buscar por nombre, modelo o clave..."
                        v-model="searchQuery"
                        @input="handleInput"
                        @focus="showResults = true"
                        @keydown="handleKeyDown"
                    >
                    <div class="autocomplete-dropdown" v-if="showResults && filteredArticulos.length" ref="dropdownContainer">
                        <div 
                            v-for="(articulo, index) in filteredArticulos"
                            :key="articulo.id_articulo"
                            class="dropdown-item"
                            :class="{ 'active': index === selectedIndex }"
                            @click="selectItem(articulo)"
                            :ref="index === selectedIndex ? 'activeItem' : null"
                        >
                            <div class="d-flex justify-content-between">
                                <strong>{{articulo.modelo}}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" v-model="articuloId">
                <div class="col-md-2">
                    <label for="cantidad_servicio" class="form-label">Cantidad:</label>
                    <input type="number" class="form-control" id="cantidad_servicio" name="cantidad_servicio" 
                           v-model.number="cantidad" min="1">
                </div>
                <div class="col-md-4">
                    <label for="descripcion_servicio" class="form-label">Precio Unitario:</label>
                    <input type="text" class="form-control" id="descripcion_servicio" name="descripcion_servicio" 
                           v-model="precio_unitario">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary w-100 mb-3" @click="agregarArticulo">
                        Agregar
                    </button>
                </div>       
            </div>
            <hr>

            <h3>Items del Pedido</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Precio Unit.</th>
                        <th>Subtotal</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in itemsPedido" :key="index">
                        <td>
                            {{ item.nombre }} - {{ item.modelo }}
                            <input type="hidden" :name="'detalle['+index+'][descripcion]'" :value="item.nombre + ' - ' + item.modelo">
                            <input type="hidden" :name="'detalle['+index+'][id_articulo]'" :value="item.id">
                        </td>
                        <td>
                            {{ item.cantidad }}
                            <input type="hidden" :name="'detalle['+index+'][cantidad]'" :value="item.cantidad">
                        </td>
                        <td>
                            {{ formatCurrency(item.precio_unitario) }}
                            <input type="hidden" :name="'detalle['+index+'][precio_unitario]'" :value="item.precio_unitario">
                        </td>
                        <td>{{ formatCurrency(item.subtotal) }}</td>
                        <td>
                            <button @click="eliminarItem(index)" class="btn btn-danger btn-sm">
                                <span class="bi bi-x-lg"></span>
                            </button>
                        </td>
                    </tr>
                    <tr v-if="itemsPedido.length === 0">
                        <td colspan="5" class="text-center text-muted">No hay artículos agregados</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>TOTAL:</strong></td>
                        <td><strong>$2500.00</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

            <div class="row mt-3">
                <div class="col-md-6 offset-md-6">
                    <div class="card">
                        <div class="card-body">
                          <h5 class="card-title">Resumen de Pago</h5>
                            <div class="row mb-2">
                                <div class="col-6"><strong>Total:</strong></div>
                                <div class="col-6 text-end">{{ formatCurrency(total) }}</div>
                            </div>
                          
                            <div class="row mb-2">
                                <div class="col-6">
                                <label for="anticipo" class="form-label"><strong>Anticipo:</strong></label>
                            </div>
                            <div class="col-6">
                                  <input 
                                    type="number" 
                                    step="0.01" 
                                    min="0" 
                                    :max="total"
                                    class="form-control" 
                                    id="anticipo" 
                                    name="anticipo" 
                                    v-model.number="anticipo"
                                    @input="actualizarAnticipo"
                                  >
                            </div>
                            </div>
                            <!-- Sección de descuento -->
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label for="descuento" class="form-label"><strong>Descuento:</strong></label>
                                </div>
                                <div class="col-6">
                                    <select 
                                        class="form-select" 
                                        id="descuento" 
                                        name="descuento"
                                        v-model="descuento"
                                        @change="aplicarDescuento"
                                    >
                                        <option value="0">Sin descuento</option>
                                        <option value="5">5%</option>
                                        <option value="10">10%</option>
                                        <option value="15">15%</option>
                                        <option value="20">20%</option>
                                        <option value="25">25%</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-6"><strong>Subtotal:</strong></div>
                                <div class="col-6 text-end">{{ formatCurrency(subtotal) }}</div>
                            </div>

                            <div class="row mb-4" v-if="descuento > 0">
                                <div class="col-6"><strong>Descuento ({{ descuento }}%):</strong></div>
                                <div class="col-6 text-end">- {{ formatCurrency(montoDescuento) }}</div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-6"><strong>Total con descuento:</strong></div>
                                <div class="col-6 text-end">{{ formatCurrency(totalConDescuento) }}</div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-6"><strong>Saldo:</strong></div>
                                <div class="col-6 text-end">{{ formatCurrency(saldo) }}</div>
                            </div>
                            
                            <!-- Select de bancos -->
                            <div class="row mb-2 mt-2">
                                <div class="col-6"><strong>Banco:</strong></div>
                                <div class="col-6">
                                    <select class="form-select" name="banco_id" required>
                                        <option disabled selected value="">Seleccione un banco</option>
                                        <?php foreach ($cuentas as $cuenta): ?>
                                            <option value="<?= esc($cuenta['id_cuenta']) ?>">
                                                <?= esc($cuenta['banco']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="total_final_hidden" :value="total">
            <button type="submit" class="btn btn-success btn-lg">Finalizar Venta</button>
        </div>
    </div>
</form>
</div>
<script src="<?php echo base_url('public/js/ventas.js'); ?>"></script>
<?= $this->endSection() ?>
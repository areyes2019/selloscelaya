<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="midde_cont" id="app">
    <div class="container-fluid">
        <div class="row column_title card rounded-0 shadow-sm">
            <div class="col-md-12">
                <div class="page_title">
                    <h2>Facturas Emitidas</h2>
                </div>
                <!-- Mensajes Flash -->
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- row -->
        <div class="row">
            <!-- table section -->
            <div class="col-md-12">
                <div class="white_shd full margin_bottom_30">
                    <div class="full graph_head">
                        <div class="heading1 margin_0">
                            <button class="btn btn-danger rounded-0 btn-sm mb-5" @click="crearFactura">
                                <i class="fas fa-plus"></i> Nueva Factura
                            </button>
                            <div class="float-end">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm rounded-0" 
                                           placeholder="Buscar factura..." 
                                           v-model="busqueda"
                                           @input="filtrarFacturas">
                                    <button class="btn btn-outline-secondary btn-sm rounded-0" type="button">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card rounded-0 shadow-sm table_section padding_infor_info">
                        <div class="table-responsive-sm">
                            <table id="facturasTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th># Factura</th>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>RFC</th>
                                        <th>Subtotal</th>
                                        <th>IVA</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="factura in paginatedFacturas" :key="factura.uuid">
                                        <td>{{ factura.folio }}</td>
                                        <td>{{ formatFecha(factura.fecha) }}</td>
                                        <td>{{ factura.cliente }}</td>
                                        <td>{{ factura.rfc }}</td>
                                        <td class="text-end">${{ formatNumber(factura.subtotal) }}</td>
                                        <td class="text-end">${{ formatNumber(factura.iva) }}</td>
                                        <td class="text-end">${{ formatNumber(factura.total) }}</td>
                                        <td>
                                            <span class="badge" :class="{
                                                'bg-success': factura.estado === 'Timbrada',
                                                'bg-warning text-dark': factura.estado === 'Pendiente',
                                                'bg-danger': factura.estado === 'Cancelada'
                                            }">
                                                {{ factura.estado }}
                                            </span>
                                        </td>
                                        <td class="d-flex gap-1">
                                            <!-- Descargar PDF -->
                                            <button class="btn btn-sm btn-danger rounded-0" 
                                                    @click="descargarPDF(factura.uuid)"
                                                    title="Descargar PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </button>
                                            
                                            <!-- Descargar XML -->
                                            <button class="btn btn-sm btn-secondary rounded-0" 
                                                    @click="descargarXML(factura.uuid)"
                                                    title="Descargar XML">
                                                <i class="fas fa-file-code"></i>
                                            </button>
                                            
                                            <!-- Enviar por correo -->
                                            <button class="btn btn-sm btn-primary rounded-0" 
                                                    @click="enviarCorreo(factura.uuid)"
                                                    title="Enviar por correo">
                                                <i class="fas fa-envelope"></i>
                                            </button>
                                            
                                            <!-- Cancelar -->
                                            <button v-if="factura.estado === 'Timbrada'" 
                                                    class="btn btn-sm btn-warning rounded-0" 
                                                    @click="confirmarCancelacion(factura.uuid)"
                                                    title="Cancelar factura">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- Paginación -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    Mostrando {{ (currentPage - 1) * itemsPerPage + 1 }} - 
                                    {{ Math.min(currentPage * itemsPerPage, filteredFacturas.length) }} de {{ filteredFacturas.length }} facturas
                                </div>
                                
                                <nav aria-label="Page navigation">
                                    <ul class="pagination pagination-sm">
                                        <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                            <button class="page-link" @click="changePage(currentPage - 1)">Anterior</button>
                                        </li>
                                        
                                        <li class="page-item" v-for="page in totalPages" :key="page" 
                                            :class="{ active: currentPage === page }">
                                            <button class="page-link" @click="changePage(page)">{{ page }}</button>
                                        </li>
                                        
                                        <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                                            <button class="page-link" @click="changePage(currentPage + 1)">Siguiente</button>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para nueva factura -->
<div class="modal fade" id="nuevaFacturaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Factura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="emitirFactura">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Cliente</label>
                            <select class="form-select" v-model="facturaData.clienteId" required>
                                <option value="">Seleccione un cliente</option>
                                <option v-for="cliente in clientes" :value="cliente.id">{{ cliente.nombre }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha</label>
                            <input type="date" class="form-control" v-model="facturaData.fecha" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Artículos</label>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Artículo</th>
                                        <th>Cantidad</th>
                                        <th>Precio</th>
                                        <th>Importe</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in facturaData.items" :key="index">
                                        <td>
                                            <select class="form-select" v-model="item.articuloId" @change="actualizarPrecio(item)" required>
                                                <option value="">Seleccione artículo</option>
                                                <option v-for="articulo in articulos" :value="articulo.id">{{ articulo.nombre }}</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" min="1" v-model="item.cantidad" @change="calcularImporte(item)" required>
                                        </td>
                                        <td>${{ formatNumber(item.precio) }}</td>
                                        <td>${{ formatNumber(item.importe) }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger" @click="eliminarItem(index)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5">
                                            <button type="button" class="btn btn-sm btn-primary" @click="agregarItem">
                                                <i class="fas fa-plus"></i> Agregar Artículo
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Forma de pago</label>
                            <select class="form-select" v-model="facturaData.formaPago" required>
                                <option value="01">Efectivo</option>
                                <option value="02">Cheque</option>
                                <option value="03">Transferencia</option>
                                <option value="04">Tarjeta de crédito</option>
                                <option value="05">Tarjeta de débito</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Método de pago</label>
                            <select class="form-select" v-model="facturaData.metodoPago" required>
                                <option value="PUE">Pago en una sola exhibición</option>
                                <option value="PPD">Pago en parcialidades</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Subtotal</h6>
                                    <h4 class="card-text">${{ formatNumber(calcularSubtotal) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">IVA (16%)</h6>
                                    <h4 class="card-text">${{ formatNumber(calcularIVA) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total</h6>
                                    <h4 class="card-text">${{ formatNumber(calcularTotal) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" :disabled="isSaving">
                            <span v-if="isSaving">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Procesando...
                            </span>
                            <span v-else>
                                <i class="fas fa-file-invoice"></i> Emitir Factura
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Script Vue 3 con Composition API
const { createApp, ref, computed, onMounted } = Vue;

createApp({
    setup() {
        // Estado reactivo
        const busqueda = ref('');
        const currentPage = ref(1);
        const itemsPerPage = ref(10);
        const isSaving = ref(false);
        
        const facturaData = ref({
            clienteId: '',
            fecha: new Date().toISOString().substr(0, 10),
            formaPago: '01',
            metodoPago: 'PUE',
            items: [{
                articuloId: '',
                cantidad: 1,
                precio: 0,
                importe: 0
            }]
        });
        
        // Datos ficticios
        const facturas = ref([
            {
                uuid: '123e4567-e89b-12d3-a456-426614174000',
                folio: 'FAC-2023-0001',
                fecha: '2023-05-15T12:00:00',
                cliente: 'Juan Pérez López',
                rfc: 'PEPJ800101ABC',
                subtotal: 1500.00,
                iva: 240.00,
                total: 1740.00,
                estado: 'Timbrada'
            },
            {
                uuid: '223e4567-e89b-12d3-a456-426614174001',
                folio: 'FAC-2023-0002',
                fecha: '2023-05-16T14:30:00',
                cliente: 'Empresa ABC, S.A. de C.V.',
                rfc: 'ABC850101XYZ',
                subtotal: 3250.50,
                iva: 520.08,
                total: 3770.58,
                estado: 'Timbrada'
            },
            {
                uuid: '323e4567-e89b-12d3-a456-426614174002',
                folio: 'FAC-2023-0003',
                fecha: '2023-05-17T10:15:00',
                cliente: 'María González Sánchez',
                rfc: 'GOMS750202DEF',
                subtotal: 890.00,
                iva: 142.40,
                total: 1032.40,
                estado: 'Cancelada'
            },
            {
                uuid: '423e4567-e89b-12d3-a456-426614174003',
                folio: 'FAC-2023-0004',
                fecha: '2023-05-18T16:45:00',
                cliente: 'Roberto Martínez',
                rfc: 'MARB900303GHI',
                subtotal: 2100.00,
                iva: 336.00,
                total: 2436.00,
                estado: 'Pendiente'
            }
        ]);
        
        const clientes = ref([
            { id: 1, nombre: 'Juan Pérez López', rfc: 'PEPJ800101ABC' },
            { id: 2, nombre: 'Empresa ABC, S.A. de C.V.', rfc: 'ABC850101XYZ' },
            { id: 3, nombre: 'María González Sánchez', rfc: 'GOMS750202DEF' },
            { id: 4, nombre: 'Roberto Martínez', rfc: 'MARB900303GHI' }
        ]);
        
        const articulos = ref([
            { id: 1, nombre: 'Producto A', precio: 150.00 },
            { id: 2, nombre: 'Producto B', precio: 250.50 },
            { id: 3, nombre: 'Producto C', precio: 89.99 },
            { id: 4, nombre: 'Producto D', precio: 120.00 }
        ]);
        
        // Computed properties
        const filteredFacturas = computed(() => {
            return facturas.value.filter(factura => {
                const searchTerm = busqueda.value.toLowerCase();
                return (
                    factura.folio.toLowerCase().includes(searchTerm) ||
                    factura.cliente.toLowerCase().includes(searchTerm) ||
                    factura.rfc.toLowerCase().includes(searchTerm) ||
                    factura.estado.toLowerCase().includes(searchTerm)
                );
            });
        });
        
        const paginatedFacturas = computed(() => {
            const start = (currentPage.value - 1) * itemsPerPage.value;
            const end = start + itemsPerPage.value;
            return filteredFacturas.value.slice(start, end);
        });
        
        const totalPages = computed(() => {
            return Math.ceil(filteredFacturas.value.length / itemsPerPage.value);
        });
        
        const calcularSubtotal = computed(() => {
            return facturaData.value.items.reduce((sum, item) => sum + item.importe, 0);
        });
        
        const calcularIVA = computed(() => {
            return calcularSubtotal.value * 0.16;
        });
        
        const calcularTotal = computed(() => {
            return calcularSubtotal.value + calcularIVA.value;
        });
        
        // Métodos
        const formatNumber = (value) => {
            return new Intl.NumberFormat('es-MX', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(value);
        };
        
        const formatFecha = (fecha) => {
            return new Date(fecha).toLocaleDateString('es-MX');
        };
        
        const filtrarFacturas = () => {
            currentPage.value = 1;
        };
        
        const changePage = (page) => {
            if (page >= 1 && page <= totalPages.value) {
                currentPage.value = page;
            }
        };
        
        const crearFactura = () => {
            const modal = new bootstrap.Modal(document.getElementById('nuevaFacturaModal'));
            modal.show();
        };
        
        const agregarItem = () => {
            facturaData.value.items.push({
                articuloId: '',
                cantidad: 1,
                precio: 0,
                importe: 0
            });
        };
        
        const eliminarItem = (index) => {
            if (facturaData.value.items.length > 1) {
                facturaData.value.items.splice(index, 1);
            }
        };
        
        const actualizarPrecio = (item) => {
            const articulo = articulos.value.find(a => a.id == item.articuloId);
            if (articulo) {
                item.precio = articulo.precio;
                item.importe = item.precio * item.cantidad;
            }
        };
        
        const calcularImporte = (item) => {
            item.importe = item.precio * item.cantidad;
        };
        
        const emitirFactura = () => {
            isSaving.value = true;
            
            // Simular llamada a API
            setTimeout(() => {
                // Crear nueva factura ficticia
                const cliente = clientes.value.find(c => c.id == facturaData.value.clienteId);
                const nuevaFactura = {
                    uuid: 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                        var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                        return v.toString(16);
                    }),
                    folio: `FAC-2023-${String(facturas.value.length + 1).padStart(4, '0')}`,
                    fecha: new Date().toISOString(),
                    cliente: cliente.nombre,
                    rfc: cliente.rfc,
                    subtotal: calcularSubtotal.value,
                    iva: calcularIVA.value,
                    total: calcularTotal.value,
                    estado: 'Timbrada'
                };
                
                facturas.value.unshift(nuevaFactura);
                isSaving.value = false;
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('nuevaFacturaModal'));
                modal.hide();
                
                // Mostrar notificación
                $.notify({
                    title: 'Factura emitida',
                    message: `La factura ${nuevaFactura.folio} se ha generado correctamente`,
                    icon: 'fas fa-check-circle'
                }, {
                    type: 'success'
                });
                
                // Resetear formulario
                facturaData.value = {
                    clienteId: '',
                    fecha: new Date().toISOString().substr(0, 10),
                    formaPago: '01',
                    metodoPago: 'PUE',
                    items: [{
                        articuloId: '',
                        cantidad: 1,
                        precio: 0,
                        importe: 0
                    }]
                };
            }, 1500);
        };
        
        const descargarPDF = (uuid) => {
            // Simular descarga de PDF
            $.notify({
                title: 'Descarga iniciada',
                message: 'El PDF de la factura se está descargando',
                icon: 'fas fa-file-pdf'
            }, {
                type: 'info'
            });
        };
        
        const descargarXML = (uuid) => {
            // Simular descarga de XML
            $.notify({
                title: 'Descarga iniciada',
                message: 'El XML de la factura se está descargando',
                icon: 'fas fa-file-code'
            }, {
                type: 'info'
            });
        };
        
        const enviarCorreo = (uuid) => {
            // Simular envío por correo
            $.notify({
                title: 'Correo enviado',
                message: 'La factura ha sido enviada al cliente por correo electrónico',
                icon: 'fas fa-envelope'
            }, {
                type: 'success'
            });
        };
        
        const confirmarCancelacion = (uuid) => {
            if (confirm('¿Está seguro de cancelar esta factura?')) {
                const factura = facturas.value.find(f => f.uuid === uuid);
                if (factura) {
                    factura.estado = 'Cancelada';
                    $.notify({
                        title: 'Factura cancelada',
                        message: 'La factura ha sido cancelada correctamente',
                        icon: 'fas fa-ban'
                    }, {
                        type: 'warning'
                    });
                }
            }
        };
        
        return {
            busqueda,
            currentPage,
            itemsPerPage,
            isSaving,
            facturaData,
            facturas,
            clientes,
            articulos,
            filteredFacturas,
            paginatedFacturas,
            totalPages,
            calcularSubtotal,
            calcularIVA,
            calcularTotal,
            formatNumber,
            formatFecha,
            filtrarFacturas,
            changePage,
            crearFactura,
            agregarItem,
            eliminarItem,
            actualizarPrecio,
            calcularImporte,
            emitirFactura,
            descargarPDF,
            descargarXML,
            enviarCorreo,
            confirmarCancelacion
        };
    }
}).mount('#app');
</script>

<?php echo $this->endSection()?>
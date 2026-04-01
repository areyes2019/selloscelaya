const { createApp } = Vue;

createApp({
    data() {
        return {
            ordenes: {
                dibujo: [],
                elaboracion: [],
                entrega: [],
                facturacion: []
            },
            ordenSeleccionada: {},
            cuentasBancarias: [],
            cuentaSeleccionada: null,
            cargandoCuentas: false,
            cargandoDetalle: false,
            error: null,
            // ... tus otros datos existentes ...
            nuevaTarea: '',
            nuevoTelefono: '',
            nuevaFecha: this.getFormattedDate(new Date()), // Fecha por defecto (hoy)
            tareas: [
                { 
                    titulo: 'Llamar al cliente', 
                    descripcion: 'Confirmar diseño', 
                    telefono: '5551234567', 
                    fecha: this.getFormattedDate(new Date()), 
                    completada: false, 
                    prioridad: 'alta' 
                },
                { 
                    titulo: 'Seguimiento pedido', 
                    telefono: '5557654321', 
                    fecha: this.getFormattedDate(this.getTomorrowDate()), 
                    completada: false, 
                    prioridad: 'media' 
                },
                { 
                    titulo: 'Materiales proveedor', 
                    descripcion: 'Confirmar stock', 
                    telefono: '5559876543', 
                    fecha: this.getFormattedDate(new Date()), 
                    completada: true, 
                    prioridad: 'baja' 
                }
            ],
            filtroTareas: 'todas'
        };
    },
    // En computed
    computed: {
        fechaHoy() {
            const options = { weekday: 'long', day: 'numeric', month: 'short' };
            return new Date().toLocaleDateString('es-ES', options);
        },
        tareasFiltradas() {
            const hoy = this.getFormattedDate(new Date());
            const mañana = this.getFormattedDate(this.getTomorrowDate());
            
            return this.tareas.filter(t => {
                if (this.filtroTareas === 'hoy') return t.fecha === hoy;
                if (this.filtroTareas === 'mañana') return t.fecha === mañana;
                return true;
            });
        }
    },
    mounted() {
        this.cargarOrdenes();
    },
    methods: {
        cargarOrdenes() {
            fetch('/administracion/cargar_ordenes')
                .then(response => response.json())
                .then(data => {
                    this.ordenes.dibujo = data.filter(o => o.status === 'Dibujo');
                    this.ordenes.elaboracion = data.filter(o => o.status === 'Elaboracion');
                    this.ordenes.entrega = data.filter(o => o.status === 'Entrega' || o.status === 'Entregado');
                    this.ordenes.facturacion = data.filter(o => o.status === 'Facturacion');
                })
                .catch(error => {
                    console.error(error);
                    this.error = 'Error al cargar las órdenes.';
                });
        },
        cargarDetalleOrden(id_ot) {
            this.cargandoDetalle = true;
            this.error = '';
            
            // Usando axios (más limpio que fetch)
            axios.get(`/administracion/ordenes/${id_ot}`) // o `/detalle_orden/${id_ot}` según tu ruta
                .then(response => {
                    this.ordenSeleccionada = response.data;
                    this.cargandoDetalle = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.cargandoDetalle = false;
                    this.error = 'Error al cargar detalles de la orden.';
                    
                    // Mostrar mensaje de error más específico
                    if (error.response && error.response.data && error.response.data.error) {
                        this.error = error.response.data.error;
                    }
                });
        },
        imprimirOrden(id_ot) {
            window.print(); // O tu lógica de impresión específica
        },
        actualizarEstado(id_ot, nuevoEstado) {
            fetch(`/administracion/actualizar-estado/${id_ot}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                },
                body: JSON.stringify({ status: nuevoEstado })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.cargarOrdenes();
                    this.mostrarToast('Estado actualizado correctamente');
                } else {
                    this.mostrarToast('Error: ' + (data.message || 'No se pudo actualizar el estado'), true);
                }
            })
            .catch(err => {
                console.error(err);
                this.mostrarToast('Error en la actualización del estado', true);
            });
        },
        confirmarPago(pedido_id) {
            this.cuentaSeleccionada = null;
            this.obtenerCuentas();
            // Guarda el ID del pedido en la orden seleccionada para usarlo al confirmar
            this.ordenSeleccionada.pedido_id = pedido_id;
            new bootstrap.Modal(document.getElementById('cuentaModal')).show();
        },
        obtenerCuentas() {
            this.cargandoCuentas = true;
            fetch('/cuentas/listar')
                .then(response => response.json())
                .then(data => {
                    this.cuentasBancarias = data;
                    this.cargandoCuentas = false;
                })
                .catch(error => {
                    console.error(error);
                    this.cargandoCuentas = false;
                    this.mostrarToast('Error al cargar las cuentas bancarias', true);
                });
        },
        procesarPago() {
            if (!this.cuentaSeleccionada) {
                this.mostrarToast('Debe seleccionar una cuenta bancaria', true);
                return;
            }

            fetch(`/administracion/pagar/${this.ordenSeleccionada.pedido_id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                },
                body: JSON.stringify({
                    cuenta_id: this.cuentaSeleccionada
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.mostrarToast('Pedido marcado como pagado. Saldo actualizado.');
                    this.cargarOrdenes();
                    bootstrap.Modal.getInstance(document.getElementById('cuentaModal')).hide();
                } else {
                    this.mostrarToast('Error: ' + (data.error || 'No se pudo procesar el pago'), true);
                }
            })
            .catch(err => {
                console.error(err);
                this.mostrarToast('Error al procesar el pago', true);
            });
        },
        eliminarOrden(id_ot) {
            if (!confirm('¿Está seguro de eliminar esta orden? Esta acción no se puede deshacer.')) {
                return;
            }
            
            axios.get(`ordenes/eliminar/${id_ot}`, {
                headers: {
                    'X-CSRF-TOKEN': window.csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                const data = response.data;
                if (data.success) {
                    this.mostrarToast('Orden eliminada correctamente');
                    this.cargarOrdenes();
                } else {
                    this.mostrarToast('Error: ' + (data.message || 'No se pudo eliminar la orden'), true);
                }
            })
            .catch(error => {
                console.error('Error completo:', error);
                
                if (error.response) {
                    // El servidor respondió con un código de error
                    console.error('Status:', error.response.status);
                    console.error('Data:', error.response.data);
                    
                    if (error.response.status === 404) {
                        this.mostrarToast('Error: Ruta no encontrada', true);
                    } else if (error.response.status === 500) {
                        this.mostrarToast('Error interno del servidor', true);
                    } else {
                        this.mostrarToast('Error: ' + error.response.status, true);
                    }
                } else if (error.request) {
                    // La petición fue hecha pero no se recibió respuesta
                    this.mostrarToast('Error de conexión con el servidor', true);
                } else {
                    // Error al configurar la petición
                    this.mostrarToast('Error: ' + error.message, true);
                }
            });
        },
        mostrarToast(mensaje, esError = false) {
            const toastElement = document.getElementById('liveToast');
            const toastMessage = document.getElementById('toastMessage');
            toastMessage.textContent = mensaje;
            toastElement.classList.remove('bg-success', 'bg-danger');
            toastElement.classList.add(esError ? 'bg-danger' : 'bg-success');
            new bootstrap.Toast(toastElement).show();
        },
        imprimirOrden(id_ot) {
            window.open(`/ordenes/imprimir/${id_ot}`, '_blank');
        },
        marcarComoFacturado(id_ot, index) {
            this.actualizarEstado(id_ot, 'Facturado');
        },

        //formulario de tareas
        establecerFechaHoy() {
        this.nuevaFecha = this.getFormattedDate(new Date());
        this.filtroTareas = 'hoy';
        },
        
        establecerFechaManana() {
            const manana = new Date();
            manana.setDate(manana.getDate() + 1);
            this.nuevaFecha = this.getFormattedDate(manana);
            this.filtroTareas = 'mañana';
        },
        
        actualizarTarea(tarea) {
            // Aquí iría la llamada axios para actualizar en el servidor
            console.log('Tarea actualizada:', tarea);
            this.mostrarNotificacion('Tarea actualizada');
        },
        
        eliminarTarea(index) {
            if (confirm('¿Estás seguro de eliminar esta tarea?')) {
                this.tareas.splice(index, 1);
                this.mostrarNotificacion('Tarea eliminada');
            }
        },
        
        getFormattedDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        },
        
        getTomorrowDate() {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            return tomorrow;
        },
        
        filtrarTareas(filtro) {
            this.filtroTareas = filtro;
        },
        
        mostrarNotificacion(mensaje, tipo = 'success') {
            const toast = new bootstrap.Toast(document.getElementById('liveToast'));
            const toastMessage = document.getElementById('toastMessage');
            
            toastMessage.textContent = mensaje;
            const toastElement = document.getElementById('liveToast');
            
            // Limpiar clases anteriores
            toastElement.classList.remove('bg-success', 'bg-danger', 'bg-info', 'bg-warning');
            
            // Añadir clase según el tipo
            if (tipo === 'success') {
                toastElement.classList.add('bg-success');
            } else if (tipo === 'danger') {
                toastElement.classList.add('bg-danger');
            } else if (tipo === 'info') {
                toastElement.classList.add('bg-info');
            } else if (tipo === 'warning') {
                toastElement.classList.add('bg-warning');
            }
            
            toast.show();
        },
        actualizarFiltroPorFecha() {
            const hoy = this.getFormattedDate(new Date());
            const manana = this.getFormattedDate(this.getTomorrowDate());
            
            if (this.nuevaFecha === hoy) {
                this.filtroTareas = 'hoy';
            } else if (this.nuevaFecha === manana) {
                this.filtroTareas = 'mañana';
            } else {
                this.filtroTareas = 'personalizada';
            }
        },
        agregarTarea() {
            if (!this.nuevaTarea.trim()) return;
            
            const tareaData = {
                titulo: this.nuevaTarea,
                telefono: this.nuevoTelefono,
                fecha: this.nuevaFecha,
                prioridad: 'media'
            };
            
            // Si estás usando Axios con el backend:
            axios.post('/api/tareas', tareaData)
                .then(response => {
                    this.tareas.unshift(response.data);
                    this.limpiarFormulario();
                    this.mostrarNotificacion('Tarea agregada');
                })
                .catch(error => {
                    console.error('Error al agregar tarea:', error);
                    this.mostrarNotificacion('Error al agregar tarea', 'danger');
                });
            
            // Si estás trabajando solo en frontend:
            /*
            this.tareas.unshift({
                ...tareaData,
                id_tarea: Date.now(), // ID temporal
                completada: false,
                descripcion: ''
            });
            this.limpiarFormulario();
            this.mostrarNotificacion('Tarea agregada');
            */
        },
        
        limpiarFormulario() {
            this.nuevaTarea = '';
            this.nuevoTelefono = '';
            // Mantener la fecha por defecto (no limpiar este campo)
        },
    }
}).mount('#app');

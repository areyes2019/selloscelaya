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
            error: null
        };
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
            fetch(`/ordenes/${id_ot}`)
                .then(response => response.json())
                .then(data => {
                    this.ordenSeleccionada = data;
                    this.cargandoDetalle = false;
                })
                .catch(error => {
                    console.error(error);
                    this.cargandoDetalle = false;
                    this.error = 'Error al cargar detalles de la orden.';
                });
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
            fetch(`/administracion/eliminar/${id_ot}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': window.csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.mostrarToast('Orden eliminada correctamente');
                    this.cargarOrdenes();
                } else {
                    this.mostrarToast('Error: ' + (data.message || 'No se pudo eliminar la orden'), true);
                }
            })
            .catch(err => {
                console.error(err);
                this.mostrarToast('Error al eliminar la orden', true);
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
        }
    }
}).mount('#app');

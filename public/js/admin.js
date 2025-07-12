const { createApp } = Vue;

createApp({
    data() {
        return {
            ordenes: {
                dibujo: [],
                elaboracion: [],
                entrega: [],
                facturacion: [], // Nuevo array para facturación
            },
            loading: false,
            error: null,
            ordenSeleccionada: {},
            cargandoDetalle: false
        }
    },
    methods: {
        async cargarDetalleOrden(id_ot) {
            try {
                // Validación básica del ID
                if (!id_ot || isNaN(id_ot)) {
                    throw new Error('ID de orden no válido');
                }

                const response = await axios.get('ordenes/mostrar/' + id_ot, {
                    params: {
                        // Puedes añadir parámetros adicionales si es necesario
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                if (response.status !== 200 || !response.data) {
                    throw new Error('Respuesta no válida del servidor');
                }

                this.ordenSeleccionada = response.data[0]; // Accede a la propiedad 'orden'
                
            } catch (error) {
                console.error('Error al cargar detalle de orden:', error);
                // Puedes manejar el error mostrando un mensaje al usuario
                this.mostrarError('No se pudo cargar la orden. Por favor intenta nuevamente.');
            }
        },
        
        formatFecha(fecha) {
            if (!fecha) return 'N/A';
            return new Date(fecha).toLocaleString();
        },
        
        imprimirOrden(id_ot) {
            // Implementa la lógica de impresión aquí
            window.open(`/ordenes/imprimir/${id_ot}`, '_blank');
        },
        async cargarOrdenes() {
            try {
                const response = await axios.get('/administracion/cargar_ordenes');

                this.ordenes = {
                    dibujo: response.data.filter(o => o.status.toLowerCase() === 'dibujo'),
                    elaboracion: response.data.filter(o => o.status.toLowerCase() === 'elaboracion'),
                    entrega: response.data.filter(o => ['entrega', 'entregado'].includes(o.status.toLowerCase())),
                    facturacion: response.data.filter(o => o.status.toLowerCase() === 'facturacion') // Filtrar para facturación
                };

            } catch (error) {
                console.error("Error al cargar órdenes:", error);
                this.error = "Error al cargar las órdenes";
                this.mostrarNotificacion('Error al cargar órdenes', 'error');
            }
        },

        async actualizarEstado(id, nuevoEstado) {
            try {
                this.loading = true;

                const response = await axios.post(`/administracion/${id}/actualizar-estado`, {
                    status: nuevoEstado
                });

                this.actualizarOrdenLocal(id, nuevoEstado);
                this.mostrarNotificacion('Estado actualizado correctamente', 'success');

            } catch (error) {
                console.error("Error al actualizar:", error);
                this.mostrarNotificacion(
                    error.response?.data?.message || 
                    'Error al actualizar el estado', 
                    'error'
                );
            } finally {
                this.loading = false;
            }
        },

        actualizarOrdenLocal(id, nuevoEstado) {
            const grupos = ['dibujo', 'elaboracion', 'entrega', 'facturacion']; // Agregado facturacion
            let ordenEncontrada = null;

            for (const grupo of grupos) {
                const index = this.ordenes[grupo].findIndex(o => o.id_ot == id);
                if (index !== -1) {
                    [ordenEncontrada] = this.ordenes[grupo].splice(index, 1);
                    break;
                }
            }

            if (ordenEncontrada) {
                ordenEncontrada.status = nuevoEstado;
                const nuevoGrupo = this.obtenerGrupoPorEstado(nuevoEstado);
                this.ordenes[nuevoGrupo].push(ordenEncontrada);
            }
        },

        obtenerGrupoPorEstado(estado) {
            const estadoLower = estado.toLowerCase();
            if (estadoLower === 'dibujo') return 'dibujo';
            if (estadoLower === 'elaboracion') return 'elaboracion';
            if (estadoLower === 'facturacion') return 'facturacion'; // Nuevo caso
            return 'entrega';
        },

        async marcarComoFacturado(id_ot, index) {
            try {
                const confirmado = confirm('¿Marcar esta orden como facturada?');
                if (!confirmado) return;

                const response = await axios.post(`/administracion/${id_ot}/actualizar-estado`, {
                    status: 'Facturado',
                     [window.csrfToken]: window.csrfHash  // Token CSRF dinámico
                });

                if (response.data.success) {
                    // Eliminar de la lista actual sin recargar toda la página
                    this.ordenes.facturacion.splice(index, 1);
                    this.mostrarNotificacion('¡Orden facturada correctamente!', 'success');
                } else {
                    this.mostrarNotificacion(response.data.message || 'Error al facturar', 'error');
                }
            } catch (error) {
                console.error("Error al facturar:", error);
                this.mostrarNotificacion(
                    error.response?.data?.message || 'Error en el servidor al procesar la facturación',
                    'error'
                );
            }
        },

        // Resto de los métodos permanecen igual...
        obtenerSiguienteEstado(estado) {
            const estadoLower = estado.toLowerCase();
            if (estadoLower === 'dibujo') return 'elaboracion';
            if (estadoLower === 'elaboracion') return 'entrega';
            return 'entregado';
        },

        mostrarNotificacion(mensaje, tipo = 'success') {
            // Verificar si existen los elementos del DOM
            const toastEl = document.getElementById('liveToast');
            const toastMsg = document.getElementById('toastMessage');
            
            if (!toastEl || !toastMsg) {
                console.error('Elementos de notificación no encontrados');
                alert(mensaje); // Fallback básico
                return;
            }

            // Configurar el toast
            toastMsg.textContent = mensaje;
            toastEl.classList.remove('text-bg-success', 'text-bg-danger', 'text-bg-warning', 'text-bg-info');
            
            // Mapear tipos de notificación a clases de Bootstrap
            const typeClass = {
                'success': 'text-bg-success',
                'error': 'text-bg-danger',
                'warning': 'text-bg-warning',
                'info': 'text-bg-info'
            }[tipo] || 'text-bg-primary';

            toastEl.classList.add(typeClass);
            
            // Mostrar el toast
            const toast = new bootstrap.Toast(toastEl);
            toast.show();

            // Limpiar después de 5 segundos
            setTimeout(() => {
                toast.hide();
            }, 5000);
        },
        
        async eliminarOrden(id_ot) {
            try {
                this.loading = true;
                const response = await axios.delete(`/administracion/${id_ot}/eliminar`);

                if (response.data.success) {
                    // Eliminar la orden del arreglo local
                    const index = this.ordenes.entrega.findIndex(o => o.id_ot === id_ot);
                    if (index !== -1) {
                        this.ordenes.entrega.splice(index, 1);
                    }
                    this.mostrarNotificacion('Orden eliminada correctamente', 'success');
                } else {
                    this.mostrarNotificacion(response.data.message || 'No se pudo eliminar la orden', 'error');
                }
            } catch (error) {
                console.error("Error al eliminar la orden:", error);
                this.mostrarNotificacion(
                    error.response?.data?.message || 'Error al eliminar la orden',
                    'error'
                );
            } finally {
                this.loading = false;
            }
        },
        
        async confirmarPago(id_pedido) {
            if (!id_pedido) {
              console.error('ID de pedido inválido');
              return;
            }

            const confirmado = confirm('¿Deseas marcar este pedido como pagado?');
            if (!confirmado) return;

            try {
              const response = await fetch(`/administracion/${id_pedido}/pagar`, {
                method: 'POST',
                headers: {
                  'X-Requested-With': 'XMLHttpRequest',
                  'Content-Type': 'application/json'
                }
              });

              if (!response.ok) {
                throw new Error('No se pudo marcar como pagado');
              }

              const data = await response.json();
              alert(data.message || 'Pedido marcado como pagado');
              this.cargarOrdenes();

            } catch (error) {
              console.error(error);
              alert('Ocurrió un error al marcar como pagado');
            }
        },
       async copiarEtiqueta() {
        try {
            if (!this.ordenSeleccionada?.id_ot) {
                alert('Por favor selecciona una orden primero');
                return;
            }

            // Cargar html2canvas si no está disponible
            if (typeof html2canvas === 'undefined') {
                await this.cargarHtml2Canvas();
            }

            const element = document.getElementById('etiquetaOrden');
            if (!element) {
                alert('Error en el sistema de etiquetas');
                return;
            }

            // Generar imagen directamente (sin cambiar visibilidad)
            const canvas = await html2canvas(element, {
                scale: 2,
                logging: false,
                useCORS: true,
                backgroundColor: '#FFFFFF' // Fondo blanco
            });

            // Copiar al portapapeles
            canvas.toBlob(async (blob) => {
                try {
                    await navigator.clipboard.write([
                        new ClipboardItem({ 'image/png': blob })
                    ]);
                    alert('La etiqueta se ha copiado al portapapeles\n\nPuedes pegarla (Ctrl+V) donde la necesites');
                } catch (error) {
                    console.error('Error al copiar:', error);
                    alert('No se pudo copiar al portapapeles. Por favor intenta nuevamente.');
                }
            });
        
        } catch (error) {
            console.error('Error:', error);
            alert('Error al generar la etiqueta');
                }
        },
        ejemplo(){
            //solo para actualizar el git
        },
        
    },
    mounted() {
        this.cargarOrdenes();
    }
}).mount('#app');
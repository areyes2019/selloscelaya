const {createApp, ref} = Vue

	createApp({
		data(){
			return{
				pedido:[],
				articulo:[],
				articulos:[],
				saludo:"hola",
                selectedItems: [], // Array para almacenar los IDs seleccionados
                selectAll: false,
                filtroNombre: '',
                filtroModelo: '',
                filtroProveedor: '',
                currentPage: 1,
                itemsPerPage: 10, // Puedes ajustar este número
                totalItems: 0,
                allArticles: [], // Aquí guardaremos todos los artículos sin filtrar
                filteredArticles: [], // Artículos después de aplicar filtro
                isLoading: false,
                magenModalUrl: '',
                nombreModal: '',
                precioModal: 0,
                modalInstance: null,
                isCopyingWhatsApp: false
			}
		},
		methods:{
            async cargarArticulos() {
                try {
                    const response = await axios.get('mostrar_articulos');
                    this.allArticles = response.data;
                    this.totalItems = this.allArticles.length;
                    this.filtrarArticulos();
                } catch (error) {
                    console.error('Error al cargar artículos:', error);
                    this.mostrarNotificacion('error', 'Error al cargar los artículos');
                }
            },
            abrirModal(articulo) {
                this.imagenModalUrl = 'public/img/catalogo/' + articulo.img;
                this.nombreModal = articulo.nombre;
                this.precioModal = articulo.precio_pub; // Agregamos el precio
                
                // Inicializar el modal si no existe la instancia
                if (!this.modalInstance) {
                    const modalElement = document.getElementById('imagenModal');
                    this.modalInstance = new bootstrap.Modal(modalElement);
                }
                
                this.modalInstance.show();
            },

            // Método para copiar imagen para WhatsApp
            copiarParaWhatsApp() {
                // Lógica para copiar imagen para WhatsApp
                // Puedes implementar lo que necesites aquí
                console.log('Copiando imagen para WhatsApp:', this.imagenModalUrl);
                
                // Ejemplo básico: copiar URL al portapapeles
                navigator.clipboard.writeText(this.imagenModalUrl)
                    .then(() => {
                        this.mostrarNotificacion('success', 'URL de la imagen copiada al portapapeles');
                    })
                    .catch(err => {
                        console.error('Error al copiar:', err);
                        this.mostrarNotificacion('error', 'Error al copiar la imagen');
                    });
            },

            filtrarArticulos() {
                // Si no hay filtros, mostrar todos los artículos
                if (!this.filtroNombre && !this.filtroModelo && !this.filtroProveedor) {
                    this.filteredArticles = [...this.allArticles];
                } else {
                    this.filteredArticles = this.allArticles.filter(articulo => {
                        const nombreMatch = this.filtroNombre ? 
                            articulo.nombre.toLowerCase().includes(this.filtroNombre.toLowerCase()) : true;
                        
                        const modeloMatch = this.filtroModelo ? 
                            (articulo.modelo ? articulo.modelo.toLowerCase().includes(this.filtroModelo.toLowerCase()) : false) : true;
                        
                        const proveedorMatch = this.filtroProveedor ? 
                            (articulo.nombre_proveedor ? articulo.nombre_proveedor.toLowerCase().includes(this.filtroProveedor.toLowerCase()) : false) : true;
                        
                        return nombreMatch && modeloMatch && proveedorMatch;
                    });
                }
                
                this.totalItems = this.filteredArticles.length;
                this.currentPage = 1; // Resetear a la primera página al filtrar
            },
            formatNumber(num) {
                // Convertir a número y verificar si es válido
                const number = parseFloat(num);
                
                // Si no es un número válido, retornar $0.00
                if (isNaN(number)) {
                    return '$0.00';
                }
                
                // Formatear el número válido
                return '$' + number.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            },
            // Métodos para la paginación
            paginatedArticles() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                return this.filteredArticles.slice(start, end);
            },

            changePage(page) {
                this.currentPage = page;
            },

            totalPages() {
                return Math.ceil(this.totalItems / this.itemsPerPage);
            },
			cambio_rapido(data){
				axios.get('/editar_rapido/'+data).then((response)=>{
					this.articulo = response.data.data[0];
					console.log(this.articulo);
					$('#quickEditModal').modal('show');
				})
			},
			async guardarEdicionRapida(idArticulo) {
                try {
                    // Confirmación antes de guardar
                    const confirmar = confirm('¿Estás seguro de que deseas actualizar este artículo?');
                    if (!confirmar) return;
                    
                    // Obtener los datos del artículo desde el modelo Vue
                    const datosActualizados = {
                        nombre: this.articulo.nombre,
                        modelo: this.articulo.modelo,
                        precio_pub: this.articulo.precio_pub,
                        precio_dist: this.articulo.precio_dist,
                        precio_prov: this.articulo.precio_prov,
                        categoria: this.articulo.categoria,
                    };
                    
                    // Enviar los datos al servidor
                    const response = await axios.post(`/actualizar_rapido/${idArticulo}`, datosActualizados);
                    
                    // Mostrar notificación de éxito
                    this.mostrarNotificacion('error','Artículo actualizado correctamente');
                    
                    // Cerrar el modal
                    const modalId = 'quickEditModal';
                    const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
                    if (modal) modal.hide();
                    
                    // Recargar la página después de 1 segundo
                    this.cargarArticulos();
                    
                } catch (error) {
                    console.error('Error al actualizar el artículo:', error);
                    notify('Error al actualizar el artículo');
                    
                    // Mostrar errores de validación si existen
                    if (error.response && error.response.data.errors) {
                        this.errores = error.response.data.errors;
                        // Mostrar cada error individualmente
                        for (const [campo, mensajes] of Object.entries(this.errores)) {
                            notify(`${campo}: ${mensajes.join(', ')}`);
                        }
                    }
                }
            },
            async cambiar_visible(articuloId, event) {
                const switchElement = event.target;
                const isChecked = switchElement.checked; // Nuevo estado del switch (true/false)
                const nuevoEstadoVisible = isChecked ? 1 : 0; // Convertir a 0 o 1 para el backend

                try {
                    // console.log(`Cambiando visibilidad para artículo ${articuloId} a ${nuevoEstadoVisible}`);
                    const response = await axios.post(`cambiar-visibilidad/${articuloId}`, {
                        visible: nuevoEstadoVisible
                    },{
                        headers:{
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });

                    if (response.data && response.data.success) {
                        this.mostrarNotificacion('success','Este artículo ahora es visible');
                        // Actualizar el title del switch dinámicamente
                        switchElement.title = isChecked ? 'Marcar como Oculto' : 'Marcar como Visible';

                        // Opcional: Si tienes 'articulos' en tu data de Vue y quieres actualizarlo localmente
                        // sin recargar la página, podrías hacer algo como:
                        // const articuloIndex = this.articulos.findIndex(art => art.id_articulo === articuloId);
                        // if (articuloIndex !== -1) {
                        //     this.articulos[articuloIndex].visible = nuevoEstadoVisible;
                        // }

                    } else {
                        // Si el backend devuelve success: false o una respuesta inesperada
                        this.mostrarNotificacion('error', response.data.message || 'Error al actualizar la visibilidad.');
                        // Revertir el switch a su estado anterior porque la actualización falló
                        switchElement.checked = !isChecked;
                    }

                } catch (error) {
                    console.error('Error al cambiar visibilidad:', error);
                    let errorMessage = 'Ocurrió un error de red o del servidor.';
                    if (error.response && error.response.data && error.response.data.message) {
                        errorMessage = error.response.data.message;
                    }
                    this.mostrarNotificacion('error', errorMessage);
                    // Revertir el switch a su estado anterior porque la actualización falló
                    switchElement.checked = !isChecked;
                }
            },
            mostrarNotificacion(tipo, mensaje) {
                // Opcional: puedes personalizar el color según el tipo
                const colores = {
                    success: '#4CAF50',
                    error: '#F44336',
                    warning: '#FF9800',
                    info: '#2196F3'
                };
                
                const color = colores[tipo] || colores.info;
                notify(mensaje, color);
            },
            // Método para seleccionar/deseleccionar todos los checkboxes
            toggleSelectAll(event) {
                const isChecked = event.target.checked;
                this.selectedItems = [];
                
                if (isChecked) {
                    // Seleccionar todos los artículos visibles en la página actual
                    this.paginatedArticles().forEach(articulo => {
                        this.selectedItems.push(articulo.id_articulo);
                    });
                    
                    // Marcar visualmente los checkboxes
                    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                        checkbox.checked = true;
                    });
                } else {
                    // Desmarcar todos los checkboxes
                    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                        checkbox.checked = false;
                    });
                }
            },
            // Método para eliminar los artículos seleccionados
            async eliminarSeleccionados() {
                if (this.selectedItems.length === 0) {
                    this.mostrarNotificacion('warning', 'No hay artículos seleccionados');
                    return;
                }
                
                const confirmar = confirm(`¿Estás seguro de eliminar los ${this.selectedItems.length} artículos seleccionados?`);
                if (!confirmar) return;
                
                try {
                    this.isLoading = true;
                    const response = await axios.post('eliminar_masivo', {
                        ids: this.selectedItems
                    });
                    
                    if (response.data.success) {
                        this.mostrarNotificacion('success', `Se eliminaron ${response.data.deleted} artículos`);
                        // Recargar los artículos después de eliminar
                        await this.cargarArticulos();
                        this.selectedItems = []; // Limpiar selección
                    } else {
                        this.mostrarNotificacion('error', response.data.message || 'Error al eliminar artículos');
                    }
                } catch (error) {
                    console.error('Error al eliminar artículos:', error);
                    this.mostrarNotificacion('error', 'Ocurrió un error al eliminar los artículos');
                } finally {
                    this.isLoading = false;
                }
            },
            updateSelectAllState() {
                const selectAllCheckbox = document.getElementById('selectAll');
                if (!selectAllCheckbox) return;
                
                // Verificar si todos los artículos en la página están seleccionados
                const allSelected = this.paginatedArticles().every(articulo => 
                    this.selectedItems.includes(articulo.id_articulo)
                );
                
                selectAllCheckbox.checked = allSelected;
            },
            async copiarParaWhatsApp() {
                this.isCopyingWhatsApp = true;
                try {
                    const modalImagen = document.querySelector('#modalImagen');

                    const canvas = await html2canvas(modalImagen, {
                        backgroundColor: '#fff', // o null para fondo transparente
                        scale: 2
                    });

                    canvas.toBlob(async (blob) => {
                        if (!blob) throw new Error('No se pudo generar la imagen');

                        await navigator.clipboard.write([
                            new ClipboardItem({ [blob.type]: blob })
                        ]);

                        this.mostrarNotificacion('success', 'Captura copiada al portapapeles');
                    }, 'image/png');
                } catch (error) {
                    console.error('Error al copiar:', error);
                    this.mostrarNotificacion('error', 'No se pudo copiar la imagen');
                } finally {
                    this.isCopyingWhatsApp = false;
                }
            }

		},
		mounted(){
            this.cargarArticulos();
		}
}).mount('#app')
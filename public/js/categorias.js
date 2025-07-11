const {createApp, ref} = Vue

	createApp({
		data() {
        return {
            categorias: [],
            loading: true,
            error: null,
            modal: null,
            modalTitle: 'Nueva Categoría',
            processing: false,
            formData: {
                id_categoria: '',
                nombre: ''
            },
            errors: {
                nombre: ''
            }
        }
    },
    methods: {
        /**
         * Obtiene todas las categorías desde el servidor
         */
        obtenerCategorias() {
            this.loading = true;
            this.error = null;
            axios.get('/categorias/show')
                .then(response => {
                    this.categorias = response.data;
                })
                .catch(error => {
                    this.error = 'Error al cargar las categorías';
                    console.error('Error:', error);
                })
                .finally(() => {
                    this.loading = false;
            });
        },
	        
        /**
         * Muestra el modal para crear una nueva categoría
         */
        mostrarModalCrear() {
            this.resetForm();
            this.modalTitle = 'Nueva Categoría';
            this.modal.show();
        },
	        
        /**
         * Muestra el modal para editar una categoría existente
         * @param {Object} categoria - La categoría a editar
         */
        mostrarModalEditar(categoria) {
            this.resetForm();
            this.modalTitle = 'Editar Categoría';
            this.formData = {
                id_categoria: categoria.id_categoria,
                nombre: categoria.nombre
            };
            this.modal.show();
        },
	        
        /**
         * Guarda o actualiza una categoría
         */
        guardarCategoria() {
            this.processing = true;
            this.clearErrors();
            
            const url = this.formData.id_categoria 
                ? base_url + `categorias/update/${this.formData.id_categoria}`
                : base_url + 'categorias/store';
            
            const method = this.formData.id_categoria ? 'put' : 'post';
            
            axios[method](url, this.formData)
                .then(response => {
                    this.obtenerCategorias();
                    this.modal.hide();
                    this.showToast(response.data.message || 'Operación exitosa');
                })
                .catch(error => {
                    if (error.response && error.response.status === 422) {
                        // Manejar errores de validación
                        const validationErrors = error.response.data.errors;
                        for (const field in validationErrors) {
                            if (this.errors.hasOwnProperty(field)) {
                                this.errors[field] = validationErrors[field][0];
                            }
                        }
                    } else {
                        this.showToast('Error en la operación', 'error');
                    }
                })
                .finally(() => {
                    this.processing = false;
                });
        },
	        
        /**
         * Elimina una categoría
         * @param {Number} id - ID de la categoría a eliminar
         */
        eliminarCategoria(id) {
            if (confirm('¿Estás seguro de eliminar esta categoría?')) {
                axios.get('categorias/delete/'+id)
                    .then(response => {
                        this.obtenerCategorias();
                        notify('Categoría eliminada correctamente');
                    })
                    .catch(error => {
                        this.showToast('Error al eliminar la categoría', 'error');
                        console.error('Error:', error);
                    });
            }
        },
	        
        /**
         * Resetea el formulario a valores por defecto
         */
        resetForm() {
            this.formData = {
                id_categoria: '',
                nombre: ''
            };
            this.clearErrors();
        },
	        
        /**
         * Limpia todos los mensajes de error
         */
        clearErrors() {
            this.errors = {
                nombre: ''
            };
        },
	        
        /**
         * Muestra una notificación toast
         * @param {String} message - Mensaje a mostrar
         * @param {String} type - Tipo de notificación (success, error, etc.)
         */
        showToast(message, type = 'success') {
            // Implementación básica con SweetAlert
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: type,
                    title: message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            } else {
                // Fallback a alert básico si no hay SweetAlert
                alert(message);
            }
        }
    },
	mounted(){
		this.obtenerCategorias();
	}
}).mount('#app')
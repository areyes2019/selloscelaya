const {createApp, ref} = Vue

	createApp({
		data(){
			return{
				pedido:[],
				articulo:[],
				articulos:[],
				saludo:"hola"
			}
		},
		methods:{
			cambio_rapido(data){
				axios.get('/editar_rapido/'+data).then((response)=>{
					this.articulo = response.data.data[0];
					$('#quickEditModal').modal('show');
				})
			},
			async guardarEdicionRapida(idArticulo) {
	        try {
	            // Obtener los datos del artículo desde el modelo Vue
	            const datosActualizados = {
	                nombre: this.articulo.nombre,
	                modelo: this.articulo.modelo,
	                precio_pub: this.articulo.precio_pub,
	                precio_dist: this.articulo.precio_dist,
	                precio_prov: this.articulo.precio_prov
	            };
	            
	            // Enviar los datos al servidor
	            const response = await axios.post(`/actualizar-articulo/${idArticulo}`, datosActualizados);
	            
	            // Mostrar notificación de éxito
	            this.mostrarNotificacion('success', 'Artículo actualizado correctamente');
	            
	            // Cerrar el modal
	            const modalId = 'quickEditModal';
	            const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
	            modal.hide();
	            
	            // Opcional: Actualizar datos en la lista
	            this.obtenerArticulos();
	            
	        } catch (error) {
	            console.error('Error al actualizar el artículo:', error);
	            this.mostrarNotificacion('error', 'Error al actualizar el artículo');
	            
	            // Mostrar errores de validación si existen
	            if (error.response && error.response.data.errors) {
	                this.errores = error.response.data.errors;
	            }
	        }
	    },
	    
	    mostrarNotificacion(tipo, mensaje) {
	        // Implementa tu sistema de notificaciones (Toast, SweetAlert, etc.)
	        console.log(`${tipo}: ${mensaje}`);
	        // Ejemplo con SweetAlert:
	        // Swal.fire({ icon: tipo, title: mensaje, timer: 3000 });
	    }
		},
		mounted(){

		}
}).mount('#app')
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
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                    
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
            }
		},
		mounted(){

		}
}).mount('#app')
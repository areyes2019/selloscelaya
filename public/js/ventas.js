const {createApp, ref} = Vue
	createApp({
		data(){
			return{
				nombre:"",
				telefono:"",
				pedido:[],
				articulos:[],
				anticipo: 0,
				articulo:"",
				precio_unitario:"",
				articuloId:"",
				articuloSeleccionado: null,
            	cantidad: 1,
            	itemsPedido: [],
            	searchQuery: '',
		        showResults: false,
		        selectedIndex: -1,
		        articuloSeleccionado: null,
		        selectedIndex: -1,
		        bancos: [],
    			bancoSeleccionado: null
			}
		},
		computed: {
	        total() {
			    return this.itemsPedido.reduce((sum, item) => sum + parseFloat(item.subtotal), 0);
			  },
			  saldo() {
			    const saldoCalculado = this.total - parseFloat(this.anticipo);
			    return saldoCalculado > 0 ? saldoCalculado : 0;
			},
		    filteredArticulos() {
		        if (!this.searchQuery.trim()) return [];
		        const query = this.searchQuery.trim().toLowerCase();
		        return this.articulos.filter(articulo => 
		            (articulo.nombre && articulo.nombre.toLowerCase().includes(query)) || 
		            (articulo.modelo && articulo.modelo.toLowerCase().includes(query))
		        );
		    }
	    },
		methods:{
			async listarBancos() {
			    try {
			      const response = await axios.get('/cuentas/listar');
			      this.bancos = response.data;
			    } catch (error) {
			      console.error('Error al obtener las cuentas bancarias:', error);
			      alert('Error al cargar las cuentas bancarias');
			    }
			},

			//esto maneja el selec dinamico
			async cargarArticulos() {
		        try {
		            const response = await axios.get('/ventas/stock');
		            this.articulos = response.data;
		        } catch (error) {
		            console.error('Error:', error);
		            alert('Error al cargar artículos');
		        }
		    },
		    
	        handleInput() {
		        this.showResults = this.searchQuery.length > 0;
		        this.selectedIndex = -1;
		    },
		    
		    handleKeyDown(e) {
		        // Permitir que ESC funcione siempre
			    if (e.key === 'Escape') {
			        this.showResults = false;
			        this.selectedIndex = -1;
			        return;
			    }

			    // Si el dropdown no está visible o no hay resultados, no hacer nada más
			    if (!this.showResults || this.filteredArticulos.length === 0) return;

			    // Prevenir comportamiento por defecto solo si se presionan teclas relevantes
			    if (['ArrowDown', 'ArrowUp', 'Enter'].includes(e.key)) {
			        e.preventDefault();
			    }
		        
		        switch(e.key) {
		            case 'ArrowDown':
		                this.selectedIndex = 
		                    this.selectedIndex < this.filteredArticulos.length - 1 
		                    ? this.selectedIndex + 1 
		                    : 0;
		                this.scrollToItem();
		                break;
		                
		            case 'ArrowUp':
		                this.selectedIndex = 
		                    this.selectedIndex > 0 
		                    ? this.selectedIndex - 1 
		                    : this.filteredArticulos.length - 1;
		                this.scrollToItem();
		                break;
		                
		            case 'Enter':
		                if (this.selectedIndex >= 0) {
		                    this.selectItem(this.filteredArticulos[this.selectedIndex]);
		                }
		                break;
		        }
		    },
		    
		    scrollToItem() {
		        this.$nextTick(() => {
		            const container = this.$refs.dropdownContainer;
        			const selectedItem = this.$refs.activeItem;
		            
		            if (container && selectedItem) {
		                // Calcular posición para el scroll
		                const containerTop = container.scrollTop;
		                const containerBottom = containerTop + container.offsetHeight;
		                const itemTop = selectedItem.offsetTop;
		                const itemBottom = itemTop + selectedItem.offsetHeight;
		                
		                if (itemTop < containerTop) {
		                    container.scrollTop = itemTop;
		                } else if (itemBottom > containerBottom) {
		                    container.scrollTop = itemBottom - container.offsetHeight;
		                }
		            }
		        });
		    },
		    
		    selectItem(articulo) {
		        this.articuloSeleccionado = articulo;
		        this.searchQuery = `${articulo.nombre} - ${articulo.modelo}`;
		        this.articuloId = articulo.id_articulo;
		        this.precio_unitario = articulo.precio_pub;
		        this.showResults = false;
		        this.selectedIndex = -1;
		    },
		    
		    onBlur() {
		        setTimeout(() => {
		            this.showResults = false;
		        }, 200);
		    },

			//hata aqui
			mostrar_existencias(){
					
			},
			async listarArticulos() {
	          try {
	            const response = await axios.get('/ventas/mostrar_articulos');
	            if (response.data && response.data.length > 0) {
	              this.articulos = response.data;
	            } else {
	              this.mensajeError = 'No se encontraron artículos';
	            }
	          } catch (error) {
	            console.error('Error al obtener los artículos:', error);
	            this.articulos = [];
	            if (error.response) {
	              if (error.response.status === 404) {
	                this.mensajeError = 'Endpoint no encontrado';
	              } else if (error.response.status === 500) {
	                this.mensajeError = 'Error interno del servidor';
	              } else {
	                this.mensajeError = 'Error: ' + error.response.status;
	              }
	            } else if (error.request) {
	              this.mensajeError = 'No se pudo conectar al servidor';
	            } else {
	              this.mensajeError = 'Error al realizar la solicitud';
	            }
	          }
	        },
	        actualizarAnticipo(event) {
			    // Validar que el anticipo no sea mayor al total
			    const valorAnticipo = parseFloat(event.target.value) || 0;
			    if (valorAnticipo > this.total) {
			      alert('El anticipo no puede ser mayor al total');
			      this.anticipo = this.total;
			      return;
			    }
			    this.anticipo = valorAnticipo;
			},
			actualizarPrecio() {
		    	if (this.articuloSeleccionado) {
				    this.precio_unitario = this.articuloSeleccionado.precio_publico;
				} else {
				    this.precio_unitario = '';
				}
		    },
	       	agregarArticulo() {
			  // Validación mejorada
			  if (!this.articuloId) {
			    alert('Por favor seleccione un artículo');
			    return;
			  }
			  
			  if (!this.cantidad || this.cantidad < 1) {
			    alert('La cantidad debe ser al menos 1');
			    return;
			  }

			  // Buscar el artículo seleccionado
			  const articuloSeleccionado = this.articulos.find(
			    articulo => articulo.id_articulo == this.articuloId
			  );
			  
			  if (!articuloSeleccionado) {
			    alert('Artículo no encontrado');
			    return;
			  }
			  
			  // Verificar si el artículo ya existe en itemsPedido
			  const articuloExistente = this.itemsPedido.find(
			    item => item.id === articuloSeleccionado.id_articulo
			  );
			  
			  if (articuloExistente) {
			    if (confirm('Este artículo ya está en la lista. ¿Desea actualizar la cantidad en lugar de agregar uno nuevo?')) {
			      // Actualizar la cantidad del artículo existente
			      articuloExistente.cantidad += parseInt(this.cantidad);
			      articuloExistente.subtotal = (articuloExistente.cantidad * articuloExistente.precio_unitario).toFixed(2);
			      
			      // Limpiar campos
			      this.resetearCamposArticulo();
			      return;
			    } else {
			      return; // El usuario decidió no hacer nada
			    }
			  }
			  
			  // Crear el item del pedido
			  const itemPedido = {
			    id: articuloSeleccionado.id_articulo,
			    nombre: articuloSeleccionado.nombre,
			    modelo: articuloSeleccionado.modelo,
			    cantidad: parseInt(this.cantidad) || 1,
			    precio_unitario: parseFloat(articuloSeleccionado.precio_pub) || 0,
			    subtotal: (parseFloat(articuloSeleccionado.precio_pub) * parseInt(this.cantidad)).toFixed(2)
			  };
			  
			  // Agregar al array de pedido
			  this.itemsPedido.push(itemPedido);
			  
			  // Limpiar campos
			  this.resetearCamposArticulo();
			  
			  // Opcional: hacer foco en el autocomplete
			  this.$nextTick(() => {
			    document.getElementById('autocomplete_input').focus();
			  });
			},
			// Añade este método a tus methods:
			resetearCamposArticulo() {
			  this.articuloId = null;
			  this.articuloSeleccionado = null;
			  this.searchQuery = '';
			  this.precio_unitario = '';
			  this.cantidad = 1;
			  this.showResults = false;
			},
			eliminarItem(index) {
			  if (confirm('¿Está seguro que desea eliminar este artículo?')) {
			    if (index >= 0 && index < this.itemsPedido.length) {
			      this.itemsPedido.splice(index, 1);
			      
			      // Opcional: Notificación más elegante que alert
			      // this.mostrarNotificacion('Artículo eliminado', 'success');
			    }
			  }
			},
	        formatCurrency(value) {
			    if (value === null || value === undefined) return '$0.00';
			    return new Intl.NumberFormat('es-MX', {
			      style: 'currency',
			      currency: 'MXN',
			    }).format(value);
			},

		},
		mounted(){
			this.listarArticulos();
			this.listarBancos();
			this.cargarArticulos();
		},
		
}).mount('#app')
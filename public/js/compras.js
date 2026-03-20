import AutocompleteSelect from './componentes/autocomplete-select.js';
const { createApp, ref } = Vue
createApp({
    //esto tambien se integra al componente
    components: {
        AutocompleteSelect, // Lo registras aquí
    },
    data() {
      return {
        //todo esto hay que integralo en el componente
        search: '',
        showList: false,
        highlightedIndex: -1,
        selectedArticulo: null,
        cantidadArticulo: 1,
        //componente
        articulos:[],
        compras:[],
        lista:[],
        independiente:[],
        cantidad:"1",
        anticipo:"",
        sub_total:"",
        iva:"",
        total:"",
        saldo:"",
        hide:"",
        pago:"",
        saldo:"",
        sugerido:"",
        utilidad:"",
        articulo_ind:"",
        cantidad_ind:"",
        precio_ind:"",
        descuento:"",
        //ocultar diferentes botones
        display_pagado:"",
        display_recibido:"",
        cuentaSeleccionada: null,
      }
    },
    //componente
    watch: {
        modelValue(newVal) {
            const match = this.options.find(opt => opt.id_articulo === newVal);
            this.search = match ? match.nombre : '';
        },
        search(newVal) {
            if (newVal !== '') {
                this.showList = true;
            } else {
            this.showList = true; // también mostrar cuando está vacío
            }
            this.highlightedIndex = -1;
        }
    },
    computed: {
      filteredOptions() {
        const term = this.search.toLowerCase();
        return this.options.filter(opt => opt.nombre.toLowerCase().includes(term));
      }
    },
    //componente
    methods:{
        mostrar_articulos(){
            var id = this.$refs.proveedor.innerHTML;
            var url = '/select_compras/'+id;
            //console.log(pedido);
            axios.get(url)
                  .then(response => {
                    this.lista = response.data;
                })
              .catch(error => {
              console.error(error);
            });
        },
        /*Estas funciones manejan el select*/
        
        agregarArticulo() {
            if (!this.selectedArticulo) {
                alert('Por favor seleccione un artículo');
                return;
            }
            
            if (!this.cantidad || this.cantidad <= 0) {
                alert('Por favor ingrese una cantidad válida');
                return;
            }
            
            var pedido = this.$refs.pedido.innerHTML;
            
            axios.post('/agregar_articulo_compras', {
                'id_articulo': this.selectedArticulo,
                'pedidos_id': pedido,
                'cantidad': this.cantidad // Agregamos la cantidad al request
            }).then((response) => {
                if (response.data == 1) {
                    alert('Este producto ya fue agregado');
                } else {
                    this.mostrar_lineas();
                    // Opcional: resetear los campos después de agregar
                    this.selectedArticulo = '';
                    this.cantidad = 1;
                }
            }).catch(error => {
                console.error('Error al agregar artículo:', error);
                alert('Ocurrió un error al agregar el artículo');
            });
        },
        add_articulo(data){
            var pedido = this.$refs.pedido.innerHTML;
            //var descuento = this.$refs.descuento.innerHTML;
            axios.post('/agregar_articulo_compras',{
              'id_articulo':data,
              'pedidos_id':pedido,
            }).then((response)=>{
                if (response.data == 1) {
                  alert('Este producto ya agregado')
                }else{
                  this.mostrar_lineas();
                }
            })
        },
        modificar_cantidad(data) {
            const url = "/modificar_cantidad_compras";
            const cantidad = event.target.value;
            console.log(data);
            axios.post(url, {
                id: data,
                cantidad: cantidad,
            })
          .then(response => {
            this.mostrar_lineas(); // Usamos arrow function para mantener el contexto de 'this'
          })
          .catch(error => {
            console.error("Error al modificar cantidad:", error);
            // Podrías añadir aquí un mensaje de error al usuario
          });
        },
        mostrar_lineas(){
            var pedido = this.$refs.pedido.innerHTML;
            axios.get('/mostrar_detalles_compras/'+pedido).then((response)=>{
                this.articulos = response.data['articulo'];
                this.sub_total = response.data['sub_total'];
                this.iva = response.data['iva'];
                this.total = response.data['total'];
                this.pago = response.data['abono'];
                this.saldo = response.data['saldo'];
                this.sugerido = response.data['sugerido'];
                this.descuento = response.data['descuento'];
                
            })
        },
        display() {
            const pedidoId = this.$refs.pedido.textContent.trim();
            const url = `/pedido/${pedidoId}`;
            
            axios.get(url)
                .then(response => {
                    // Verifica que exista data y tenga la estructura esperada
                    const pedidoData = response.data || {};
                    
                    // Usa valores booleanos directamente
                    this.display_pagado = pedidoData.pagado; // Cambiado a "pagada" para coincidir con backend
                    this.display_recibido = pedidoData.entregada; // Cambiado a "entregada"
                })
                .catch(error => {
                    console.error('Error al obtener datos del pedido:', error);
                    // Opcional: establecer valores por defecto en caso de error
                    this.display_pagado = false;
                    this.display_recibido = false;
                });
        },
        borrar_linea(data){
            console.log(data);
            //console.log(data);

            if (window.confirm("¿Realmente quieres borrar esta linea?")) {
                axios.get('/borrar_linea_compras/'+data).then((response)=>{
                    this.mostrar_lineas();
                })
            }
        },
        
        agregar_pago() {
            let pedido = this.$refs.pedido.textContent.trim();
            if (!this.cuentaSeleccionada) {
                alert('Por favor seleccione una cuenta');
                return;
            }
            
            axios.post('/pago_compras', {
                'banco': this.cuentaSeleccionada, // ID de la cuenta seleccionada
                'id': pedido // Solo enviamos el ID del pedido
            }).then((response) => {
                if (response.data.status === 'ok') {
                    alert(response.data.message);
            
                    // Redireccionar después de 2 segundos
                    setTimeout(() => {
                        window.location.href = '/compras'; // Ajusta esta ruta según tu aplicación
                    }, 2000);
                } else {
                    alert(response.data.message);
                }
            }).catch((error) => {
                console.error('Error:', error);
                alert('Ocurrió un error al procesar el pago');
            });
        },

        descargar_img(){
            var pedido = this.$refs.pedidos_id.innerHTML;
            html2canvas(document.querySelector("#ticket")).then(canvas => {
            canvas.toBlob(function(blob) {
              window.saveAs(blob, 'TK-'+cotizacion+'.jpg');
            });
            });
        },
        tabla(){
            $( document ).ready(function() {
              new DataTable('#articulos');
            });
        },
        recibida() {
            const pedido = this.$refs.pedido.innerText;
            const url = "/recibido_compras";

            // 🔥 Confirmación clara
            const confirmar = confirm(
                "¿Confirmas que deseas marcar esta orden como RECIBIDA?\n\n" +
                "⚠ Esto agregará los artículos al inventario."
            );

            if (!confirmar) return;

            axios.post(url, {
                pedido: pedido
            })
            .then((response) => {

                const res = response.data;

                if (response.status === 200) {
                    notify("✅ Inventario actualizado correctamente");

                    // 🔥 RECARGAR LA PÁGINA
                    setTimeout(() => {
                        location.reload();
                    }, 800); // pequeño delay para que se vea el mensaje

                } else {
                    notify("⚠ " + res.message);
                }

            })
            .catch((error) => {
                console.error(error);
                notify("❌ Error al procesar la solicitud");
            });
        }
    },
    mounted(){
      this.mostrar_lineas();
      this.mostrar_articulos();
      this.display();
    }
}).mount('#app')
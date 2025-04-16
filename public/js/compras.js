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
        agregar_pago(){
            var pedido = this.$refs.pedido.innerHTML;
            var monto_total = this.$refs.monto_total.innerHTML; // Asegúrate de tener esta referencia

            if (window.confirm("¿Realmente quieres marcar este pedido como pagado?")){
                axios.post('/pago_compras',{
                    'id': pedido,
                    'monto_total': monto_total
                }).then((response)=>{
                        notify('Pagado');
                        setTimeout(function() {
                            window.location.href = "/compras";
                        }, 1000);
                })
            }
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
        recibida(){
            var pedido = this.$refs.pedido.innerHTML;
            var url = "/recibido_compras";
            if (window.confirm("Esto agregara el producto al inventario")){
            axios.post(url,{
              'pedido':pedido
            }).then((response)=>{
                if (response.data.flag == 1) {
                    notify('Se ajusto el inventario y se marco como entregada la orden');
                    this.mostrar_lineas();
                    this.display();
                }
                notify(response.data.message);
            })
        }
      }
    },
    mounted(){
      this.mostrar_lineas();
      this.mostrar_articulos();
      this.display();
    }
}).mount('#app')
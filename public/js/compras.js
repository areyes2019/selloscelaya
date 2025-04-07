const { createApp, ref } = Vue

  createApp({
    data() {
      return {
        selectedArticulo: null,
        cantidadArticulo: 1,
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
        display_pagado: 0,
        display_recibido: 0,
      }
    },
    methods:{
        mostrar_articulos(){
            var id = this.$refs.proveedor.innerHTML;
            var url = '/mostrar_articulos_compras/'+id;
            //console.log(pedido);
            axios.get(url)
              .then(response => {
                this.lista = response.data;
                this.tabla();
            })
          .catch(error => {
          console.error(error);
        });
      },
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
      display(){
        var me = this;
        var pedido = this.$refs.pedido.innerHTML;
        var url = "/pedido/"+ pedido;
        axios.get(url).then(function (response){
          if (response.data[0]['pagado']==1) {
            me.display_pagado = 1;
          }
          if (response.data[0]['recibido']==1) {
            me.display_recibido = 1;
          }

        })

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
        var me = this;
        var pedido = this.$refs.pedido.innerHTML;
        if (window.confirm("¿Quieres marcar como pagado?")){
          axios.post('/pago_compras',{
            'id':pedido
          }).then(function (response){
            me.mostrar_lineas();
            me.display();
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
      recibida(data){
        var me = this;
        var url = "/compra_recibida";
        if (window.confirm("Esto agregara el producto al inventario")){
        axios.post(url,{
          'pedido':data
        }).then(function (response){
          me.mostrar_lineas();
          me.display();
        })
        }
      }
    },
    mounted(){
      this.mostrar_lineas();
      //this.mostrar_articulos();
      this.display();
    }
}).mount('#app')
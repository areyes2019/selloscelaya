const { createApp, ref } = Vue

  createApp({
    data() {
      return {
        articulos:[],
        lista:[],
        independiente:[],
        cantidad:"1",
        anticipo:"",
        bancoSeleccionado:null,
        sub_total:"",
        iva:"",
        total:"",
        saldo:"",
        display:"",
        hide:"",
        pago:"",
        pagado:"",
        saldo:"",
        sugerido:"",
        utilidad:"",
        articulo_ind:"",
        cantidad_ind:"",
        precio_ind:"",
        descuento:"",
        disabled:0,
        totales:[],
        dinero_descuento:"",
        saldo:"",
        entregada:""
      }
    },
    computed: {
        hayArticulos() {
            // Verifica si hay artículos normales o independientes
            return this.articulos.length > 0 ;
            console.log(this.articulos.length);
        },
        mostrarBotonPago() {
          if (!this.totales || !this.totales.total || !this.totales.pago) return false;
          
          const total = parseFloat(this.totales.total);
          const pago = parseFloat(this.totales.pago);

          // Solo muestra el botón si el total es mayor al pago
          return total < pago;
        },
        isDisabled() {
          // Asegúrate de que 'totales' y sus valores 'total' y 'pago' existen y son válidos
          if (!this.totales || !this.totales.total || !this.totales.pago) return false;

          // Parseamos los valores para asegurar que son números
          const total = parseFloat(this.totales.total);
          const pago = parseFloat(this.totales.pago);

          // Si el pago es mayor o igual al total, deshabilitamos el input
          return pago >= total;
        }
    },
    methods:{
      mostrar_articulos(){
        var url = '/mostrar_articulos';
        axios.get(url)
          .then((response) => {
            this.lista = response.data;
            this.tabla();
        })
          .catch(error => {
          console.error(error);
        });
      },
      add_articulo(data){
        var me = this;
        var cantidad = this.$refs[data][0].value;
        var cotizacion = this.$refs.id_cotizacion.innerHTML;
        var descuento = this.$refs.descuento.innerHTML;
        axios.post('/agregar_articulo',{
          'id_articulo':data,
          'cantidad':cantidad,
          'id_cotizacion':cotizacion,
        }).then(response=>{
            if (response.data.flag == 0) {
              alert('Este producto ya agregado')
            }else{
              this.mostrar_lineas();
              this.mostrar_totales();
            }
        })
      },
      agregar_ind(){
        if (this.articulo_ind && this.cantidad_ind && this.precio_ind) {    
          var me = this;
          var cotizacion = this.$refs.id_cotizacion.innerHTML;
          axios.post('/agregar_articulo_ind',{
            'cantidad':me.cantidad_ind,
            'id_cotizacion':cotizacion,
            'descripcion':me.articulo_ind,
            'p_unitario':me.precio_ind
          }).then(function (response){
              me.mostrar_lineas();
              me.articulo_ind = "";
              me.cantidad_ind = "";
              me.precio_ind="";
          })
        }else{
          alert("No puedes dejar campos vacios");
        }
      },
      modificar_cantidad(id, cantidad) {
          const url = "/modificar_cantidad";
          axios.post(url, {
              'id': id,
              'cantidad': cantidad
          }).then((response) => {
              this.mostrar_lineas();
              this.mostrar_totales();
              // No necesitas mostrar_lineas() si usas v-model
          }).catch(error => {
              console.error("Error al modificar cantidad:", error);
          });
      },
      mostrar_collapse(){
        $('#pago').collapse('show');
      },
      mostrar_lineas(){
        var cotizacion = this.$refs.id_cotizacion.innerHTML;
        axios.get('/mostrar_detalles/'+cotizacion).then((response)=> {
            this.articulos = response.data;
        })
      },
      mostrar_totales(){
        var cotizacion = this.$refs.id_cotizacion.innerHTML;
        axios.get('/totales/'+ cotizacion).then((response)=>{
            this.totales = response.data.data.cotizacion;
            this.saldo = response.data.data.totales.saldo;
            this.pagado = response.data.data.cotizacion.pago;
            this.entregada = response.data.data.cotizacion.entregada;
        })
      },
      borrar_linea(data){
        if (window.confirm("¿Realmente quieres borrar esta linea?")) {
            axios.get('/borrar_linea/'+data).then((response)=> {
              this.mostrar_lineas();
              this.mostrar_totales();
            })
        }
      },
      isValidCurrency(value) {
          // Elimina espacios en blanco al inicio/final
          const trimmedValue = String(value).trim();
          // Regex para moneda (ej: $1,000.00 | 100,50 | €500)
          const currencyPattern = /^\d+(\.\d{1,2})?$/;
          return currencyPattern.test(trimmedValue);
      },
      agregar_pago(){
        var cotizacion = this.$refs.id_cotizacion.innerHTML;
        
        axios.post('/pago', {
          'pago': this.anticipo,
          'id': cotizacion,
          'id_banco': this.bancoSeleccionado // Agregamos el ID del banco
        }).then((response) => {
          if (response.data.flag == 1) {
            $('#modalPago').modal('hide');
            this.mostrar_lineas();
            this.mostrar_totales();
            location.reload(); // Mover el reload aquí para que solo se ejecute si la petición fue exitosa
          }
        }).catch((error) => {
          console.error('Error al agregar pago:', error);
          this.$refs.errorFeedback.textContent = "Error al procesar el pago";
          this.$refs.errorFeedback.classList.remove('d-none');
        });
      },
      descontar_inventario() {
        if (!confirm("¿Estás seguro de que quieres descontar del inventario?")) {
          return;
        }

        var cotizacion = this.$refs.id_cotizacion.innerHTML;

        axios.post('/descontar_inventario', {
          'id': cotizacion
        }).then((response) => {
          if (response.data.status === 'ok') {
            alert('Inventario descontado correctamente.');
            location.reload();
          } else {
            alert('Error: ' + response.data.message);
          }
        }).catch((error) => {
          console.error('Error en la petición:', error);
          alert('Hubo un error al intentar descontar el inventario.');
        });
      },
      marcar_pagado() {
        var cotizacion = this.$refs.id_cotizacion.innerHTML;
        var banco = this.bancoSeleccionado;
        if (confirm('¿Deseas marcar como pagado esta cotización?') === true) {
          axios.post('/marcar_pagado', {
            'id': cotizacion,
            'banco':banco
          })
          .then((response) => {
            if (response.data.flag === 1) {
              notify('Pago efectuado con éxito');
              location.reload();
            } else {
              notify(response.data.message || 'Ocurrió un error al registrar el pago ❌');
            }
          })
          .catch((error) => {
            notify('Error en el servidor: ' + (error.response?.data?.message || error.message));
          });
        }
      },
      aplicar_descuento(){
        axios.post('/descuento',{
          'id_cotizacion':this.$refs.id_cotizacion.innerHTML,
          'descuento':this.descuento
        }).then((response)=>{
          if (response.data.flag == 1) {
            this.mostrar_lineas();
            this.mostrar_totales();
          }
        })  
      },
      aplicar_descuento_dinero() {
          axios.post('/descuento_dinero', {
              'id_cotizacion': this.$refs.id_cotizacion.innerHTML,
              'descuento': this.dinero_descuento
          }).then((response) => {
              if (response.data.flag == 1) {
                  this.mostrar_lineas();
                  this.mostrar_totales();
                  notify(response.data.message);
              } else {
                  notify(response.data.message);
              }
          }).catch(error => {
              this.$emit('notify', {
                  type: 'error',
                  message: 'Error de conexión con el servidor'
              });
              console.error(error);
          });
      },
      descargar_img(){
        var cotizacion = this.$refs.id_cotizacion.innerHTML;
        html2canvas(document.querySelector("#ticket")).then(canvas => {
        canvas.toBlob(function(blob) {
          window.saveAs(blob, 'TK-'+cotizacion+'.jpg');
        });
        });
      },
      generar_factura(){

        if (confirm('¿Deseas generar esta factura')== true) { 
          axios.post('/facturar_cotizacion',{
            'id_cotizacion':this.$refs.id_cotizacion.innerHTML
          }).then((response)=>{
            if (response.data.factura == 1) {
              alert("Esta cotizacion ya se facturo");
            }
            window.location.href = '/cotizaciones';
          })
        }
      },
      entregadas(data){
        var title = "Hecho";
        var message = "Se ha realizado la accion";
        toaster(title,message);
        /*var me = this;
        var url = "/marcar_entregado"
        axios.post(url,{
          'id':data    
        }).then(function (response){
        });*/
      },
      tabla(){
        $( document ).ready(function() {
          new DataTable('#articulos');
        });
      },
    },
    mounted(){
      this.mostrar_lineas();
      this.mostrar_totales();
      this.mostrar_articulos();
    }
}).mount('#app')
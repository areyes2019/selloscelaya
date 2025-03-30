const { createApp, ref } = Vue

  createApp({
    data() {
      return {
        articulos:[],
        lista:[],
        independiente:[],
        cantidad:"1",
        anticipo:"",
        sub_total:"",
        iva:"",
        total:"",
        saldo:"",
        display:"",
        hide:"",
        pago:"",
        saldo:"",
        sugerido:"",
        utilidad:"",
        articulo_ind:"",
        cantidad_ind:"",
        precio_ind:"",
        descuento:"",
        disabled:0,
        totales:""
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
      modificar_cantidad(data){
        var me = this;
        var url = "/modificar_cantidad";
        var cantidad = this.$refs[data][0].value;
        var descuento = this.$refs.descuento.innerHTML;
        axios.post(url,{
          'id':data,
          'cantidad':cantidad,
          'descuento':descuento
        }).then(function (response){
          me.mostrar_lineas();
          $('#pago').collapse('hide');
        })

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
           this.totales = response.data;
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
        //comprobamos si lo que viene en el input tiene el formato correcto
        if (!this.isValidCurrency(this.anticipo)) {
            this.$refs.errorFeedback.textContent = "Formato inválido. Use: 1000.00";
            this.$refs.errorFeedback.classList.remove('d-none');
            return;
        }
        var cotizacion = this.$refs.id_cotizacion.innerHTML;
        axios.post('/pago',{
          'pago':this.anticipo,
          'id':cotizacion
        }).then((response)=>{
          if (response.data.flag == 1) {
            $('#modalPago').modal('hide');
            this.mostrar_lineas();
            this.anticipo = "";
          }
        })
        $("#pagoModal").modal('hide');

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
      descargar_img(){
        var cotizacion = this.$refs.id_cotizacion.innerHTML;
        html2canvas(document.querySelector("#ticket")).then(canvas => {
        canvas.toBlob(function(blob) {
          window.saveAs(blob, 'TK-'+cotizacion+'.jpg');
        });
        });
      },
      entregada(data){
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
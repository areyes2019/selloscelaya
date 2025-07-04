const {createApp, ref} = Vue

  createApp({
    data(){
      return{
        nombre: "",
        telefono: "",
        pedido: [],
        articulos: [],
        anticipo: 0,
        descuento: 0,          // Nuevo: porcentaje de descuento
        montoDescuento: 0,     // Nuevo: cantidad descontada
        totalConDescuento: 0,   // Nuevo: total con descuento aplicado
        articulo: "",
        precio_unitario: "",
        articuloId: "",
        articuloSeleccionado: null,
        cantidad: 1,
        itemsPedido: [],
        searchQuery: '',
        showResults: false,
        selectedIndex: -1,
        bancos: [],
        bancoSeleccionado: null
      }
    },
    computed: {
      total() {
        return this.itemsPedido.reduce((sum, item) => sum + parseFloat(item.subtotal), 0);
      },
      subtotal() {  // Igual al total sin descuento
        return this.total;
      },
      saldo() {
        const saldoCalculado = this.totalConDescuento - parseFloat(this.anticipo);
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
      mostrar_balance(){
        if (this.periodo == 1) {
          //hacemos la consulta del mes
          axios.get('/reportes/este_mes').then((response)=>{
            this.resumen = response.data;
          })
        }
      }
    },
    mounted(){
      this.mostrar_balance();
    }
}).mount('#app')
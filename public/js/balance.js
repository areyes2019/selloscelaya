const {createApp, ref} = Vue

	createApp({
		data(){
			return{
				pedido:[],
				articulos:[],
				periodo:1,  // 1 es este mes
				resumen:[],
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
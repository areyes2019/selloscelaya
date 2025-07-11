const {createApp, ref} = Vue

	createApp({
		data(){
			return{
				pedido:[],
				articulos:[],
				periodo:1,  // 1 es este mes
				resumen:[],
				pedido:""
			}
		},
		methods:{
			abrir_modal_pago(data){
				this.pedido = data
			}
		},
		mounted(){
		}
}).mount('#app')
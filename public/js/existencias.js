const {createApp, ref} = Vue

	createApp({
		data(){
			return{
				mes:[],
				inventario:[],
			}
		},
		methods:{
			este_mes(){
				axios.get('este_mes').then((response)=>{
					this.mes = response.data;
				})		
			},
			cambiar_inventario(data){
				axios.get('/existencias/edicion_rapida/'+data).then((response)=>{
					this.inventario = response.data.data;
					console.log(this.inventario);
				})
			},
			async guardar_rapido() {

			      	var id_entrada = this.inventario.id_entrada;
			      	var cantidad = this.inventario.cantidad;
					axios.post('/existencias/guardar_rapido',{
						'id_entrada':id_entrada, 
						'cantidad':cantidad, 
					}).then((response)=>{
						if (response.data.flag == 1) {
							location.reload();
						}
					})			  
			}
		},
		mounted(){
			//this.este_mes();
		}
}).mount('#app')
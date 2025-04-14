const {createApp, ref} = Vue

	createApp({
		data(){
			return{
				mes:[],
				articulos:[],
			}
		},
		methods:{
			este_mes(){
				axios.get('este_mes').then((response)=>{
					this.mes = response.data;
				})		
			}
		},
		mounted(){
			this.este_mes();
		}
}).mount('#app')
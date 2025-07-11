const {createApp,ref} = Vue
	createApp({
		data(){
			return{
				familias:[],
				nombre:"",
				descuento:"",
				proveedor:""
			}
		},
		methods:{
			ver_familias(data){
				$('#familias').modal('show');
				var me = this;
				var url = "/mostrar_familias/"+data;
				axios.get(url).then(function (response){
					me.familias = response.data;
					me.proveedor = data;
				})
			},
			agregar_familia(){
				var me = this;
				var url = "agregar_familia";
				var id = this.$refs.proveedor.innerHTML;
				axios.post(url,{
					'nombre': me.nombre,
					'descuento':me.descuento,
					'id':id
				}).then(function (response){
					me.ver_familias(id);
				})
			}

		},
		mounted(){

		}
	}).mount('#app')
const { createApp, ref} = Vue;

createApp({
    setup() {
        // Obtener los datos del atributo data-
        const appElement = document.getElementById('app');
        const gastosData = JSON.parse(appElement.dataset.gastos);
        
        const gastos = ref(gastosData);
        const formData = ref({
            id_registro: '',
            descripcion: '',
            monto: '',
            fecha_gasto: ''
        });
        const modalTitle = ref('Nuevo Gasto');
        const accionBoton = ref('Guardar');
        const gastoModal = ref(null);
        const modalInstance = ref(null);

        // Inicializar modal cuando el componente está montado
        onMounted(() => {
            gastoModal.value = document.getElementById('gastoModal');
            modalInstance.value = new bootstrap.Modal(gastoModal.value);
            
            // Resetear formulario cuando se cierra el modal
            gastoModal.value.addEventListener('hidden.bs.modal', () => {
                resetForm();
            });
        });

        const formatMoney = (value) => {
            return parseFloat(value).toFixed(2);
        };

        const formatDate = (dateString) => {
            const date = new Date(dateString);
            return date.toLocaleDateString('es-MX');
        };

        const abrirModalNuevo = () => {
            resetForm();
            modalTitle.value = 'Nuevo Gasto';
            accionBoton.value = 'Guardar';
            modalInstance.value.show();
        };
        
        const editarGasto = (id) => {
            axios.get(`<?= site_url('gastos/obtener/') ?>${id}`)
                .then(response => {
                    formData.value = {
                        ...response.data,
                        fecha_gasto: response.data.fecha_gasto.split('/').reverse().join('-')
                    };
                    modalTitle.value = 'Editar Gasto';
                    accionBoton.value = 'Actualizar';
                    modalInstance.value.show();
                })
                .catch(error => {
                    console.error('Error al obtener gasto:', error);
                    alert('Error al cargar el gasto');
                });
        };

        const verGasto = (id) => {
            // Implementar vista detallada si es necesario
            window.location.href = `<?= site_url('gastos/') ?>${id}`;
        };

        const eliminarGasto = (id) => {
            if (confirm('¿Estás seguro de eliminar este gasto?')) {
                axios.get(`<?= site_url('gastos/eliminar/') ?>${id}`)
                    .then(() => {
                        gastos.value = gastos.value.filter(g => g.id_registro !== id);
                        alert('Gasto eliminado correctamente');
                    })
                    .catch(error => {
                        console.error('Error al eliminar:', error);
                        alert('Error al eliminar el gasto');
                    });
            }
        };

        const guardarGasto = () => {
            const url = formData.value.id_registro 
                ? `<?= site_url('gastos/actualizar/') ?>${formData.value.id_registro}`
                : '<?= site_url('gastos/guardar') ?>';

            const data = {
                ...formData.value,
                fecha_gasto: formData.value.fecha_gasto.split('-').reverse().join('/')
            };

            axios.post(url, data)
                .then(response => {
                    if (formData.value.id_registro) {
                        // Actualizar lista después de editar
                        const index = gastos.value.findIndex(g => g.id_registro === formData.value.id_registro);
                        gastos.value[index] = response.data;
                    } else {
                        // Agregar nuevo gasto a la lista
                        gastos.value.unshift(response.data);
                    }
                    modalInstance.value.hide();
                    alert('Gasto guardado correctamente');
                })
                .catch(error => {
                    console.error('Error al guardar:', error);
                    alert('Error al guardar el gasto');
                });
        };

        const resetForm = () => {
            formData.value = {
                id_registro: '',
                descripcion: '',
                monto: '',
                fecha_gasto: ''
            };
            modalTitle.value = 'Nuevo Gasto';
            accionBoton.value = 'Guardar';
        };

        return {
            gastos,
            formData,
            modalTitle,
            accionBoton,
            abrirModalNuevo,
            editarGasto,
            verGasto,
            eliminarGasto,
            guardarGasto,
            formatMoney,
            formatDate
        };
    }
}).mount('#app');
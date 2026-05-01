const { createApp, ref, computed, onMounted } = Vue

// ── Mapa de badges ───────────────────────────────────────────────
const BADGE_COMERCIAL = {
  borrador:  { label: 'Borrador',  cls: 'bg-secondary' },
  enviada:   { label: 'Enviada',   cls: 'bg-primary'   },
  anticipo:  { label: 'Anticipo',  cls: 'bg-info text-dark' },
  pagado:    { label: 'Pagado',    cls: 'bg-success'   },
  facturada: { label: 'Facturada', cls: 'bg-warning text-dark' },
}

createApp({
  setup() {
    // ── Estado ─────────────────────────────────────────────────
    const cotizaciones = ref([])
    const cargando     = ref(true)
    const error        = ref(null)

    const busqueda     = ref('')
    const filtroEstado = ref('todos')
    const filtroFecha  = ref('todos')

    // ── Clonar ─────────────────────────────────────────────────
    const cotizacionAClonar = ref(null)
    const clienteClonId     = ref('')
    const clonando          = ref(false)

    // ── Carga de datos ──────────────────────────────────────────
    async function cargar() {
      cargando.value = true
      error.value    = null
      try {
        const res = await axios.get('/api/cotizaciones')
        cotizaciones.value = res.data.data
      } catch (e) {
        error.value = 'No se pudieron cargar las cotizaciones.'
        console.error(e)
      } finally {
        cargando.value = false
      }
    }

    // ── Eliminar ────────────────────────────────────────────────
    async function eliminar(id) {
      if (!confirm('Esta eliminación no se puede revertir. ¿Deseas continuar?')) return
      try {
        await axios.post(`/eliminar_cotizacion/${id}`)
        cotizaciones.value = cotizaciones.value.filter(c => c.id_cotizacion != id)
      } catch (e) {
        alert('Error al eliminar la cotización.')
      }
    }

    // ── Clonar ──────────────────────────────────────────────────
    function abrirModalClonar(c) {
      cotizacionAClonar.value = c
      clienteClonId.value     = ''
      new bootstrap.Modal(document.getElementById('modalClonar')).show()
    }

    async function confirmarClonar() {
      if (!clienteClonId.value) return
      clonando.value = true
      try {
        const res = await axios.post('/clonar_cotizacion', {
          id_cotizacion: cotizacionAClonar.value.id_cotizacion,
          id_cliente:    clienteClonId.value,
        })
        if (res.data.status === 'success') {
          bootstrap.Modal.getInstance(document.getElementById('modalClonar'))?.hide()
          window.location.href = `/pagina_cotizador/${res.data.slug}`
        } else {
          alert(res.data.message || 'Error al clonar la cotización')
        }
      } catch (e) {
        alert('Error al clonar la cotización')
      } finally {
        clonando.value = false
      }
    }

    // ── Rango de fechas según filtroFecha ───────────────────────
    const rangoFecha = computed(() => {
      if (filtroFecha.value === 'todos') return null

      const hoy = new Date()
      hoy.setHours(0, 0, 0, 0)
      const fin = new Date()
      fin.setHours(23, 59, 59, 999)

      if (filtroFecha.value === 'hoy') {
        return { inicio: new Date(hoy), fin }
      }
      if (filtroFecha.value === 'semana') {
        const inicio = new Date(hoy)
        const dia = hoy.getDay()
        inicio.setDate(hoy.getDate() - (dia === 0 ? 6 : dia - 1))
        return { inicio, fin }
      }
      // mes
      return { inicio: new Date(hoy.getFullYear(), hoy.getMonth(), 1), fin }
    })

    const LABELS_FECHA = { todos: 'Todas', hoy: 'Hoy', semana: 'Esta semana', mes: 'Este mes' }
    const etiquetaFecha = computed(() => LABELS_FECHA[filtroFecha.value] ?? '')

    // ── Filtro base: texto + rango de fecha ─────────────────────
    const baseFiltrada = computed(() => {
      const rango = rangoFecha.value
      const q = busqueda.value.trim().toLowerCase()

      return cotizaciones.value.filter(c => {
        // Filtro por fecha (solo si no es 'todos')
        if (rango) {
          const fechaDoc = c.created_at ? new Date(c.created_at.replace(' ', 'T')) : null
          if (!fechaDoc || fechaDoc < rango.inicio || fechaDoc > rango.fin) return false
        }
        // Filtro por texto de búsqueda
        if (!q) return true
        return (
          (c.nombre  || '').toLowerCase().includes(q) ||
          (c.correo  || '').toLowerCase().includes(q) ||
          String(c.id_cotizacion).includes(q)
        )
      })
    })

    // ── Lista del tab Cotizaciones ──────────────────────────────
    const listaComercial = computed(() => {
      const base = baseFiltrada.value
      if (filtroEstado.value === 'todos') return base
      return base.filter(c => (c.estado_comercial || 'borrador') === filtroEstado.value)
    })

    // ── Helpers de presentación ─────────────────────────────────
    function badgeComercial(val) {
      return BADGE_COMERCIAL[val || 'borrador'] ?? { label: val, cls: 'bg-secondary' }
    }
    function moneda(val) {
      return parseFloat(val || 0).toLocaleString('es-MX', {
        style:    'currency',
        currency: 'MXN',
      })
    }
    function fecha(val) {
      if (!val) return '—'
      return new Date(val).toLocaleDateString('es-MX', { day: '2-digit', month: 'short', year: 'numeric' })
    }

    onMounted(cargar)

    return {
      // estado
      cotizaciones, cargando, error,
      // filtros
      busqueda, filtroEstado, filtroFecha, etiquetaFecha,
      // listas
      listaComercial,
      // helpers
      badgeComercial, moneda, fecha,
      // acciones
      eliminar, cargar,
      // clonar
      cotizacionAClonar, clienteClonId, clonando, abrirModalClonar, confirmarClonar,
    }
  },
}).mount('#app-cotizaciones')

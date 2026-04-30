const { createApp, ref, computed, onMounted } = Vue

// ── Mapas de badges ──────────────────────────────────────────────
const BADGE_ESTATUS = {
  0: { label: 'Pendiente',  cls: 'bg-secondary' },
  1: { label: 'Enviada',    cls: 'bg-primary'   },
  2: { label: 'Pagada',     cls: 'bg-success'   },
  3: { label: 'Entregada',  cls: 'bg-warning text-dark' },
}

const BADGE_FINANCIERO = {
  pendiente: { label: 'Pendiente', cls: 'bg-secondary'      },
  anticipo:  { label: 'Anticipo',  cls: 'bg-info text-dark' },
  parcial:   { label: 'Parcial',   cls: 'bg-warning text-dark' },
  pagado:    { label: 'Pagado',    cls: 'bg-success'        },
}

const BADGE_FISCAL = {
  sin_facturar: { label: 'Sin facturar', cls: 'bg-secondary' },
  facturada:    { label: 'Facturada',    cls: 'bg-success'   },
  cancelada:    { label: 'Cancelada',    cls: 'bg-danger'    },
}

// ── Definición de tabs ───────────────────────────────────────────
const TABS = [
  { id: 'cotizaciones', label: 'Cotizaciones', icon: 'bi-file-earmark-text' },
  { id: 'pagos',        label: 'Pagos',        icon: 'bi-cash-coin'         },
  { id: 'facturacion',  label: 'Facturación',  icon: 'bi-receipt'           },
  { id: 'acciones',     label: 'Acciones',     icon: 'bi-lightning-charge'  },
]

createApp({
  setup() {
    // ── Estado ─────────────────────────────────────────────────
    const cotizaciones = ref([])
    const cargando     = ref(true)
    const error        = ref(null)

    const tabActiva          = ref('cotizaciones')
    const busqueda           = ref('')
    const filtroEstatus      = ref('todos')
    const filtroFinanciero   = ref('todos')
    const filtroFiscal       = ref('todos')
    const filtroFecha        = ref('mes')

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

    // ── Rango de fechas según filtroFecha ───────────────────────
    const rangoFecha = computed(() => {
      const hoy = new Date()
      hoy.setHours(0, 0, 0, 0)
      const fin = new Date()
      fin.setHours(23, 59, 59, 999)

      if (filtroFecha.value === 'hoy') {
        return { inicio: new Date(hoy), fin }
      }
      if (filtroFecha.value === 'semana') {
        const inicio = new Date(hoy)
        const dia = hoy.getDay() // 0=Dom … 6=Sáb
        inicio.setDate(hoy.getDate() - (dia === 0 ? 6 : dia - 1))
        return { inicio, fin }
      }
      // mes (default)
      return { inicio: new Date(hoy.getFullYear(), hoy.getMonth(), 1), fin }
    })

    const LABELS_FECHA = { hoy: 'Hoy', semana: 'Esta semana', mes: 'Este mes' }
    const etiquetaFecha = computed(() => LABELS_FECHA[filtroFecha.value] ?? '')

    // ── Filtro base: texto + rango de fecha ─────────────────────
    const baseFiltrada = computed(() => {
      const { inicio, fin } = rangoFecha.value
      const q = busqueda.value.trim().toLowerCase()

      return cotizaciones.value.filter(c => {
        const fechaDoc = c.created_at ? new Date(c.created_at.replace(' ', 'T')) : null
        if (!fechaDoc || fechaDoc < inicio || fechaDoc > fin) return false
        if (!q) return true
        return (
          (c.nombre  || '').toLowerCase().includes(q) ||
          (c.correo  || '').toLowerCase().includes(q) ||
          String(c.id_cotizacion).includes(q)
        )
      })
    })

    // ── Listas por tab ──────────────────────────────────────────
    const listaComercial = computed(() => {
      const base = baseFiltrada.value
      if (filtroEstatus.value === 'todos') return base
      return base.filter(c => String(c.estatus ?? 0) === filtroEstatus.value)
    })

    const listaFinanciero = computed(() => {
      const base = baseFiltrada.value
      if (filtroFinanciero.value === 'todos') return base
      return base.filter(c => (c.estado_financiero || 'pendiente') === filtroFinanciero.value)
    })

    const listaFiscal = computed(() => {
      const base = baseFiltrada.value
      if (filtroFiscal.value === 'todos') return base
      return base.filter(c => (c.estado_fiscal || 'sin_facturar') === filtroFiscal.value)
    })

    // ── Listas de Acciones (combinaciones críticas) ─────────────
    const aceptadasSinPago = computed(() =>
      baseFiltrada.value.filter(c =>
        Number(c.estatus) >= 1 &&
        (c.estado_financiero || 'pendiente') === 'pendiente'
      )
    )

    const pagadasSinFacturar = computed(() =>
      baseFiltrada.value.filter(c =>
        (c.estado_financiero || 'pendiente') === 'pagado' &&
        (c.estado_fiscal     || 'sin_facturar') === 'sin_facturar'
      )
    )

    const facturadaSinPago = computed(() =>
      baseFiltrada.value.filter(c =>
        (c.estado_fiscal     || 'sin_facturar') === 'facturada' &&
        (c.estado_financiero || 'pendiente')    !== 'pagado'
      )
    )

    // ── Helpers de presentación ─────────────────────────────────
    function badgeEstatus(val) {
      return BADGE_ESTATUS[val ?? 0] ?? { label: val, cls: 'bg-secondary' }
    }
    function badgeFinanciero(val) {
      return BADGE_FINANCIERO[val || 'pendiente'] ?? { label: val, cls: 'bg-secondary' }
    }
    function badgeFiscal(val) {
      return BADGE_FISCAL[val || 'sin_facturar'] ?? { label: val, cls: 'bg-secondary' }
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
      // navegación
      TABS, tabActiva,
      // filtros
      busqueda, filtroEstatus, filtroFinanciero, filtroFiscal,
      filtroFecha, etiquetaFecha,
      // listas computadas
      listaComercial, listaFinanciero, listaFiscal,
      aceptadasSinPago, pagadasSinFacturar, facturadaSinPago,
      // helpers
      badgeEstatus, badgeFinanciero, badgeFiscal, moneda, fecha,
      // acciones
      eliminar, cargar,
    }
  },
}).mount('#app-cotizaciones')

<?php echo $this->extend('Panel/panel_template') ?>
<?php echo $this->section('contenido') ?>

<div class="midde_cont">
    <div class="container-fluid">

        <div class="row column_title">
            <div class="col-md-12">
                <div class="page_title">
                    <h2>Importación de Clientes</h2>
                </div>
            </div>
        </div>

        <div class="row" id="importApp">
            <div class="col-md-8 offset-md-2">
                <div class="white_shd full margin_bottom_30">
                    <div class="full graph_head">
                        <div class="heading1 margin_0">
                            <h2>Importar desde CSV (Facturama)</h2>
                        </div>
                    </div>
                    <div class="full padding_infor_info">

                        <!-- AVISO: solo una vez -->
                        <div class="alert alert-warning border-warning mb-4" role="alert">
                            <strong>Aviso:</strong> Esta importación está diseñada para ejecutarse
                            <strong>una sola vez</strong>. Los datos fiscales existentes
                            <strong>no serán sobrescritos</strong>; solo se completarán los campos vacíos.
                        </div>

                        <!-- FORMULARIO -->
                        <div v-if="!resultado">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Archivo CSV de Facturama</label>
                                <input
                                    type="file"
                                    class="form-control"
                                    accept=".csv,text/csv"
                                    @change="onFileChange"
                                    :disabled="cargando"
                                />
                                <div v-if="errorArchivo" class="text-danger small mt-1">{{ errorArchivo }}</div>
                                <small class="text-muted">Máximo 10 MB. Columnas esperadas: RazonSocial, RFC, Email, Calle…</small>
                            </div>

                            <button
                                class="btn btn-danger rounded-0 px-4"
                                :disabled="!archivo || cargando"
                                @click="importar"
                            >
                                <span v-if="cargando">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                    Procesando…
                                </span>
                                <span v-else>Iniciar importación</span>
                            </button>
                        </div>

                        <!-- RESUMEN DE RESULTADOS -->
                        <div v-if="resultado">

                            <h5 class="mt-2 mb-3">Resultado</h5>

                            <div class="row g-3 mb-4">
                                <div class="col-6 col-md-4">
                                    <div class="card text-center border-secondary">
                                        <div class="card-body py-2">
                                            <div class="fs-4 fw-bold">{{ resultado.stats.total }}</div>
                                            <div class="small text-muted">Total procesados</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="card text-center border-success">
                                        <div class="card-body py-2">
                                            <div class="fs-4 fw-bold text-success">{{ resultado.stats.creados }}</div>
                                            <div class="small text-muted">Creados</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="card text-center border-primary">
                                        <div class="card-body py-2">
                                            <div class="fs-4 fw-bold text-primary">{{ resultado.stats.actualizados }}</div>
                                            <div class="small text-muted">Actualizados</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="card text-center border-secondary">
                                        <div class="card-body py-2">
                                            <div class="fs-4 fw-bold text-secondary">{{ resultado.stats.omitidos }}</div>
                                            <div class="small text-muted">Omitidos</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="card text-center border-warning">
                                        <div class="card-body py-2">
                                            <div class="fs-4 fw-bold text-warning">{{ resultado.stats.conflictos }}</div>
                                            <div class="small text-muted">Conflictos</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="card text-center border-danger">
                                        <div class="card-body py-2">
                                            <div class="fs-4 fw-bold text-danger">{{ resultado.stats.errores }}</div>
                                            <div class="small text-muted">Errores</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- FILTROS DEL LOG -->
                            <div class="d-flex gap-2 flex-wrap mb-3">
                                <button
                                    v-for="a in acciones"
                                    :key="a.key"
                                    class="btn btn-sm"
                                    :class="filtroActivo === a.key ? a.btnActive : a.btn"
                                    @click="filtroActivo = filtroActivo === a.key ? '' : a.key"
                                >
                                    {{ a.label }} ({{ contarPorAccion(a.key) }})
                                </button>
                            </div>

                            <!-- TABLA LOG -->
                            <div v-if="logFiltrado.length" class="table-responsive">
                                <table class="table table-sm table-bordered table-hover small">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="width:60px">Fila</th>
                                            <th>Nombre / RFC</th>
                                            <th style="width:110px">Acción</th>
                                            <th>Motivo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="entry in logFiltrado"
                                            :key="entry.fila + entry.nombre"
                                            :class="rowClass(entry.accion)"
                                        >
                                            <td class="text-center">{{ entry.fila }}</td>
                                            <td>{{ entry.nombre }}</td>
                                            <td>
                                                <span class="badge" :class="badgeClass(entry.accion)">
                                                    {{ entry.accion }}
                                                </span>
                                            </td>
                                            <td>{{ entry.motivo }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p v-else class="text-muted small">No hay entradas para el filtro seleccionado.</p>

                            <button class="btn btn-outline-secondary btn-sm mt-3" @click="reiniciar">
                                Nueva importación
                            </button>
                        </div>

                        <!-- ERROR GLOBAL -->
                        <div v-if="errorGlobal" class="alert alert-danger mt-3">{{ errorGlobal }}</div>

                    </div><!-- /padding_infor_info -->
                </div><!-- /white_shd -->
            </div>
        </div><!-- /row -->

    </div>
</div>

<script>
const { createApp, ref, computed } = Vue;

createApp({
    setup() {
        const archivo      = ref(null);
        const errorArchivo = ref('');
        const cargando     = ref(false);
        const resultado    = ref(null);
        const errorGlobal  = ref('');
        const filtroActivo = ref('');

        const acciones = [
            { key: 'creado',      label: 'Creados',      btn: 'btn-outline-success',  btnActive: 'btn-success'  },
            { key: 'actualizado', label: 'Actualizados', btn: 'btn-outline-primary',  btnActive: 'btn-primary'  },
            { key: 'omitido',     label: 'Omitidos',     btn: 'btn-outline-secondary',btnActive: 'btn-secondary'},
            { key: 'conflicto',   label: 'Conflictos',   btn: 'btn-outline-warning',  btnActive: 'btn-warning'  },
            { key: 'error',       label: 'Errores',      btn: 'btn-outline-danger',   btnActive: 'btn-danger'   },
        ];

        function onFileChange(e) {
            errorArchivo.value = '';
            const f = e.target.files[0];
            if (!f) { archivo.value = null; return; }
            if (!f.name.toLowerCase().endsWith('.csv')) {
                errorArchivo.value = 'Solo se aceptan archivos .csv';
                archivo.value = null;
                return;
            }
            if (f.size > 10 * 1024 * 1024) {
                errorArchivo.value = 'El archivo supera los 10 MB';
                archivo.value = null;
                return;
            }
            archivo.value = f;
        }

        async function importar() {
            if (!archivo.value) return;
            cargando.value    = true;
            errorGlobal.value = '';

            const form = new FormData();
            form.append('archivo_csv', archivo.value);
            form.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            try {
                const res  = await fetch('<?= base_url('clientes/importar_csv') ?>', {
                    method: 'POST',
                    body:   form,
                });
                const json = await res.json();

                if (!json.ok) {
                    errorGlobal.value = json.message || 'Error desconocido';
                } else {
                    resultado.value = json;
                }
            } catch (err) {
                errorGlobal.value = 'Error de red: ' + err.message;
            } finally {
                cargando.value = false;
            }
        }

        const logFiltrado = computed(() => {
            if (!resultado.value) return [];
            const log = resultado.value.log;
            return filtroActivo.value
                ? log.filter(e => e.accion === filtroActivo.value)
                : log;
        });

        function contarPorAccion(key) {
            if (!resultado.value) return 0;
            return resultado.value.log.filter(e => e.accion === key).length;
        }

        function badgeClass(accion) {
            return {
                'bg-success':   accion === 'creado',
                'bg-primary':   accion === 'actualizado',
                'bg-secondary': accion === 'omitido',
                'bg-warning text-dark': accion === 'conflicto',
                'bg-danger':    accion === 'error',
            };
        }

        function rowClass(accion) {
            return {
                'table-warning': accion === 'conflicto',
                'table-danger':  accion === 'error',
            };
        }

        function reiniciar() {
            archivo.value      = null;
            resultado.value    = null;
            errorGlobal.value  = '';
            filtroActivo.value = '';
        }

        return {
            archivo, errorArchivo, cargando, resultado,
            errorGlobal, filtroActivo, acciones,
            onFileChange, importar, logFiltrado,
            contarPorAccion, badgeClass, rowClass, reiniciar,
        };
    }
}).mount('#importApp');
</script>

<?php echo $this->endSection() ?>

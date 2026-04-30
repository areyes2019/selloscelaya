<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<?php $c = $clientes[0]; ?>

<div class="container mt-3" id="app-cliente">

    <div class="my-card d-flex align-items-center justify-content-between">
        <h2 class="mb-0"><?= esc($nombre) ?></h2>
        <!-- Botón Cargar CSF -->
        <label class="btn btn-outline-primary btn-sm mb-0" style="cursor:pointer;">
            <i class="bi bi-file-earmark-arrow-up me-1"></i> Cargar CSF
            <input type="file" id="csf-file-input" accept="application/pdf" style="display:none;">
        </label>
    </div>

    <!-- Alerta de datos cargados desde CSF -->
    <div id="csf-alert" class="alert alert-info alert-dismissible fade show d-none mt-2" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <strong>Datos extraídos del PDF.</strong> Revísalos y haz clic en <strong>Guardar</strong> para confirmar.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Spinner -->
    <div id="csf-spinner" class="text-center py-3 d-none">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-2 text-muted small">Procesando PDF…</p>
    </div>

    <div class="my-card mt-2">
        <!-- Pestañas -->
        <ul class="nav nav-tabs mb-3" id="clienteTabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab-general">
                    <i class="bi bi-person me-1"></i>General
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-fiscal">
                    <i class="bi bi-receipt me-1"></i>Datos Fiscales
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-direccion">
                    <i class="bi bi-geo-alt me-1"></i>Dirección
                </a>
            </li>
        </ul>

        <form class="row g-3" action="<?= base_url('actualizar_cliente') ?>" method="post" id="form-cliente">
            <?= csrf_field() ?>
            <input type="hidden" name="idcliente" value="<?= esc($c['id_cliente']) ?>">

            <div class="tab-content w-100">

                <!-- ══════════ TAB GENERAL ══════════ -->
                <div class="tab-pane fade show active" id="tab-general">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Nombre completo</label>
                            <input type="text" class="my-input form-control p-1"
                                   name="nombre" id="f-nombre"
                                   value="<?= esc($c['nombre']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" class="my-input form-control p-1"
                                   name="correo" id="f-correo"
                                   value="<?= esc($c['correo'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Teléfono / WhatsApp</label>
                            <input type="text" class="my-input form-control p-1"
                                   name="telefono" id="f-telefono"
                                   value="<?= esc($c['telefono'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tipo de cliente</label>
                            <select name="tipo" class="form-select my-input form-control p-1">
                                <option value="1" <?= ($c['tipo'] ?? 1) == 1 ? 'selected' : '' ?>>Cliente</option>
                                <option value="2" <?= ($c['tipo'] ?? 1) == 2 ? 'selected' : '' ?>>Distribuidor</option>
                            </select>
                        </div>

                    </div>
                </div>

                <!-- ══════════ TAB FISCAL ══════════ -->
                <div class="tab-pane fade" id="tab-fiscal">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">RFC <span class="text-danger">*</span></label>
                            <input type="text" class="my-input form-control p-1 text-uppercase"
                                   name="tax_id" id="f-tax-id"
                                   value="<?= esc($c['tax_id'] ?? '') ?>"
                                   maxlength="13" placeholder="GOGH8608172Q7">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Código Postal <span class="text-danger">*</span></label>
                            <input type="text" class="my-input form-control p-1"
                                   name="codigo_postal" id="f-cp"
                                   value="<?= esc($c['codigo_postal'] ?? '') ?>"
                                   maxlength="5" placeholder="38000">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Régimen Fiscal <span class="text-danger">*</span></label>
                            <select name="regimen_fiscal" id="f-regimen" class="form-select my-input form-control p-1">
                                <option value="">Seleccionar…</option>
                                <option value="601" <?= ($c['regimen_fiscal'] ?? '') == 601 ? 'selected' : '' ?>>601 – General de Ley PM</option>
                                <option value="603" <?= ($c['regimen_fiscal'] ?? '') == 603 ? 'selected' : '' ?>>603 – PM Fines no Lucrativos</option>
                                <option value="605" <?= ($c['regimen_fiscal'] ?? '') == 605 ? 'selected' : '' ?>>605 – Sueldos y Salarios</option>
                                <option value="606" <?= ($c['regimen_fiscal'] ?? '') == 606 ? 'selected' : '' ?>>606 – Arrendamiento</option>
                                <option value="612" <?= ($c['regimen_fiscal'] ?? '') == 612 ? 'selected' : '' ?>>612 – PF Actividades Empresariales</option>
                                <option value="616" <?= ($c['regimen_fiscal'] ?? '') == 616 ? 'selected' : '' ?>>616 – Sin obligaciones fiscales</option>
                                <option value="621" <?= ($c['regimen_fiscal'] ?? '') == 621 ? 'selected' : '' ?>>621 – Incorporación Fiscal</option>
                                <option value="622" <?= ($c['regimen_fiscal'] ?? '') == 622 ? 'selected' : '' ?>>622 – Agrícola / Ganadero</option>
                                <option value="625" <?= ($c['regimen_fiscal'] ?? '') == 625 ? 'selected' : '' ?>>625 – Plataformas Tecnológicas</option>
                                <option value="626" <?= ($c['regimen_fiscal'] ?? '') == 626 ? 'selected' : '' ?>>626 – Simplificado de Confianza (RESICO)</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <p class="text-muted small mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                Estos datos son requeridos para generar facturas (CFDI 4.0).
                                Usa el botón <strong>Cargar CSF</strong> para autocompletar desde el PDF del SAT.
                            </p>
                        </div>

                    </div>
                </div>

                <!-- ══════════ TAB DIRECCIÓN ══════════ -->
                <div class="tab-pane fade" id="tab-direccion">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Nombre de Vialidad (Calle)</label>
                            <input type="text" class="my-input form-control p-1"
                                   name="calle" id="f-calle"
                                   value="<?= esc($c['calle'] ?? '') ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Número Exterior</label>
                            <input type="text" class="my-input form-control p-1"
                                   name="no_ext" id="f-no-ext"
                                   value="<?= esc($c['no_ext'] ?? '') ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Número Interior</label>
                            <input type="text" class="my-input form-control p-1"
                                   name="no_int" id="f-no-int"
                                   value="<?= esc($c['no_int'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Colonia</label>
                            <input type="text" class="my-input form-control p-1"
                                   name="colonia" id="f-colonia"
                                   value="<?= esc($c['colonia'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Ciudad / Localidad</label>
                            <input type="text" class="my-input form-control p-1"
                                   name="ciudad" id="f-ciudad"
                                   value="<?= esc($c['ciudad'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Municipio</label>
                            <input type="text" class="my-input form-control p-1"
                                   name="municipio" id="f-municipio"
                                   value="<?= esc($c['municipio'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <input type="text" class="my-input form-control p-1"
                                   name="estado" id="f-estado"
                                   value="<?= esc($c['estado'] ?? '') ?>">
                        </div>

                    </div>
                </div>

            </div><!-- /tab-content -->

            <div class="col-12 mt-3">
                <button type="submit" class="my-btn-primary p-3">
                    <i class="bi bi-save me-1"></i> Guardar
                </button>
                <a href="<?= base_url('clientes') ?>" class="btn btn-secondary ms-2">Cancelar</a>
            </div>

        </form>
    </div>
</div>

<script>
(function () {
    const input    = document.getElementById('csf-file-input');
    const spinner  = document.getElementById('csf-spinner');
    const alertBox = document.getElementById('csf-alert');

    // Mapeo: campo del JSON → id del input en el form
    const MAP = {
        nombre_completo : 'f-nombre',
        rfc             : 'f-tax-id',
        codigo_postal   : 'f-cp',
        calle           : 'f-calle',
        no_ext          : 'f-no-ext',
        no_int          : 'f-no-int',
        colonia         : 'f-colonia',
        ciudad          : 'f-ciudad',
        municipio       : 'f-municipio',
        estado          : 'f-estado',
    };

    function fill(datos) {
        // Campos de texto
        for (const [key, id] of Object.entries(MAP)) {
            const el = document.getElementById(id);
            if (el && datos[key]) {
                el.value = datos[key];
                el.classList.add('border-primary');   // resalta cambio
            }
        }

        // Régimen fiscal (select)
        if (datos.regimen_codigo) {
            const sel = document.getElementById('f-regimen');
            if (sel) {
                sel.value = datos.regimen_codigo;
                sel.classList.add('border-primary');
            }
        }

        // Activar pestaña "Datos Fiscales" para que el usuario revise
        const tabFiscal = document.querySelector('[href="#tab-fiscal"]');
        if (tabFiscal) {
            bootstrap.Tab.getOrCreateInstance(tabFiscal).show();
        }

        alertBox.classList.remove('d-none');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    input.addEventListener('change', function () {
        if (!this.files.length) return;

        const file = this.files[0];
        if (file.type !== 'application/pdf') {
            alert('Por favor selecciona un archivo PDF.');
            return;
        }

        const csrfToken = window.csrfToken
            || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || '';

        const fd = new FormData();
        fd.append('csf_pdf', file);

        spinner.classList.remove('d-none');
        alertBox.classList.add('d-none');

        axios.post('<?= base_url('clientes/parsear_csf') ?>', fd, {
            headers: {
                'X-CSRF-TOKEN' : csrfToken,
                'Accept'       : 'application/json',
                // Content-Type lo pone Axios automáticamente como multipart
            }
        })
        .then(function (res) {
            if (res.data.ok) {
                fill(res.data.datos);
            } else {
                alert('Error al procesar el PDF: ' + (res.data.message || 'Error desconocido'));
            }
        })
        .catch(function (err) {
            const msg = err.response?.data?.message || err.message || 'Error de conexión';
            alert('Error: ' + msg);
        })
        .finally(function () {
            spinner.classList.add('d-none');
            input.value = '';
        });
    });
})();
</script>

<?php echo $this->endSection()?>

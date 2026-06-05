<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>

<div class="container mt-3">
    <div class="my-card d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Facturas Emitidas</h2>
    </div>

    <!-- Filtros rápidos por fecha -->
    <div class="my-card mt-2 d-flex gap-2 align-items-center flex-wrap">
        <span class="fw-bold me-1">Filtrar:</span>
        <button class="btn btn-sm btn-outline-primary btn-filtro-fecha active" data-rango="hoy">Hoy</button>
        <button class="btn btn-sm btn-outline-primary btn-filtro-fecha" data-rango="semana">Esta semana</button>
        <button class="btn btn-sm btn-outline-primary btn-filtro-fecha" data-rango="mes">Este mes</button>
        <button class="btn btn-sm btn-outline-secondary btn-filtro-fecha" data-rango="todas">Todas</button>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mt-2">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mt-2">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="my-card mt-2 responsive-table-container">
        <table class="advanced-responsive-table" id="tbl-facturas">
            <thead>
                <tr>
                    <th>Serie / Folio</th>
                    <th>Cliente</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th>Fecha timbrado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($facturas as $f): ?>
                <tr>
                    <td data-label="Serie / Folio">
                        <?= esc(($f['serie'] ?? '') . ($f['folio'] ? '-' . $f['folio'] : '')) ?>
                    </td>
                    <td data-label="Cliente"><?= esc($f['nombre_cliente']) ?></td>
                    <td data-label="Monto">
                        $<?= number_format((float)($f['monto'] ?? 0), 2) ?>
                    </td>
                    <td data-label="Estado">
                        <?php
                        $est = strtolower($f['estado'] ?? '');
                        if (in_array($est, ['valid', 'timbrada', 'vigente'])):
                        ?>
                            <span class="badge bg-success">Timbrada</span>
                        <?php elseif ($est === 'cancelada' || $est === 'canceled'): ?>
                            <span class="badge bg-danger">Cancelada</span>
                        <?php else: ?>
                            <span class="badge bg-secondary"><?= esc($f['estado']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Fecha timbrado" data-order="<?= esc($f['fecha_timbrado'] ?? '') ?>">
                        <?= $f['fecha_timbrado'] ? date('d/m/Y H:i', strtotime($f['fecha_timbrado'])) : '—' ?>
                    </td>
                    <td data-label="Acciones">
                        <!-- Vista previa -->
                        <button class="btn btn-sm btn-info btn-preview"
                                title="Vista previa"
                                data-factura="<?= htmlspecialchars(json_encode([
                                    'serie'        => $f['serie']          ?? '',
                                    'folio'        => $f['folio']          ?? '',
                                    'estado'       => $f['estado']         ?? '',
                                    'fecha'        => $f['fecha_timbrado'] ?? '',
                                    'monto'        => $f['monto']          ?? 0,
                                    'cliente'      => $f['nombre_cliente'] ?? '',
                                    'factura_uuid' => $f['factura_uuid']   ?? '',
                                    'respuesta'    => json_decode($f['respuesta_completa'] ?? '{}', true),
                                ]), ENT_QUOTES, 'UTF-8') ?>">
                            <i class="bi bi-eye"></i>
                        </button>
                        <a href="<?= base_url('facturas/pdf/' . $f['id']) ?>"
                           class="btn btn-sm btn-danger" title="Descargar PDF" target="_blank">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </a>
                        <a href="<?= base_url('facturas/descargar/' . $f['id'] . '/xml') ?>"
                           class="btn btn-sm btn-secondary" title="Descargar XML" target="_blank">
                            <i class="bi bi-file-earmark-code"></i>
                        </a>
                        <a href="<?= base_url('facturas/enviar/' . $f['id']) ?>"
                           class="btn btn-sm btn-primary" title="Enviar por correo"
                           onclick="return confirm('¿Enviar la factura por correo al cliente?')">
                            <i class="bi bi-envelope"></i>
                        </a>
                        <?php if (in_array(strtolower($f['estado'] ?? ''), ['valid', 'timbrada', 'vigente'])): ?>
                        <button class="btn btn-sm btn-warning btn-cancelar"
                                data-id="<?= $f['id'] ?>"
                                data-folio="<?= esc(($f['serie'] ?? '') . '-' . ($f['folio'] ?? '')) ?>"
                                title="Cancelar">
                            <i class="bi bi-x-circle"></i>
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal vista previa -->
<div class="modal fade" id="modalPreview" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    <span id="prev-titulo">Vista previa</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="prev-body">
                <!-- contenido inyectado por JS -->
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal cancelar -->
<div class="modal fade" id="modalCancelar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancelar Factura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Confirmas la cancelación de la factura <strong id="folio-cancelar"></strong>?</p>
                <div class="mb-3">
                    <label class="form-label">Motivo de cancelación</label>
                    <select id="motivo-cancelar" class="form-select">
                        <option value="01">01 – Comprobante emitido con errores con relación</option>
                        <option value="02">02 – Comprobante emitido con errores sin relación</option>
                        <option value="03">03 – No se llevó a cabo la operación</option>
                        <option value="04">04 – Operación nominativa relacionada en una factura global</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">No, volver</button>
                <button id="btn-confirmar-cancelar" class="btn btn-danger">Sí, cancelar</button>
            </div>
        </div>
    </div>
</div>

<script>
const fmt  = n  => new Intl.NumberFormat('es-MX', {minimumFractionDigits:2}).format(n ?? 0);
const fmtD = dt => dt ? new Date(dt).toLocaleDateString('es-MX', {day:'2-digit',month:'2-digit',year:'numeric',hour:'2-digit',minute:'2-digit'}) : '—';

function buildPreview(d) {
    const r     = d.respuesta ?? {};
    const cust  = r.customer ?? {};
    const stamp = r.stamp    ?? {};
    const items = r.items    ?? [];

    const estadoBadge = ['valid','timbrada','vigente'].includes((d.estado ?? '').toLowerCase())
        ? '<span class="badge bg-success">Timbrada</span>'
        : '<span class="badge bg-danger">Cancelada</span>';

    const filas = items.map(it => {
        const precio  = it.unit_price ?? 0;
        const cant    = it.quantity   ?? 1;
        const importe = precio * cant;
        return `<tr>
            <td>${it.quantity ?? 1}</td>
            <td>${it.description ?? ''}</td>
            <td>${it.product_key ?? ''}</td>
            <td class="text-end">$${fmt(precio)}</td>
            <td class="text-end">$${fmt(importe)}</td>
        </tr>`;
    }).join('');

    const subtotal = r.subtotal ?? (items.reduce((s,i) => s + (i.unit_price ?? 0) * (i.quantity ?? 1), 0));
    const total    = d.monto ?? r.total ?? 0;
    const iva      = total - subtotal;

    return `
    <div class="p-4">
        <!-- Encabezado -->
        <div class="row mb-3">
            <div class="col-md-6">
                <h6 class="fw-bold text-uppercase text-muted mb-1">Emisor</h6>
                <p class="mb-0 fw-semibold"><?= esc(getenv('app.name') ?: 'Tu Empresa') ?></p>
            </div>
            <div class="col-md-6 text-md-end">
                <h4 class="fw-bold mb-0">${d.serie}-${d.folio}</h4>
                <div class="mt-1">${estadoBadge}</div>
                <small class="text-muted">${fmtD(d.fecha)}</small>
            </div>
        </div>
        <hr>
        <!-- Receptor -->
        <div class="row mb-3">
            <div class="col-md-6">
                <h6 class="fw-bold text-uppercase text-muted mb-1">Receptor</h6>
                <p class="mb-0 fw-semibold">${d.cliente}</p>
                <small class="text-muted">${cust.tax_id ?? ''}</small><br>
                <small class="text-muted">${cust.email ?? ''}</small>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold text-uppercase text-muted mb-1">UUID SAT</h6>
                <small class="font-monospace text-break">${stamp.uuid ?? d.factura_uuid ?? '—'}</small>
            </div>
        </div>
        <!-- Conceptos -->
        <h6 class="fw-bold text-uppercase text-muted mb-2">Conceptos</h6>
        <div class="table-responsive mb-3">
            <table class="table table-sm table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Cant.</th><th>Descripción</th><th>Clave SAT</th>
                        <th class="text-end">Precio unit.</th><th class="text-end">Importe</th>
                    </tr>
                </thead>
                <tbody>${filas || '<tr><td colspan="5" class="text-center text-muted">Sin detalle disponible</td></tr>'}</tbody>
            </table>
        </div>
        <!-- Totales -->
        <div class="row justify-content-end">
            <div class="col-md-5">
                <table class="table table-sm mb-0">
                    <tr><td>Subtotal</td><td class="text-end">$${fmt(subtotal)}</td></tr>
                    <tr><td>IVA (16%)</td><td class="text-end">$${fmt(iva)}</td></tr>
                    <tr class="fw-bold table-light"><td>Total</td><td class="text-end">$${fmt(total)}</td></tr>
                </table>
            </div>
        </div>
    </div>`;
}

$(document).ready(function () {
    // Inicializar DataTable sin ordenamiento en columnas
    const dt = new DataTable('#tbl-facturas', {
        ordering: false
    });

    // ── Filtros rápidos por fecha ─────────────────────────────────
    function aplicarFiltroFecha(rango) {
        // Actualizar botones activos
        document.querySelectorAll('.btn-filtro-fecha').forEach(btn => {
            btn.classList.remove('active');
        });
        const btnActivo = document.querySelector(`.btn-filtro-fecha[data-rango="${rango}"]`);
        if (btnActivo) btnActivo.classList.add('active');

        // Mostrar todas las filas primero
        dt.rows().every(function (rowIdx, tableLoop, rowLoop) {
            $(this.node()).show();
        });

        if (rango === 'todas') {
            return;
        }

        const hoy = new Date();
        let inicio, fin;

        switch (rango) {
            case 'hoy':
                inicio = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate());
                fin = new Date(inicio);
                fin.setDate(fin.getDate() + 1);
                break;
            case 'semana':
                const diaSem = hoy.getDay(); // 0=domingo
                const diff = diaSem === 0 ? 6 : diaSem - 1; // lunes = 0
                inicio = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate() - diff);
                fin = new Date(inicio);
                fin.setDate(fin.getDate() + 7);
                break;
            case 'mes':
                inicio = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
                fin = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 1);
                break;
        }

        const fmtInicio = inicio.toISOString().split('T')[0];
        const fmtFin = fin.toISOString().split('T')[0];

        // Filtrar por rango de fecha usando el atributo data-order de la columna "Fecha timbrado" (índice 4)
        dt.rows().every(function (rowIdx, tableLoop, rowLoop) {
            const rowNode = this.node();
            const cells = rowNode.querySelectorAll('td');
            // La celda de fecha es la número 4 (índice 4)
            const fechaCell = cells[4];
            if (fechaCell) {
                const fechaAttr = fechaCell.getAttribute('data-order') || '';
                // Extraer solo la parte de la fecha (YYYY-MM-DD)
                const fechaFactura = fechaAttr.split(' ')[0];
                if (fechaFactura && (fechaFactura < fmtInicio || fechaFactura >= fmtFin)) {
                    $(this.node()).hide();
                }
            }
        });
    }

    document.querySelectorAll('.btn-filtro-fecha').forEach(btn => {
        btn.addEventListener('click', function () {
            aplicarFiltroFecha(this.dataset.rango);
        });
    });

    // ── Vista previa ──────────────────────────────────────────────
    document.querySelectorAll('.btn-preview').forEach(btn => {
        btn.addEventListener('click', function () {
            const data = JSON.parse(this.dataset.factura);
            document.getElementById('prev-titulo').textContent =
                'Factura ' + (data.serie ?? '') + '-' + (data.folio ?? '');
            document.getElementById('prev-body').innerHTML = buildPreview(data);
            const modalEl = document.getElementById('modalPreview');
            const modalPreview = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modalPreview.show();
        });
    });

    // ── Cancelar ──────────────────────────────────────────────────
    let facturaIdCancelar = null;

    document.querySelectorAll('.btn-cancelar').forEach(btn => {
        btn.addEventListener('click', function () {
            facturaIdCancelar = this.dataset.id;
            document.getElementById('folio-cancelar').textContent = this.dataset.folio;
            const modalEl = document.getElementById('modalCancelar');
            const modalCancelar = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modalCancelar.show();
        });
    });

    document.getElementById('btn-confirmar-cancelar').addEventListener('click', function () {
        if (!facturaIdCancelar) return;
        const motivo = document.getElementById('motivo-cancelar').value;

        axios.post('<?= base_url('facturas/cancelar') ?>', {
            id_factura : facturaIdCancelar,
            motivo     : motivo,
        }).then(res => {
            if (res.data.status === 'success') {
                location.reload();
            } else {
                alert('Error: ' + (res.data.message ?? 'No se pudo cancelar'));
            }
        }).catch(err => {
            alert('Error: ' + (err.response?.data?.message ?? err.message));
        });
    });
});
</script>

<?php echo $this->endSection()?>

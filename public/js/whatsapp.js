(function () {
    const MODAL_ID = 'modalWhatsappGlobal';
    const TOAST_ID = 'toastWhatsappGlobal';

    const WA_ICON = `<i class="bi bi-whatsapp me-1"></i>`;

    function inject() {
        if (document.getElementById(MODAL_ID)) return;

        document.body.insertAdjacentHTML('beforeend', `
        <div class="modal fade" id="${MODAL_ID}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background:#25D366;">
                        <h5 class="modal-title">${WA_ICON} Enviar por WhatsApp</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-1">Número destino:</p>
                        <p class="fw-semibold mb-3" id="wa-telefono-preview">—</p>
                        <p class="text-muted small mb-1">Mensaje que se enviará:</p>
                        <div id="wa-msg-preview" class="rounded p-3 bg-light border"
                             style="font-size:.9rem; white-space:pre-wrap;"></div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button id="wa-btn-confirmar" class="btn text-white" style="background:#25D366; border-color:#25D366;">
                            ${WA_ICON} Enviar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="${TOAST_ID}" class="toast align-items-center text-white border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body" id="wa-toast-body"></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>`);

        document.getElementById('wa-btn-confirmar').addEventListener('click', function () {
            const btn  = this;
            const tipo = btn.dataset.tipo;
            const id   = btn.dataset.id;

            btn.disabled    = true;
            btn.innerHTML   = '<span class="spinner-border spinner-border-sm me-1"></span> Enviando…';

            axios.post('/whatsapp/send', { tipo, id: parseInt(id) })
                .then(res => {
                    bootstrap.Modal.getInstance(document.getElementById(MODAL_ID)).hide();
                    waToast(res.data.message || 'Mensaje enviado correctamente', false);
                })
                .catch(err => {
                    const msg = err.response?.data?.message || 'Error al enviar el mensaje';
                    waToast(msg, true);
                })
                .finally(() => {
                    btn.disabled  = false;
                    btn.innerHTML = `${WA_ICON} Enviar`;
                });
        });
    }

    function waToast(mensaje, esError) {
        const el = document.getElementById(TOAST_ID);
        el.classList.remove('bg-success', 'bg-danger');
        el.classList.add(esError ? 'bg-danger' : 'bg-success');
        document.getElementById('wa-toast-body').textContent = mensaje;
        bootstrap.Toast.getOrCreateInstance(el).show();
    }

    function formatearTelefono(tel) {
        const n = (tel || '').replace(/\D/g, '');
        return (n.length === 10 ? '52' + n : n);
    }

    // ── API pública ─────────────────────────────────────────────────
    window.abrirModalWA = function (tipo, id, telefono, msgPreview) {
        inject();
        document.getElementById('wa-telefono-preview').textContent = '+' + formatearTelefono(telefono);
        document.getElementById('wa-msg-preview').textContent      = msgPreview;
        const btn     = document.getElementById('wa-btn-confirmar');
        btn.dataset.tipo = tipo;
        btn.dataset.id   = String(id);
        bootstrap.Modal.getOrCreateInstance(document.getElementById(MODAL_ID)).show();
    };

    document.addEventListener('DOMContentLoaded', inject);
})();

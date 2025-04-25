<?= $this->extend('Panel/panel_template'); ?>

<?= $this->section('contenido'); ?>
<div class="container mt-4">
    <h1><?= $title; ?></h1>

    <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach (session('errors') as $error): ?>
                    <li><?= esc($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (session()->has('message')): ?>
        <div class="alert alert-success">
            <?= session('message'); ?>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-6">
            <form action="/gastos/guardar" method="post">
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                </div>
                
                <div class="mb-3">
                    <label for="monto" class="form-label">Monto</label>
                    <input type="number" step="0.01" class="form-control" id="monto" name="monto" required>
                </div>
                
                <div class="mb-3">
                    <label for="fecha_gasto" class="form-label">Fecha del Gasto</label>
                    <input type="date" class="form-control" id="fecha_gasto" name="fecha_gasto" required>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="1" id="esTransferencia" name="es_transferencia">
                    <label class="form-check-label" for="esTransferencia">
                        ¿Es una transferencia entre cuentas?
                    </label>
                </div>

                <div class="mb-3">
                    <label for="cuenta_origen" class="form-label">Cuenta de Origen</label>
                    <select name="cuenta_origen" id="cuenta_origen" class="form-select" required>
                        <option value="">Seleccione una cuenta</option>
                        <?php foreach ($cuentas as $cuenta): ?>
                            <option value="<?= $cuenta['id_cuenta']; ?>"><?= esc($cuenta['banco']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3" id="cuenta_destino_group" style="display: none;">
                    <label for="cuenta_destino" class="form-label">Cuenta de Destino</label>
                    <select name="cuenta_destino" id="cuenta_destino" class="form-select">
                        <option value="">Seleccione una cuenta</option>
                        <?php foreach ($cuentas as $cuenta): ?>
                            <option value="<?= $cuenta['id_cuenta']; ?>"><?= esc($cuenta['banco']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="<?= base_url('gastos/inicio'); ?>" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkbox = document.getElementById('esTransferencia');
        const destinoGroup = document.getElementById('cuenta_destino_group');
        const cuentaDestino = document.getElementById('cuenta_destino');
        const cuentaOrigen = document.getElementById('cuenta_origen');
        const form = document.querySelector('form');

        const errorBox = document.createElement('div');
        errorBox.className = 'alert alert-danger';
        errorBox.style.display = 'none';
        form.insertBefore(errorBox, form.firstChild);

        checkbox.addEventListener('change', function () {
            if (this.checked) {
                destinoGroup.style.display = 'block';
                cuentaDestino.required = true;
            } else {
                destinoGroup.style.display = 'none';
                cuentaDestino.required = false;
                cuentaDestino.value = '';
                errorBox.style.display = 'none';
                errorBox.innerHTML = '';
            }
        });

        form.addEventListener('submit', function (e) {
            const origen = cuentaOrigen.value;
            const destino = cuentaDestino.value;

            // Solo validar si es transferencia
            if (checkbox.checked && origen && destino && origen === destino) {
                e.preventDefault(); // Detener envío
                errorBox.innerHTML = '<ul><li>No puedes transferir entre la misma cuenta.</li></ul>';
                errorBox.style.display = 'block';
            }
        });
    });
</script>

<?= $this->endSection(); ?>

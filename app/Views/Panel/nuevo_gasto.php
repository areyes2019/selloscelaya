<?= $this->extend('Panel/panel_template'); ?>

<?= $this->section('contenido'); ?>
<div class="container mt-4">
    <h1><?= $title; ?></h1>

    <!-- Mensajes Flash -->
    <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach (session('errors') as $error): ?>
                    <li><?= esc($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger">
            <?= session('error'); ?>
        </div>
    <?php endif; ?>

    <?php if (session()->has('message')): ?>
        <div class="alert alert-success">
            <?= session('message'); ?>
        </div>
    <?php endif; ?>

    <!-- Formulario -->
    <div class="row">
        <div class="col-md-6">
            <form action="/gastos/guardar" method="post">
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <input type="text" class="form-control" id="descripcion" name="descripcion"
                           value="<?= old('descripcion'); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="salida" class="form-label">Monto del Gasto</label>
                    <input type="number" step="0.01" class="form-control" id="salida" name="salida"
                           value="<?= old('salida'); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="fecha_gasto" class="form-label">Fecha del Gasto</label>
                    <input type="date" class="form-control" id="fecha_gasto" name="fecha_gasto"
                           value="<?= old('fecha_gasto'); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="cuenta_origen" class="form-label">Cuenta</label>
                    <select name="cuenta_origen" id="cuenta_origen" class="form-select" required>
                        <option value="">Seleccione una cuenta</option>
                        <?php foreach ($cuentas as $cuenta): ?>
                            <option value="<?= $cuenta['id_cuenta']; ?>"
                                <?= old('cuenta_origen') == $cuenta['id_cuenta'] ? 'selected' : ''; ?>>
                                <?= esc($cuenta['banco']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="<?= base_url('gastos/inicio'); ?>" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

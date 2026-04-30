<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="container mt-3">
    <div class="my-card">
        <h2><?php echo $nombre?></h2>
    </div>
    <div class="my-card">
        <?php foreach ($clientes as $cliente): ?>
        <form class="row g-3" action="<?php echo base_url('actualizar_cliente'); ?>" method="post">
            <div class="col-md-6">
                <label for="inputEmail4" class="form-label">Nombre</label>
                <input type="text" class="my-input shadow-none form-control p-1"  value="<?php echo $cliente['nombre'] ?>" name="nombre">
                <input type="hidden" value="<?php echo $cliente['id_cliente']?>" name="idcliente">
            </div>
            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Correo</label>
                <input type="text" class="my-input shadow-none form-control p-1" value="<?php echo $cliente['correo'] ?? '' ?>" name="correo">
            </div>
            
            <!-- Nuevo campo tipo -->
            <div class="col-md-6">
                <label for="inputTipo" class="form-label">Tipo de cliente</label>
                <select id="inputTipo" class="form-select my-input shadow-none form-control p-1" name="tipo">
                    <option value="1" <?php echo (isset($cliente['tipo']) && $cliente['tipo'] == 1) ? 'selected' : '' ?>>Cliente</option>
                    <option value="2" <?php echo (isset($cliente['tipo']) && $cliente['tipo'] == 2) ? 'selected' : '' ?>>Distribuidor</option>
                </select>
            </div>
            
            <div class="col-12">
                <label for="inputAddress" class="form-label">Direccion Fiscal</label>
                <input type="text" class="my-input shadow-none form-control p-1" value="<?php echo $cliente['direccion'] ?? '' ?>" placeholder="Av Montaña 53, Col Arrendaderos" name="direccion">
            </div>
            <div class="col-12">
                <label for="inputAddress2" class="form-label">Telefono</label>
                <input type="text" class="my-input shadow-none form-control p-1" value="<?php echo $cliente['telefono'] ?>" name="telefono">
            </div>
            <div class="col-md-6">
                <label for="inputCity" class="form-label">Ciudad</label>
                <input type="text" class="my-input shadow-none form-control p-1" value="<?php echo $cliente['ciudad'] ?? '' ?>" name="ciudad">
            </div>
            <div class="col-md-4">
                <label for="inputState" class="form-label">Estado</label>
                <select id="inputState" class="form-select my-input shadow-none form-control p-1" name="estado">
                    <option value="">Seleccionar...</option>
                    <option value="gto" <?php echo (isset($cliente['estado']) && $cliente['estado'] == 'gto') ? 'selected' : '' ?>>Guanajuato</option>
                    <option value="mch" <?php echo (isset($cliente['estado']) && $cliente['estado'] == 'mch') ? 'selected' : '' ?>>Michoacán</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="inputZip" class="form-label">CP</label>
                <input type="text" class="my-input shadow-none form-control p-1" id="inputZip" value="<?php echo $cliente['cp'] ?? '' ?>" name="cp">
            </div>
            <div class="col-12">
                <button type="submit" class="my-btn-primary p-3"><span class="bi bi-save"></span> Guardar</button>
            </div>
        </form>
        <?php endforeach; ?>
    </div>                                                            
</div>
<?php echo $this->endSection()?>
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
		    	<input type="hidden" value="<?php echo $cliente['idCliente']?>" name="idcliente">
		 	</div>
			<div class="col-md-6">
			    <label for="inputPassword4" class="form-label">Correo</label>
			    <input type="text" class="my-input shadow-none form-control p-1" name="correo">
			</div>
			<div class="col-12">
			    <label for="inputAddress" class="form-label">Direccion Fiscal</label>
			    <input type="text" class="my-input shadow-none form-control p-1"  placeholder="Av Montaña 53, Col Arrendaderos" name="direccion">
			</div>
			<div class="col-12">
			    <label for="inputAddress2" class="form-label">Telefono</label>
			    <input type="text" class="my-input shadow-none form-control p-1"  value="<?php echo $cliente['telefono'] ?>" name="telefono">
			</div>
			<div class="col-md-6">
			    <label for="inputCity" class="form-label">Ciudad</label>
			    <input type="text" class="my-input shadow-none form-control p-1"  name="ciudad">
			</div>
			<div class="col-md-4">
			    <label for="inputState" class="form-label">Estado</label>
			    <select id="inputState" class="form-select my-input shadow-none form-control p-1" name="estado">
			      <option selected>Choose...</option>
			      <option value="gto">Guanajauto</option>
			      <option value="mch">Michoacán</option>
			    </select>
			</div>
			<div class="col-md-2">
			    <label for="inputZip" class="form-label">CP</label>
			    <input type="text" class="my-input shadow-none form-control p-1" id="inputZip" name="cp">
			</div>
			<div class="col-12">
			    <button type="submit" class="my-btn-primary p-3"><span class="bi bi-save"></span> Guardar</button>
			</div>
		</form>
		<?php endforeach; ?>

	</div>																						
</div>
<?php echo $this->endSection()?>
<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="container mt-3">
	<form action="">
		<div class="form-group">
			<label for="">Nombre</label>
			<input type="text" class="form-control rounded-0">
		</div>
		<div class="form-group">
			<label for="">WhatsApp</label>
			<input type="text" class="form-control rounded-0">
		</div>
		<div class="form-group">
			<input type="submit" value="Guardar" class="btn btn-danger mt-3 rounded-0">
		</div>
	</form>
</div>
<?php echo $this->endSection('contenido')?>

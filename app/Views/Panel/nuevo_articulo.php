<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="container mt-3">
	<h1>Nuevo Articulo</h1>
	<form  action="">
		<div class="form-group">
			<label for="">Nombre</label>
			<input type="text" class="form-control rounded-0" placeholder="Nombre del articulo">
		</div>
		<div class="form-group">
			<label for="">Modelo</label>
			<input type="text" class="form-control rounded-0" placeholder="Nombre del articulo">
		</div>
		<div class="form-group">
			<label for="">Precio Proveedor</label>
			<input type="text" class="form-control rounded-0" placeholder="Nombre del articulo">
		</div>
		<div class="form-group">
			<label for="">Precio Público</label>
			<input type="text" class="form-control rounded-0" placeholder="Nombre del articulo">
		</div>
		<div class="form-group">
			<input type="submit" class="btn btn-danger rounded-0 mt-3" value="Guardar">
		</div>
	</form>
</div>
<?php echo $this->endSection()?>

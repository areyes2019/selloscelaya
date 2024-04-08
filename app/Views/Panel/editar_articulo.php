<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="container mt-3">
	<div class="my-card">
		<h4 class="m-0 p-0"><?php echo $nombre?></h4>
	</div>
	<div class="my-card mt-3">
		<?php foreach ($articulos as $articulo): ?>
		<form class="row g-3" action="<?php echo base_url('actualizar_articulo'); ?>" method="post">
		  	<div class="col-md-6">
		    	<label for="inputEmail4" class="form-label">Nombre</label>
		    	<input type="text" class="my-input shadow-none form-control p-1"  value="<?php echo $articulo['nombre'] ?>" name="nombre">
		    	<input type="hidden" value="<?php echo $articulo['idArticulo']?>" name="idarticulo">
		 	</div>
			<div class="col-md-6">
			    <label for="inputPassword4" class="form-label">Modelo</label>
			    <input type="text" class="my-input shadow-none form-control p-1" name="modelo" value="<?php echo $articulo['modelo'] ?>">
			</div>
			<div class="col-md-4">
			    <label for="inputAddress" class="form-label">Precio Proveedor</label>
			    <input type="text" class="my-input shadow-none form-control p-1" name="precio_prov" value="<?php echo $articulo['precio_prov'] ?>">
			</div>
			<div class="col-md-4">
			    <label for="inputAddress2" class="form-label">Precio Púbico</label>
			    <input type="text" class="my-input shadow-none form-control p-1"  value="<?php echo $articulo['precio_pub'] ?>" name="precio_pub">
			</div>
			<div class="col-md-4">
			    <label for="inputCity" class="form-label">Minimoa para reorden</label>
			    <input type="number" class="my-input shadow-none form-control p-1" min="0" name="minimo" value="<?php echo $articulo['minimo'] ?>">
			</div>
			<div class="col-md-3">
			    <label for="inputState" class="form-label">En Stok</label>
			    <select id="inputState" class="form-select my-input shadow-none form-control p-1" name="stock">
			      <option selected>Escoge una opción...</option>
			      <option value="1">Sí</option>
			      <option value="0">No</option>
			    </select>
			</div>
			<div class="col-12">
			    <button type="submit" class="my-btn-primary p-3"><span class="bi bi-save"></span> Guardar</button>
			</div>
		</form>
		<?php endforeach; ?>
	</div>
</div>
<?php echo $this->endSection()?>
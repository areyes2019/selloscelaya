<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="container mt-3">
	<div class="my-card">
		<h2><?php echo $empresa?></h2>
	</div>
	<div class="my-card">
		<?php foreach ($proveedores as $proveedor): ?>
		<form class="row g-3" action="<?php echo base_url('actualizar_proveedor'); ?>" method="post">
		  	<div class="col-md-6">
		    	<label for="inputEmail4" class="form-label">Empresa</label>
		    	<input type="text" class="my-input shadow-none form-control p-1"  value="<?php echo $proveedor['empresa'] ?>" name="empresa">
		    	<input type="hidden" value="<?php echo $proveedor['id_proveedor']?>" name="id_proveedor">
		 	</div>
			<div class="col-md-6">
			    <label for="inputPassword4" class="form-label">Contacto</label>
			    <input type="text" class="my-input shadow-none form-control p-1" name="contacto" value="<?php echo $proveedor['contacto']?>">
			</div>
			<div class="col-md-6">
			    <label for="inputPassword4" class="form-label">Teléfono</label>
			    <input type="text" class="my-input shadow-none form-control p-1" name="telefono" value="<?php echo $proveedor['telefono']?>">
			</div>
			<div class="col-md-6">
			    <label for="inputPassword4" class="form-label">Correo</label>
			    <input type="text" class="my-input shadow-none form-control p-1" name="correo" value="<?php echo $proveedor['correo']?>">
			</div>
			
			<div class="col-12">
			    <button type="submit" class="btn btn-primary btn-icon-split">
			    	<span class="icon text-white-50">
			    		<i class="bi bi-save"></i>
			    	</span>
			    	<span class="text">Actualizar</span>
			    </button>
			</div>
		</form>
		<?php endforeach; ?>

	</div>																						
</div>
<?php echo $this->endSection()?>
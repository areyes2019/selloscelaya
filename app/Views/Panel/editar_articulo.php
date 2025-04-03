<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="container mt-3">
	<div class="my-card">
		<h4 class="m-0 p-0"><?php echo $nombre?></h4>
	</div>
	<div class="my-card mt-3">
		<?php foreach ($articulos as $articulo): ?>
		<form method="post" action="<?= base_url('actualizar_articulo') ?>" enctype="multipart/form-data">
		    <input type="hidden" name="idarticulo" value="<?= $articulo['id_articulo'] ?>">
		    <input type="hidden" name="imagen_actual" value="<?= $articulo['img'] ?>">
		    
		    <!-- Campos del formulario -->
		    <div class="form-group">
		        <label>Nombre</label>
		        <input type="text" name="nombre" class="form-control" value="<?= $articulo['nombre'] ?>" required>
		    </div>
		    
		    <div class="form-group">
		        <label>Precio Proveedor</label>
		        <input type="number" step="0.01" name="precio_prov" class="form-control" value="<?= $articulo['precio_prov'] ?>" required>
		    </div>
		    
		    <!-- Mostrar precios calculados (solo lectura) -->
		    <div class="form-group">
		        <label>Precio Público</label>
		        <input type="text" class="form-control" value="<?= $articulo['precio_pub'] ?>" readonly>
		    </div>
		    
		    <div class="form-group">
		        <label>Precio Distribuidor</label>
		        <input type="text" class="form-control" value="<?= $articulo['precio_dist'] ?>" readonly>
		    </div>
		    
		    <div class="form-group">
		        <label>Imagen Actual</label>
		        <?php if ($articulo['img']): ?>
		            <img src="<?= base_url('articulos/verImagen/'.$articulo['img']) ?>" class="img-thumbnail mb-2" width="100">
		        <?php endif; ?>
		        <input type="file" name="img" class="form-control-file">
		    </div>
		    
		    <button type="submit" class="btn btn-primary">Actualizar Artículo</button>
		</form>
		<?php endforeach; ?>
	</div>
</div>
<?php echo $this->endSection()?>
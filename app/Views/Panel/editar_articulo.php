<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="container mt-3">
	<div class="my-card">
		<h4 class="m-0 p-0"><?php echo $nombre?></h4>
	</div>
	<div class="my-card mt-3">
	    <?php foreach ($articulos as $articulo): ?>
	    <form class="row g-3" action="<?= base_url('actualizar_articulo') ?>" method="post" enctype="multipart/form-data">
	        <input type="hidden" name="idarticulo" value="<?= $articulo['id_articulo'] ?>">
	        <input type="hidden" name="imagen_actual" value="<?= $articulo['img'] ?>">
	        
	        <div class="col-md-6">
	            <label class="form-label">Nombre</label>
	            <input type="text" class="my-input shadow-none form-control p-1" name="nombre" value="<?= $articulo['nombre'] ?>">
	        </div>
	        
	        <div class="col-md-6">
	            <label class="form-label">Modelo</label>
	            <input type="text" class="my-input shadow-none form-control p-1" name="modelo" value="<?= $articulo['modelo'] ?>">
	        </div>
	        
	        <div class="col-md-4">
	            <label class="form-label">Precio Proveedor</label>
	            <input type="number" step="0.01" class="my-input shadow-none form-control p-1" name="precio_prov" value="<?= $articulo['precio_prov'] ?>">
	        </div>
	        
	        <div class="col-md-4">
	            <label class="form-label">Precio Público</label>
	            <input type="text" class="my-input shadow-none form-control p-1" value="<?= $articulo['precio_pub'] ?>" readonly>
	        </div>
	        
	        <div class="col-md-4">
	            <label class="form-label">Precio Distribuidor</label>
	            <input type="text" class="my-input shadow-none form-control p-1" value="<?= $articulo['precio_dist'] ?>" readonly>
	        </div>
	        
	        <div class="col-md-4">
	            <label class="form-label">Mínimo para reorden</label>
	            <input type="number" class="my-input shadow-none form-control p-1" min="0" name="minimo" value="<?= $articulo['minimo'] ?>">
	        </div>
	        
	        <div class="col-md-4">
	            <label class="form-label">Clave del producto</label>
	            <input type="text" class="my-input shadow-none form-control p-1" name="clave_producto" value="<?= $articulo['clave_producto'] ?>">
	        </div>
	        
	        <div class="col-md-4">
	            <label class="form-label">Imagen</label>
	            <input type="file" class="my-input shadow-none form-control p-1" name="img">
	            <?php if ($articulo['img']): ?>
	                <small class="text-muted">Imagen actual: <?= $articulo['img'] ?></small>
	            <?php endif; ?>
	        </div>
	        
	        <div class="col-md-3">
	            <label class="form-label">En Stock</label>
	            <select class="form-select my-input shadow-none form-control p-1" name="stock">
	                <option value="1" <?= $articulo['stock'] == 1 ? 'selected' : '' ?>>Sí</option>
	                <option value="0" <?= $articulo['stock'] == 0 ? 'selected' : '' ?>>No</option>
	            </select>
	        </div>
	        
	        <div class="col-md-3">
	            <label class="form-label">Disponible para venta</label>
	            <select class="form-select my-input shadow-none form-control p-1" name="venta">
	                <option value="1" <?= $articulo['venta'] == 1 ? 'selected' : '' ?>>Sí</option>
	                <option value="0" <?= $articulo['venta'] == 0 ? 'selected' : '' ?>>No</option>
	            </select>
	        </div>
	        
	        <div class="col-12">
	            <button type="submit" class="my-btn-primary p-3">
	                <span class="bi bi-save"></span> Guardar
	            </button>
	        </div>
	    </form>
	    <?php endforeach; ?>
	</div>
</div>
<?php echo $this->endSection()?>
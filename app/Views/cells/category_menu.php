<?php if (!empty($categorias)): ?>
    <?php foreach($categorias as $categoria): ?>
        <a href="<?php echo base_url('articulos/'.url_title(esc($categoria['nombre']), '-', true));?>" class="dropdown-item">
            <?= esc($categoria['nombre']) ?>
        </a>
    <?php endforeach; ?>
<?php else: ?>
    <a href="#" class="dropdown-item">No hay categorías disponibles</a>
<?php endif; ?>
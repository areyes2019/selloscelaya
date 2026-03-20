<?= $this->extend('Panel/panel_template') ?>

<?= $this->section('titulo') ?>
    <?= $titulo_pagina ?? 'Inventario' ?>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<div class="container-fluid" id="app">

<!-- ALERTAS -->
<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<!-- RESUMEN -->
<div class="row mb-4">
<div class="col-md-4">
<h4>Valor Público</h4>
<h2><?= number_to_currency($valor_total_inventario, 'MXN') ?></h2>
</div>

<div class="col-md-4">
<h4>Utilidad</h4>
<h2><?= number_to_currency($valor_utilidades, 'MXN') ?></h2>
</div>

<div class="col-md-4">
<h4>Costo</h4>
<h2><?= number_to_currency($valor_neto_inventario, 'MXN') ?></h2>
</div>
</div>

<!-- TABLA -->
<table class="table table-bordered">
<thead>
<tr>
<th>ID Art</th>
<th>Nombre</th>
<th>Modelo</th>
<th>Stock</th>
<th>Mínimo</th>
<th>Dif</th>
<th>Precio</th>
<th>Total</th>
<th>Acciones</th>
</tr>
</thead>

<tbody>
<?php foreach ($lista as $item): ?>
<tr>

<td><?= $item['id_articulo'] ?></td>
<td><?= $item['nombre'] ?></td>
<td><?= $item['modelo'] ?></td>

<td><?= $item['stock'] ?></td>
<td><?= $item['minimo'] ?></td>

<?php
$variacion = $item['stock'] - $item['minimo'];
$color = ($item['stock'] < $item['minimo']) ? 'color:red;' : '';
?>

<td style="<?= $color ?>"><?= $variacion ?></td>

<td><?= number_to_currency($item['precio_pub'], 'MXN') ?></td>

<td>
<?= number_to_currency($item['precio_pub'] * $item['stock'], 'MXN') ?>
</td>

<td>
<button class="btn btn-warning"
@click="cambiar_inventario(<?= $item['id_articulo'] ?>)">
Editar
</button>
</td>

</tr>
<?php endforeach; ?>
</tbody>
</table>

</div>

<?= $this->endSection() ?>
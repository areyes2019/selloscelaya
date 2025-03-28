<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="container mt-3">
	<div class="my-card mb-3">
		<h2>Número: 563</h2>
	</div>
	<div class="row">
		<div class="col-8">
			<div class="my-card mb-3">
				<div class="input-articles">
					<select class="select-nav" name='marcas' id="articulo">
					    <option value=''>Seleccionar un modelo</option>
					    <option value='audi'>Sello Fechador - Printer 55 Dater</option>
					    <option value='bmw'>Printer C40</option>
					    <option value='citroen'>Printer C30</option>
					    <option value='fiat'>Printer C20</option>
					    <option value='ford'>Printer 55</option>
					    <option value='honda'>Printer 35 Dater</option>
					    <option value='hyundai'>Printer Q43 Dater</option>
					    <option value='kia'>Printer Q43</option>
					    <option value='mazda'>Tinta Negro 801</option>
					</select>
					<label for="">Cant.</label>
					<input type="number" class="my-input input-nav" min="1" value="1">
					<button class="my-btn-primary btn-nav"><span class="bi-check-lg"></span></button>
				</div>
			</div>
			<div class="my-card">
				<div class="row">
					<div class="col-4">
						<p class="m-0">De:</p>
						<p class="m-0">Sellos Celaya</p>
						<p class="m-0">Real del Seminario #122 <br>Valle Del Real. Celay Gto</p>
					</div>
					<div class="col-4">
						<p class="m-0">Para:</p>
						<p class="m-0">Adriana Salinas</p>
						<p class="m-0">461 2901 439</p>
					</div>
					<div class="col-4">
						<p class="m-0">Ticket: 2663</p>
						<p class="m-0">Clave: 5969</p>
						<p class="m-0">Fecha: 5 Ene 2024</p>
						
					</div>
				</div>
			</div>
			<div class="my-card mt-3">
				<table class="table mt-4">
						<thead>
							<tr>
								<th>Nombre</th>
								<th>Modelo</th>
								<th>Cantidad</th>
								<th>PU</th>
								<th>Total</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Sello de 59 x 23 mm</td>
								<td>Printer C40</td>
								<td>5</td>
								<td>$250.00</td>
								<td>$250.00</td>
								<td><button class="my-btn-danger float-right"><span class="bi bi-x-square-fill"></span></button></td>
							</tr>
						</tbody>
					</table>
			</div>
			<button class="my-btn-danger mt-3 p-3"><span class="bi bi-trash3"></span> Cancelar</button>
			<button class="my-btn-primary mt-3 p-3"><span class="bi bi-trash3"></span> Enviar WhatsApp</button>
			<button class="my-btn-primary mt-3 p-3"><span class="bi bi-download"></span> Descargar Img</button>
			<button class="my-btn-primary mt-3 p-3"><span class="bi bi-filetype-pdf"></span> Descargar PDF</button>
			<button class="my-btn-primary mt-3 p-3"><span class="bi bi-send"></span> Enviar por Correo</button>
			</div>
			<div class="col-4">
				<div class="my-card">
					<div class="invoice-detail">
						<table>
							<tr class="line-invoice">
								<th>Sub-Total</th>
								<td>$250.00</td>
							</tr>
							<tr class="line-invoice">
								<th>Dcto.</th>
								<td>$250.00</td>
							</tr>
							<tr class="line-invoice">
								<th>IVA</th>
								<td>$250.00</td>
							</tr>
							<tr class="line-invoice">
								<th>Total</th>
								<td>$250.00</td>
							</tr>
							<tr>
								<td>
									<p>Anticipo:</p>
									<p>$250.00</p>
								</td>
								<td>
									<p>Saldo:</p>
									<p>$450.00</p>
								</td>
							</tr>
						</table>
					</div>
					<div class="payment-data">
						<button class="my-btn-success p-3 mt-3 w-100" data-bs-toggle="collapse" data-bs-target="#add_payment"><span class="bi bi-cash-coin"></span> Agregar Pago</button>
						<div class="collapse" id="add_payment">
							<div class="payment-group w-100 mt-3">
								<input type="text" class="my-input">
								<button class="my-btn-primary"><span class="bi-check-lg"></span></button>
							</div>
						</div>
						<button class="my-btn-danger p-3 mt-3 w-100"><span class="bi bi-currency-dollar"></span> Marcar Pago Total</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo $this->endSection()?>
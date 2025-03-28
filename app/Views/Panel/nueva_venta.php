<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="container mt-3 mb-4">
	<h2>Nueva Venta</h2>
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
					<label for="">Anticipo</label>
					<input type="text" class="my-input input-nav">
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
			</div>
			<div class="col-4">
				<div class="my-card">
					<div class="ticket">
						<center>
							<img width="80" src="<?php echo base_url('public/img/LOGOciruclos3.png'); ?>" alt="">
						</center>
						<p class="mt-3"><center>Real del Seminario 122, Col Valle del Real <br>Celaya, Gto</center></p>
						<p>WhatsApp 461 358 1090</p>
						<p class="italic-font">ventas@sellopronto.com.mx</p>
						<h2 class="mt-4">Ticket: 452</h2>
						<h6>Ref: 8599</h6>
					</div>
					<hr>
					<div class="ticket-body">
						<div class="ticket-item">
							<div>
								<p>1</p>
							</div>
							<div>
								<p>Sello de 49 x 59 mm - Printer C40 - Tinta azul</p>
								<p>$250.00</p>
							</div>
						</div>	
					</div>
					<div class="resumen">
						<h6>Su pago</h6>
						<table class="table mt-3 align-text-bottom">
							<tr>
								<td>Total</td>
								<td>$250.00</td>
							</tr>
							<tr>
								<td>Anticipo</td>
								<td>$250.00</td>
							</tr>
							<tr>
								<td>Saldo</td>
								<td>$150.00</td>
							</tr>
						</table>
					</div>
					<small><center>Tenga a la manos simepre el ticket de compra para recoger su pedido. Si alguien mas va a recoger, tenga la bondad de reenviar este ticket.</center></small>
					<div class="payment-data">
						<button class="my-btn-success p-3 mt-3 w-100" data-bs-toggle="collapse" data-bs-target="#add_payment"><span class="bi bi-box-seam"></span> Agregar Datos de Envío</button>
						<div class="collapse" id="add_payment">
							<div class="payment-group w-100 mt-2">
								<input type="text" class="my-input form-control shadow-none mt-1" placeholder="Nombre de quien recibe">
								<input type="text" class="my-input form-control shadow-none mt-1" placeholder="Dirección">
								<input type="text" class="my-input form-control shadow-none mt-1" placeholder="Numero de WhatsApp">
								<button class="my-btn-primary mt-3 p-2"><span class="bi-check-lg"></span> Guardar</button>
							</div>
						</div>
						<button class="my-btn-danger p-3 mt-2 w-100"><span class="bi bi-currency-dollar"></span> Marcar Pago Total</button>
						<button class="my-btn-primary p-3 mt-2 w-100"><span class="bi bi-currency-dollar"></span> Marcar Enviado</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo $this->endSection('contenido')?>
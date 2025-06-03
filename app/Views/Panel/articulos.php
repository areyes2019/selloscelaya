<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="midde_cont" id="app">
    <div class="container-fluid">
        <div class="row column_title card rounded-0 shadow-sm">
            <div class="col-md-12">
                <div class="page_title">
                    <h2>Lista de precios</h2>
                </div>
                <!-- Mensajes Flash -->
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- row -->
        <div class="row">
            <!-- table section -->
            <div class="col-md-12">
                <div class="white_shd full margin_bottom_30">
                    <div class="full graph_head">
                        <div class="heading1 margin_0">
                            <a class="btn btn-danger rounded-0 btn-sm mb-5" href="<?php echo base_url('nuevo_art_vista'); ?>">Agregar Articulo</a>
                            <a href="<?php echo base_url('categorias'); ?>" class="btn btn-danger btn-sm rounded-0 mb-5">Categorías</a>
                            <a class="btn btn-danger rounded-0 btn-sm mb-5" 
                               href="#" 
                               @click="eliminarSeleccionados" 
                               :disabled="selectedItems.length === 0">
                               Eliminar seleccionados
                            </a>
                        </div>
                    </div>
                    <div class="card rounded-0 shadow-sm table_section padding_infor_info">
                        <div class="table-responsive-sm">
                            <table id="example" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" 
                                                   @change="toggleSelectAll"
                                                   :checked="selectedItems.length === paginatedArticles().length && paginatedArticles().length > 0">
                                        </th>
                                        <th>#</th>
                                        <th>Img</th>
                                        <th>Nombre</th>
                                        <th>Modelo</th>
                                        <th>Proveedor</th>
                                        <th>Costo</th>
                                        <th>Precio Dist.</th>
                                        <th>Precio Público</th>
                                        <th>Beneficio</th>
                                        <th>Stock</th>
                                        <th>Visible</th>
                                        <th>Acciones</th>
                                    </tr>
                                    <!-- Fila de búsqueda -->
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>
                                            <input type="text" class="form-control form-control-sm" 
                                                   placeholder="Buscar por nombre" 
                                                   v-model="filtroNombre"
                                                   @input="filtrarArticulos">
                                        </th>
                                        <th>
                                            <input type="text" class="form-control form-control-sm" 
                                                   placeholder="Buscar por modelo" 
                                                   v-model="filtroModelo"
                                                   @input="filtrarArticulos">
                                        </th>
                                        <th>
                                            <input type="text" class="form-control form-control-sm" 
                                                   placeholder="Buscar por proveedor" 
                                                   v-model="filtroProveedor"
                                                   @input="filtrarArticulos">
                                        </th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="articulo in paginatedArticles()" :key="articulo.id_articulo">
                                        <td>
                                            <input type="checkbox" class="item-checkbox" 
                                                   :value="articulo.id_articulo" 
                                                   v-model="selectedItems"
                                                   @change="updateSelectAllState">
                                        </td>
                                        <td>{{ articulo.id_articulo }}</td>
                                        <td>
                                            <template v-if="articulo.img">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#imagenModal" 
                                                   :data-imagen="'public/img/catalogo/' + articulo.img"
                                                   :data-nombre="articulo.nombre">
                                                   <img :src="'public/img/catalogo/' + articulo.img" 
                                                        :alt="articulo.nombre" 
                                                        style="width: 50px; height: 50px; object-fit: contain;">
                                                </a>
                                            </template>
                                            <template v-else>
                                                <span class="text-muted">Sin imagen</span>
                                            </template>
                                        </td>
                                        <td>{{ articulo.nombre }}</td>
                                        <td>{{ articulo.modelo }}</td>
                                        <td>{{ articulo.nombre_proveedor || 'No especificado' }}</td>
                                        <td>{{ formatNumber(articulo.precio_prov) }}</td>
                                        <td><strong class="text-primary">{{ formatNumber(articulo.precio_dist) }}</strong></td>
                                        <td><strong>{{ formatNumber(articulo.precio_pub) }}</strong></td>
                                        <td>{{ formatNumber(articulo.precio_pub - articulo.precio_prov) }}</td>
                                        <td>
                                            <template v-if="articulo.stock > 0">
                                                <span class="badge bg-success">Disponible</span>
                                                <small class="d-block">Inventario: {{ articulo.stock }}</small>
                                            </template>
                                            <template v-else>
                                                <span class="badge bg-secondary">Pedido especial</span>
                                                <small class="d-block">Sobre pedido</small>
                                            </template>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       role="switch" 
                                                       :id="'visibleSwitch_' + articulo.id_articulo"
                                                       :checked="articulo.visible == 1"
                                                       @click="cambiar_visible(articulo.id_articulo, $event)"
                                                       :title="articulo.visible == 1 ? 'Marcar como Oculto' : 'Marcar como Visible'">
                                                <label class="form-check-label visually-hidden" 
                                                       :for="'visibleSwitch_' + articulo.id_articulo">
                                                       Visibilidad del artículo {{ articulo.id_articulo }}
                                                </label>
                                            </div>
                                        </td>
                                        <td class="d-flex gap-1">
                                            <!-- Eliminar -->
                                            <a :href="'eliminar_articulo/' + articulo.id_articulo" 
                                               onclick="return confirm('¿Seguro que quieres eliminar este registro?')" 
                                               class="btn btn-sm btn-danger rounded-0"
                                               title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                            
                                            <!-- Editar --> 
                                            <a :href="'editar_articulo/' + articulo.id_articulo" 
                                               class="btn btn-sm btn-success rounded-0"
                                               title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            <!-- Edición rápida -->
                                            <button type="button" 
                                                    class="btn btn-sm btn-primary rounded-0" 
                                                    @click="cambio_rapido(articulo.id_articulo)"
                                                    title="Edición rápida">
                                                <i class="bi bi-lightning-charge"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- Paginación -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    Mostrando {{ (currentPage - 1) * itemsPerPage + 1 }} - 
                                    {{ Math.min(currentPage * itemsPerPage, totalItems) }} de {{ totalItems }} artículos
                                </div>
                                
                                <nav aria-label="Page navigation">
                                    <ul class="pagination pagination-sm">
                                        <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                            <button class="page-link" @click="changePage(currentPage - 1)">Anterior</button>
                                        </li>
                                        
                                        <li class="page-item" v-for="page in totalPages()" :key="page" 
                                            :class="{ active: currentPage === page }">
                                            <button class="page-link" @click="changePage(page)">{{ page }}</button>
                                        </li>
                                        
                                        <li class="page-item" :class="{ disabled: currentPage === totalPages() }">
                                            <button class="page-link" @click="changePage(currentPage + 1)">Siguiente</button>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Modal para mostrar imagen -->
<div class="modal fade" id="imagenModal" tabindex="-1" aria-labelledby="imagenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imagenModalLabel">Imagen del producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImagen" src="" class="img-fluid" alt="Imagen ampliada" style="max-height: 70vh;">
                <h5 id="modalNombre" class="mt-3"></h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="copiarImagenBtn">
                    <i class="fab fa-whatsapp"></i> Copiar para WhatsApp
                </button>
                <a href="#" id="descargarImagenBtn" class="btn btn-primary">
                    <i class="fas fa-download"></i> Descargar imagen
                </a>
            </div>
        </div>
    </div>
</div>
<!-- Modal de Edición Rápida -->
<div class="modal fade" id="quickEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edición rápida</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del artículo</label>
                        <input type="text" class="form-control" id="nombre" v-model="articulo.nombre">
                    </div>
                    <div class="mb-3">
                        <label for="modelo" class="form-label">Modelo</label>
                        <input type="text" class="form-control" id="modelo" v-model="articulo.modelo">
                    </div>
                    <div class="mb-3">
                        <div class="row g-2">
                            <div class="col">
                                <label class="form-label">Precio Público</label>
                                <input type="number" step="0.01" class="form-control" placeholder="Público" v-model="articulo.precio_pub">
                            </div>
                            <div class="col">
                                <label class="form-label">Precio Dist.</label>
                                <input type="number" step="0.01" class="form-control" placeholder="Distribuidor" v-model="articulo.precio_dist">
                            </div>
                            <div class="col">
                                <label class="form-label">Precio Prov</label>
                                <input type="number" step="0.01" class="form-control" placeholder="Proveedor" v-model="articulo.precio_prov">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row g-2">
                            <div class="col">
                                <label for="" class="form-label">Categoria</label>
                                <select name="categoria" id="categoria" class="form-control" v-model="articulo.categoria">
                                    <option value="">Seleccione una categoría...</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= esc($categoria['id_categoria']) ?>">
                                            <?= esc($categoria['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button @click.prevent="guardarEdicionRapida(articulo.id_articulo)" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<script src="<?php echo base_url('public/js/articulos.js'); ?>"></script>
<script src="<?php echo base_url('public/js/notify.js'); ?>"></script>
<?php echo $this->endSection()?>
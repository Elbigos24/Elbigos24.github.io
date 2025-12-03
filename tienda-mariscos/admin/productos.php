<?php
require_once '../config/database.php';

if (!isset($_SESSION['usuario_id']) || !$_SESSION['es_admin']) {
    header('Location: ../login.php');
    exit();
}

$conn = getConnection();

// Agregar producto
if (isset($_POST['agregar_producto'])) {
    $nombre = limpiarInput($_POST['nombre']);
    $descripcion = limpiarInput($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $stock = floatval($_POST['stock']);
    $imagen = limpiarInput($_POST['imagen']);
    
    $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio_kg, stock_kg, imagen) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdds", $nombre, $descripcion, $precio, $stock, $imagen);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Producto agregado exitosamente';
        $_SESSION['mensaje_tipo'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'Error al agregar producto';
        $_SESSION['mensaje_tipo'] = 'danger';
    }
    
    $stmt->close();
    header('Location: productos.php');
    exit();
}

// Actualizar producto
if (isset($_POST['actualizar_producto'])) {
    $id = intval($_POST['id']);
    $nombre = limpiarInput($_POST['nombre']);
    $descripcion = limpiarInput($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $stock = floatval($_POST['stock']);
    $imagen = limpiarInput($_POST['imagen']);
    $activo = isset($_POST['activo']) ? 1 : 0;
    
    $stmt = $conn->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio_kg = ?, stock_kg = ?, imagen = ?, activo = ? WHERE id = ?");
    $stmt->bind_param("ssddsii", $nombre, $descripcion, $precio, $stock, $imagen, $activo, $id);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Producto actualizado exitosamente';
        $_SESSION['mensaje_tipo'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'Error al actualizar producto';
        $_SESSION['mensaje_tipo'] = 'danger';
    }
    
    $stmt->close();
    header('Location: productos.php');
    exit();
}

// Eliminar producto
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Producto eliminado exitosamente';
        $_SESSION['mensaje_tipo'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'Error al eliminar producto';
        $_SESSION['mensaje_tipo'] = 'danger';
    }
    
    $stmt->close();
    header('Location: productos.php');
    exit();
}

// Obtener todos los productos
$sql = "SELECT * FROM productos ORDER BY nombre";
$productos = $conn->query($sql);

$page_title = 'Gestión de Productos';
$css_path = '../css/styles.css';
$is_admin = true;
include '../includes/header.php';
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-box-seam"></i> Gestión de Productos</h1>
        <div>
            <button class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                <i class="bi bi-plus-circle"></i> Agregar Producto
            </button>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>
    
    <!-- Información de imágenes -->
    <div class="alert alert-info">
        <h5><i class="bi bi-info-circle"></i> Guía de Imágenes</h5>
        <p class="mb-2"><strong>Ubicación:</strong> Las imágenes deben estar en la carpeta <code>/images/productos/</code></p>
        <p class="mb-2"><strong>Formato de nombre:</strong> <code>producto-X.jpg</code> donde X es un número</p>
        <p class="mb-0"><strong>Ejemplo:</strong> Para el producto #5 usa: <code>producto-5.jpg</code></p>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Precio/kg</th>
                            <th>Stock (kg)</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($producto = $productos->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#<?php echo $producto['id']; ?></strong></td>
                            <td>
                                <?php 
                                $ruta_imagen = '../images/productos/' . ($producto['imagen'] ?: 'sin-imagen.jpg');
                                if (!file_exists($ruta_imagen)) {
                                    $ruta_imagen = '../images/productos/sin-imagen.jpg';
                                }
                                ?>
                                <img src="<?php echo htmlspecialchars($ruta_imagen); ?>" 
                                     class="img-thumbnail" 
                                     style="width: 60px; height: 60px; object-fit: cover;"
                                     alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                <br>
                                <small class="text-muted">
                                    <i class="bi bi-image"></i> <?php echo htmlspecialchars($producto['imagen'] ?: 'sin-imagen.jpg'); ?>
                                </small>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($producto['nombre']); ?></strong><br>
                                <small class="text-muted"><?php echo htmlspecialchars(substr($producto['descripcion'], 0, 50)); ?>...</small>
                            </td>
                            <td>$<?php echo number_format($producto['precio_kg'], 2); ?></td>
                            <td>
                                <?php 
                                $clase_stock = $producto['stock_kg'] < 10 ? 'text-danger' : 'text-success';
                                echo "<span class='$clase_stock'>" . number_format($producto['stock_kg'], 2) . " kg</span>";
                                ?>
                            </td>
                            <td>
                                <?php if ($producto['activo']): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning" 
                                        onclick="editarProducto(<?php echo htmlspecialchars(json_encode($producto)); ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="productos.php?eliminar=<?php echo $producto['id']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('¿Está seguro de eliminar este producto?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Producto -->
<div class="modal fade" id="modalAgregar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Agregar Producto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Precio por kg</label>
                            <input type="number" class="form-control" name="precio" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stock (kg)</label>
                            <input type="number" class="form-control" name="stock" step="0.1" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre de Imagen</label>
                        <input type="text" class="form-control" name="imagen" placeholder="producto-1.jpg" required>
                        <small class="text-muted">
                            <i class="bi bi-lightbulb"></i> Ejemplo: producto-9.jpg, producto-10.jpg, etc.
                            <br>La imagen debe estar en <code>/images/productos/</code>
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="agregar_producto" class="btn btn-primary">
                        <i class="bi bi-save"></i> Agregar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Producto -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Editar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" id="edit_nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" id="edit_descripcion" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Precio por kg</label>
                            <input type="number" class="form-control" name="precio" id="edit_precio" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stock (kg)</label>
                            <input type="number" class="form-control" name="stock" id="edit_stock" step="0.1" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre de Imagen</label>
                        <input type="text" class="form-control" name="imagen" id="edit_imagen" placeholder="producto-1.jpg" required>
                        <small class="text-muted">
                            <i class="bi bi-lightbulb"></i> La imagen debe existir en <code>/images/productos/</code>
                        </small>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="activo" id="edit_activo">
                        <label class="form-check-label" for="edit_activo">Producto activo en tienda</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="actualizar_producto" class="btn btn-warning">
                        <i class="bi bi-save"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editarProducto(producto) {
    document.getElementById('edit_id').value = producto.id;
    document.getElementById('edit_nombre').value = producto.nombre;
    document.getElementById('edit_descripcion').value = producto.descripcion;
    document.getElementById('edit_precio').value = producto.precio_kg;
    document.getElementById('edit_stock').value = producto.stock_kg;
    document.getElementById('edit_imagen').value = producto.imagen;
    document.getElementById('edit_activo').checked = producto.activo == 1;
    
    const modal = new bootstrap.Modal(document.getElementById('modalEditar'));
    modal.show();
}
</script>

<?php
$conn->close();
include '../includes/footer.php';
?>
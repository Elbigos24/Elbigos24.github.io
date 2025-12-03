<?php
require_once 'config/database.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Agregar al carrito
if (isset($_POST['agregar_carrito'])) {
    $producto_id = intval($_POST['producto_id']);
    $cantidad = floatval($_POST['cantidad']);
    
    if ($cantidad > 0) {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = array();
        }
        
        if (isset($_SESSION['carrito'][$producto_id])) {
            $_SESSION['carrito'][$producto_id] += $cantidad;
        } else {
            $_SESSION['carrito'][$producto_id] = $cantidad;
        }
        
        $_SESSION['mensaje'] = 'Producto agregado al carrito';
        $_SESSION['mensaje_tipo'] = 'success';
        header('Location: tienda.php');
        exit();
    }
}

// Obtener productos activos
$conn = getConnection();
$sql = "SELECT * FROM productos WHERE activo = TRUE ORDER BY nombre";
$result = $conn->query($sql);

$page_title = 'Tienda';
$css_path = 'css/styles.css';
include 'includes/header.php';
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h1 class="display-4"><i class="bi bi-shop"></i> Nuestra Tienda de Mariscos</h1>
        <p class="lead text-muted">Productos frescos del mar, directo a tu mesa</p>
    </div>
    
    <div class="row g-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($producto = $result->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card producto-card shadow-sm">
                        <div class="position-relative">
                            <?php 
                            // Ruta de la imagen
                            $ruta_imagen = 'images/productos/' . ($producto['imagen'] ?: 'sin-imagen.jpg');
                            
                            // Verificar si el archivo existe
                            if (!file_exists($ruta_imagen)) {
                                // Usar imagen temporal de internet como fallback
                                $imagenes_temporales = [
                                    'producto-1.jpg' => 'https://images.unsplash.com/photo-1565680018434-b513d5e5fd47?w=400&h=300&fit=crop',
                                    'producto-2.jpg' => 'https://images.unsplash.com/photo-1599084993091-1cb5c0721cc6?w=400&h=300&fit=crop',
                                    'producto-3.jpg' => 'https://images.unsplash.com/photo-1544943110-0773bf672f3b?w=400&h=300&fit=crop',
                                    'producto-4.jpg' => 'https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?w=400&h=300&fit=crop',
                                    'producto-5.jpg' => 'https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?w=400&h=300&fit=crop',
                                    'producto-6.jpg' => 'https://images.unsplash.com/photo-1633128052568-e704da0ee5b6?w=400&h=300&fit=crop',
                                    'producto-7.jpg' => 'https://images.unsplash.com/photo-1580959375944-0b6a59d67b7f?w=400&h=300&fit=crop',
                                    'producto-8.jpg' => 'https://images.unsplash.com/photo-1606425271394-c3ca9aa1771c?w=400&h=300&fit=crop',
                                ];
                                
                                $ruta_imagen = $imagenes_temporales[$producto['imagen']] ?? 'https://via.placeholder.com/400x300/0077be/ffffff?text=Sin+Imagen';
                            }
                            ?>
                            
                            <img src="<?php echo htmlspecialchars($ruta_imagen); ?>" 
                                 class="card-img-top producto-imagen" 
                                 alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                 title="Imagen: <?php echo htmlspecialchars($producto['imagen']); ?>"
                                 onerror="this.src='https://via.placeholder.com/400x300/0077be/ffffff?text=Error+Cargando+Imagen'">
                            
                            <!-- Identificador de imagen (solo visible para admins) -->
                            <?php if (isset($_SESSION['es_admin']) && $_SESSION['es_admin']): ?>
                                <span class="position-absolute bottom-0 start-0 badge bg-dark m-2 opacity-75">
                                    <i class="bi bi-image"></i> <?php echo htmlspecialchars($producto['imagen']); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($producto['stock_kg'] < 10): ?>
                                <span class="position-absolute top-0 end-0 badge bg-warning m-2">
                                    <i class="bi bi-exclamation-triangle"></i> Poco Stock
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="producto-precio">$<?php echo number_format($producto['precio_kg'], 2); ?>/kg</span>
                                <span class="producto-stock">
                                    <i class="bi bi-box-seam"></i> <?php echo number_format($producto['stock_kg'], 2); ?> kg
                                </span>
                            </div>
                            
                            <?php if ($producto['stock_kg'] > 0): ?>
                                <form method="POST" action="" class="d-flex gap-2">
                                    <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                    <input type="number" name="cantidad" class="form-control cantidad-input" 
                                           value="0.5" min="0.1" step="0.1" max="<?php echo $producto['stock_kg']; ?>">
                                    <button type="submit" name="agregar_carrito" class="btn btn-custom flex-grow-1">
                                        <i class="bi bi-cart-plus"></i> Agregar
                                    </button>
                                </form>
                            <?php else: ?>
                                <button class="btn btn-secondary w-100" disabled>
                                    <i class="bi bi-x-circle"></i> Sin Stock
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> No hay productos disponibles en este momento.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.producto-imagen {
    width: 100%;
    height: 250px;
    object-fit: cover;
    object-position: center;
    background-color: #f0f0f0;
}
</style>

<?php
$conn->close();
include 'includes/footer.php';
?>
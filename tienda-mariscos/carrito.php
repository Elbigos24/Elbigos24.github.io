<?php
require_once 'config/database.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Actualizar cantidad
if (isset($_POST['actualizar_cantidad'])) {
    $producto_id = intval($_POST['producto_id']);
    $nueva_cantidad = floatval($_POST['cantidad']);
    
    if ($nueva_cantidad > 0) {
        $_SESSION['carrito'][$producto_id] = $nueva_cantidad;
    } else {
        unset($_SESSION['carrito'][$producto_id]);
    }
    
    header('Location: carrito.php');
    exit();
}

// Eliminar del carrito
if (isset($_GET['eliminar'])) {
    $producto_id = intval($_GET['eliminar']);
    unset($_SESSION['carrito'][$producto_id]);
    
    $_SESSION['mensaje'] = 'Producto eliminado del carrito';
    $_SESSION['mensaje_tipo'] = 'info';
    header('Location: carrito.php');
    exit();
}

// Vaciar carrito
if (isset($_GET['vaciar'])) {
    $_SESSION['carrito'] = array();
    $_SESSION['mensaje'] = 'Carrito vaciado';
    $_SESSION['mensaje_tipo'] = 'info';
    header('Location: carrito.php');
    exit();
}

$conn = getConnection();
$carrito_items = array();
$total = 0;

if (isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0) {
    $ids = array_keys($_SESSION['carrito']);
    $ids_str = implode(',', array_map('intval', $ids));
    
    $sql = "SELECT * FROM productos WHERE id IN ($ids_str)";
    $result = $conn->query($sql);
    
    while ($producto = $result->fetch_assoc()) {
        $cantidad = $_SESSION['carrito'][$producto['id']];
        $subtotal = $cantidad * $producto['precio_kg'];
        
        $carrito_items[] = array(
            'producto' => $producto,
            'cantidad' => $cantidad,
            'subtotal' => $subtotal
        );
        
        $total += $subtotal;
    }
}

$page_title = 'Carrito de Compras';
$css_path = 'css/styles.css';
include 'includes/header.php';
?>

<div class="container my-5">
    <h1 class="mb-4"><i class="bi bi-cart3"></i> Carrito de Compras</h1>
    
    <?php if (count($carrito_items) > 0): ?>
        <div class="row">
            <div class="col-lg-8">
                <?php foreach ($carrito_items as $item): ?>
                    <div class="carrito-item">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <?php 
                                // Ruta de la imagen
                                $ruta_imagen = 'images/productos/' . ($item['producto']['imagen'] ?: 'sin-imagen.jpg');
                                if (!file_exists($ruta_imagen)) {
                                    // Usar imágenes temporales de internet
                                    $imagenes_temporales = [
                                        'producto-1.jpg' => 'https://images.unsplash.com/photo-1565680018434-b513d5e5fd47?w=100&h=100&fit=crop',
                                        'producto-2.jpg' => 'https://images.unsplash.com/photo-1599084993091-1cb5c0721cc6?w=100&h=100&fit=crop',
                                        'producto-3.jpg' => 'https://images.unsplash.com/photo-1544943110-0773bf672f3b?w=100&h=100&fit=crop',
                                        'producto-4.jpg' => 'https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?w=100&h=100&fit=crop',
                                        'producto-5.jpg' => 'https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?w=100&h=100&fit=crop',
                                        'producto-6.jpg' => 'https://images.unsplash.com/photo-1633128052568-e704da0ee5b6?w=100&h=100&fit=crop',
                                        'producto-7.jpg' => 'https://images.unsplash.com/photo-1580959375944-0b6a59d67b7f?w=100&h=100&fit=crop',
                                        'producto-8.jpg' => 'https://images.unsplash.com/photo-1606425271394-c3ca9aa1771c?w=100&h=100&fit=crop',
                                    ];
                                    $ruta_imagen = $imagenes_temporales[$item['producto']['imagen']] ?? 'https://via.placeholder.com/100x100/0077be/ffffff?text=Sin+Imagen';
                                }
                                ?>
                                <img src="<?php echo htmlspecialchars($ruta_imagen); ?>" 
                                     class="img-fluid rounded carrito-imagen" 
                                     alt="<?php echo htmlspecialchars($item['producto']['nombre']); ?>"
                                     onerror="this.src='https://via.placeholder.com/100x100/0077be/ffffff?text=Error'">
                            </div>
                            <div class="col-md-3">
                                <h5><?php echo htmlspecialchars($item['producto']['nombre']); ?></h5>
                                <small class="text-muted">$<?php echo number_format($item['producto']['precio_kg'], 2); ?> por kg</small>
                            </div>
                            <div class="col-md-3">
                                <form method="POST" action="" class="d-flex gap-2">
                                    <input type="hidden" name="producto_id" value="<?php echo $item['producto']['id']; ?>">
                                    <input type="number" name="cantidad" class="form-control cantidad-input" 
                                           value="<?php echo $item['cantidad']; ?>" min="0.1" step="0.1" 
                                           max="<?php echo $item['producto']['stock_kg']; ?>">
                                    <button type="submit" name="actualizar_cantidad" class="btn btn-sm btn-custom">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-2 text-end">
                                <h5 class="text-primary">$<?php echo number_format($item['subtotal'], 2); ?></h5>
                            </div>
                            <div class="col-md-2 text-end">
                                <a href="carrito.php?eliminar=<?php echo $item['producto']['id']; ?>" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('¿Eliminar este producto?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="mt-3">
                    <a href="carrito.php?vaciar=1" class="btn btn-outline-secondary" 
                       onclick="return confirm('¿Vaciar todo el carrito?')">
                        <i class="bi bi-trash"></i> Vaciar Carrito
                    </a>
                    <a href="tienda.php" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Seguir Comprando
                    </a>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Resumen del Pedido</h4>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($total, 2); ?></span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>IVA (16%):</span>
                            <span>$<?php echo number_format($total * 0.16, 2); ?></span>
                        </div>
                        
                        <hr>
                        
                        <div class="carrito-total d-flex justify-content-between">
                            <span>Total:</span>
                            <span>$<?php echo number_format($total * 1.16, 2); ?></span>
                        </div>
                        
                        <form method="POST" action="procesar_compra.php" class="mt-4">
                            <button type="submit" class="btn btn-custom w-100 btn-lg">
                                <i class="bi bi-credit-card"></i> Procesar Compra
                            </button>
                        </form>
                        
                        <div class="alert alert-info mt-3 mb-0">
                            <small>
                                <i class="bi bi-info-circle"></i> 
                                Los precios incluyen IVA
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <h4><i class="bi bi-cart-x"></i> Tu carrito está vacío</h4>
            <p>Agrega productos desde la tienda</p>
            <a href="tienda.php" class="btn btn-custom">
                <i class="bi bi-shop"></i> Ir a la Tienda
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
.carrito-imagen {
    width: 100%;
    height: 100px;
    object-fit: cover;
    object-position: center;
}
</style>

<?php
$conn->close();
include 'includes/footer.php';
?>
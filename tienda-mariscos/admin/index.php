<?php
require_once '../config/database.php';

if (!isset($_SESSION['usuario_id']) || !$_SESSION['es_admin']) {
    header('Location: ../login.php');
    exit();
}

$conn = getConnection();

// Estadísticas
$stats = array();

// Total usuarios
$result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
$stats['usuarios'] = $result->fetch_assoc()['total'];

// Total productos
$result = $conn->query("SELECT COUNT(*) as total FROM productos WHERE activo = TRUE");
$stats['productos'] = $result->fetch_assoc()['total'];

// Total pedidos
$result = $conn->query("SELECT COUNT(*) as total FROM pedidos");
$stats['pedidos'] = $result->fetch_assoc()['total'];

// Total ventas
$result = $conn->query("SELECT COALESCE(SUM(total), 0) as total FROM pedidos");
$stats['ventas'] = $result->fetch_assoc()['total'];

// Últimos pedidos
$sql = "SELECT p.*, u.nombre as usuario_nombre 
        FROM pedidos p 
        JOIN usuarios u ON p.usuario_id = u.id 
        ORDER BY p.fecha_pedido DESC 
        LIMIT 10";
$ultimos_pedidos = $conn->query($sql);

// Productos con bajo stock
$sql = "SELECT * FROM productos WHERE stock_kg < 10 AND activo = TRUE ORDER BY stock_kg ASC LIMIT 5";
$bajo_stock = $conn->query($sql);

$page_title = 'Dashboard Admin';
$css_path = '../css/styles.css';
$is_admin = true;
include '../includes/header.php';
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-speedometer2"></i> Dashboard Administrativo</h1>
        <a href="../tienda.php" class="btn btn-outline-primary">
            <i class="bi bi-shop"></i> Ver Tienda
        </a>
    </div>
    
    <!-- Estadísticas -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card dashboard-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Usuarios</h6>
                            <h2 class="mb-0"><?php echo $stats['usuarios']; ?></h2>
                        </div>
                        <i class="bi bi-people stat-icon text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card dashboard-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Productos</h6>
                            <h2 class="mb-0"><?php echo $stats['productos']; ?></h2>
                        </div>
                        <i class="bi bi-box-seam stat-icon text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card dashboard-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Pedidos</h6>
                            <h2 class="mb-0"><?php echo $stats['pedidos']; ?></h2>
                        </div>
                        <i class="bi bi-cart stat-icon text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card dashboard-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Ventas Totales</h6>
                            <h2 class="mb-0">$<?php echo number_format($stats['ventas'], 2); ?></h2>
                        </div>
                        <i class="bi bi-currency-dollar stat-icon text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Menú de administración -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <a href="productos.php" class="text-decoration-none">
                <div class="card text-center h-100 shadow-sm hover-card">
                    <div class="card-body">
                        <i class="bi bi-box-seam display-1 text-primary"></i>
                        <h4 class="mt-3">Gestionar Productos</h4>
                        <p class="text-muted">Agregar, editar y eliminar productos</p>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-md-4">
            <a href="usuarios.php" class="text-decoration-none">
                <div class="card text-center h-100 shadow-sm hover-card">
                    <div class="card-body">
                        <i class="bi bi-people display-1 text-success"></i>
                        <h4 class="mt-3">Gestionar Usuarios</h4>
                        <p class="text-muted">Administrar usuarios y permisos</p>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-md-4">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-graph-up display-1 text-warning"></i>
                    <h4 class="mt-3">Reportes</h4>
                    <p class="text-muted">Próximamente</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Últimos pedidos -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-cart-check"></i> Últimos Pedidos</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($pedido = $ultimos_pedidos->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $pedido['id']; ?></td>
                                    <td><?php echo htmlspecialchars($pedido['usuario_nombre']); ?></td>
                                    <td>$<?php echo number_format($pedido['total'], 2); ?></td>
                                    <td>
                                        <span class="badge bg-warning">
                                            <?php echo ucfirst($pedido['estado']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Productos con bajo stock -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Bajo Stock</h5>
                </div>
                <div class="card-body">
                    <?php if ($bajo_stock->num_rows > 0): ?>
                        <ul class="list-group list-group-flush">
                            <?php while ($producto = $bajo_stock->fetch_assoc()): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo htmlspecialchars($producto['nombre']); ?>
                                <span class="badge bg-danger rounded-pill">
                                    <?php echo number_format($producto['stock_kg'], 2); ?> kg
                                </span>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted mb-0">No hay productos con bajo stock</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-card {
    transition: all 0.3s ease;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
}
</style>

<?php
$conn->close();
include '../includes/footer.php';
?>
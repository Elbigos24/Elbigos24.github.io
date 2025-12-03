<?php
require_once 'config/database.php';

// Si el usuario ya está logueado, redirigir a la tienda
if (isset($_SESSION['usuario_id'])) {
    header('Location: tienda.php');
    exit();
}

$page_title = 'Bienvenido';
$css_path = 'css/styles.css';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Mariscos del Mar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo $css_path; ?>">
    <style>
        .hero-section {
            background: linear-gradient(135deg, rgba(0,119,190,0.9), rgba(0,168,232,0.9)), 
                        url('https://images.unsplash.com/photo-1559339352-11d035aa65de?w=1200') center/cover;
            min-height: 600px;
            color: white;
            display: flex;
            align-items: center;
        }
        .feature-card {
            transition: transform 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-10px);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-water"></i> Mariscos del Mar
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="login.php">
                        <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="register.php">
                        <i class="bi bi-person-plus"></i> Registrarse
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container text-center">
        <h1 class="display-3 fw-bold mb-4">Mariscos Frescos del Mar</h1>
        <p class="lead mb-5">Los mejores productos del mar, directo a tu mesa. Calidad garantizada y entregas rápidas.</p>
        <div>
            <a href="register.php" class="btn btn-light btn-lg me-3">
                <i class="bi bi-person-plus"></i> Crear Cuenta
            </a>
            <a href="login.php" class="btn btn-outline-light btn-lg">
                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
            </a>
        </div>
    </div>
</section>

<!-- Características -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">¿Por qué elegirnos?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card feature-card border-0 shadow-sm text-center h-100">
                    <div class="card-body p-4">
                        <i class="bi bi-check-circle-fill text-success display-3 mb-3"></i>
                        <h4>Productos Frescos</h4>
                        <p class="text-muted">Mariscos capturados diariamente y conservados con los más altos estándares de calidad.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card border-0 shadow-sm text-center h-100">
                    <div class="card-body p-4">
                        <i class="bi bi-truck text-primary display-3 mb-3"></i>
                        <h4>Entrega Rápida</h4>
                        <p class="text-muted">Envíos express para mantener la frescura. Recibe tus productos en tiempo récord.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card border-0 shadow-sm text-center h-100">
                    <div class="card-body p-4">
                        <i class="bi bi-shield-check text-warning display-3 mb-3"></i>
                        <h4>Calidad Garantizada</h4>
                        <p class="text-muted">100% de satisfacción garantizada. Si no estás satisfecho, te devolvemos tu dinero.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Productos Destacados -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Nuestros Productos</h2>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-water display-1 text-primary"></i>
                        <h5 class="mt-3">Camarones</h5>
                        <p class="text-muted small">Frescos del Pacífico</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-water display-1 text-success"></i>
                        <h5 class="mt-3">Pulpo</h5>
                        <p class="text-muted small">Calidad premium</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-water display-1 text-info"></i>
                        <h5 class="mt-3">Pescado</h5>
                        <p class="text-muted small">Filetes frescos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-water display-1 text-warning"></i>
                        <h5 class="mt-3">Almejas</h5>
                        <p class="text-muted small">Del día</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-5">
            <a href="register.php" class="btn btn-custom btn-lg">
                <i class="bi bi-cart-plus"></i> Empieza a Comprar
            </a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer-custom">
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0">
                    <i class="bi bi-water"></i> &copy; <?php echo date('Y'); ?> Mariscos del Mar - Todos los derechos reservados
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="#" class="text-white text-decoration-none me-3">
                    <i class="bi bi-facebook"></i>
                </a>
                <a href="#" class="text-white text-decoration-none me-3">
                    <i class="bi bi-instagram"></i>
                </a>
                <a href="#" class="text-white text-decoration-none">
                    <i class="bi bi-whatsapp"></i>
                </a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
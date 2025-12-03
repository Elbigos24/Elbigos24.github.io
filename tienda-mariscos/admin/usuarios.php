<?php
require_once '../config/database.php';

if (!isset($_SESSION['usuario_id']) || !$_SESSION['es_admin']) {
    header('Location: ../login.php');
    exit();
}

$conn = getConnection();

// Cambiar rol de administrador
if (isset($_POST['cambiar_admin'])) {
    $id = intval($_POST['usuario_id']);
    $es_admin = intval($_POST['es_admin']);
    
    // No permitir cambiar propio rol
    if ($id != $_SESSION['usuario_id']) {
        $stmt = $conn->prepare("UPDATE usuarios SET es_admin = ? WHERE id = ?");
        $stmt->bind_param("ii", $es_admin, $id);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = 'Rol actualizado exitosamente';
            $_SESSION['mensaje_tipo'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al actualizar rol';
            $_SESSION['mensaje_tipo'] = 'danger';
        }
        
        $stmt->close();
    } else {
        $_SESSION['mensaje'] = 'No puedes cambiar tu propio rol';
        $_SESSION['mensaje_tipo'] = 'warning';
    }
    
    header('Location: usuarios.php');
    exit();
}

// Eliminar usuario
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    
    // No permitir eliminar propio usuario
    if ($id != $_SESSION['usuario_id']) {
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = 'Usuario eliminado exitosamente';
            $_SESSION['mensaje_tipo'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al eliminar usuario. Puede que tenga pedidos asociados.';
            $_SESSION['mensaje_tipo'] = 'danger';
        }
        
        $stmt->close();
    } else {
        $_SESSION['mensaje'] = 'No puedes eliminar tu propia cuenta';
        $_SESSION['mensaje_tipo'] = 'warning';
    }
    
    header('Location: usuarios.php');
    exit();
}

// Obtener todos los usuarios con estadísticas
$sql = "SELECT u.*, 
        COUNT(DISTINCT p.id) as total_pedidos,
        COALESCE(SUM(p.total), 0) as total_gastado
        FROM usuarios u
        LEFT JOIN pedidos p ON u.id = p.usuario_id
        GROUP BY u.id
        ORDER BY u.fecha_registro DESC";
$usuarios = $conn->query($sql);

$page_title = 'Gestión de Usuarios';
$css_path = '../css/styles.css';
$is_admin = true;
include '../includes/header.php';
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-people"></i> Gestión de Usuarios</h1>
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Dashboard
        </a>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Pedidos</th>
                            <th>Total Gastado</th>
                            <th>Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $usuario['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($usuario['nombre']); ?></strong>
                                <?php if ($usuario['id'] == $_SESSION['usuario_id']): ?>
                                    <span class="badge bg-info">Tú</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td>
                                <?php if ($usuario['es_admin']): ?>
                                    <span class="badge bg-danger">
                                        <i class="bi bi-shield-fill-check"></i> Admin
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-person"></i> Usuario
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-primary"><?php echo $usuario['total_pedidos']; ?></span>
                            </td>
                            <td>
                                <strong class="text-success">$<?php echo number_format($usuario['total_gastado'], 2); ?></strong>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></td>
                            <td>
                                <?php if ($usuario['id'] != $_SESSION['usuario_id']): ?>
                                    <!-- Botón cambiar rol -->
                                    <form method="POST" action="" class="d-inline">
                                        <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">
                                        <input type="hidden" name="es_admin" value="<?php echo $usuario['es_admin'] ? 0 : 1; ?>">
                                        <button type="submit" name="cambiar_admin" class="btn btn-sm btn-warning" 
                                                title="<?php echo $usuario['es_admin'] ? 'Quitar admin' : 'Hacer admin'; ?>">
                                            <i class="bi bi-shield-<?php echo $usuario['es_admin'] ? 'slash' : 'check'; ?>"></i>
                                        </button>
                                    </form>
                                    
                                    <!-- Botón eliminar -->
                                    <a href="usuarios.php?eliminar=<?php echo $usuario['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('¿Está seguro de eliminar este usuario?\n\nUsuario: <?php echo htmlspecialchars($usuario['nombre']); ?>\nEmail: <?php echo htmlspecialchars($usuario['email']); ?>')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">Tu cuenta</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Información adicional -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card border-left-primary shadow-sm">
                <div class="card-body">
                    <div class="text-primary mb-2">
                        <i class="bi bi-people-fill"></i> Total de Usuarios
                    </div>
                    <h3><?php echo $usuarios->num_rows; ?></h3>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-left-danger shadow-sm">
                <div class="card-body">
                    <div class="text-danger mb-2">
                        <i class="bi bi-shield-fill-check"></i> Administradores
                    </div>
                    <h3>
                        <?php
                        mysqli_data_seek($usuarios, 0);
                        $admin_count = 0;
                        while ($u = $usuarios->fetch_assoc()) {
                            if ($u['es_admin']) $admin_count++;
                        }
                        echo $admin_count;
                        ?>
                    </h3>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-left-success shadow-sm">
                <div class="card-body">
                    <div class="text-success mb-2">
                        <i class="bi bi-person"></i> Usuarios Regulares
                    </div>
                    <h3>
                        <?php
                        mysqli_data_seek($usuarios, 0);
                        $regular_count = 0;
                        while ($u = $usuarios->fetch_assoc()) {
                            if (!$u['es_admin']) $regular_count++;
                        }
                        echo $regular_count;
                        ?>
                    </h3>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Alertas de información -->
    <div class="alert alert-info mt-4">
        <h5><i class="bi bi-info-circle"></i> Información sobre la gestión de usuarios</h5>
        <ul class="mb-0">
            <li>Los administradores pueden gestionar productos, usuarios y ver el dashboard completo</li>
            <li>No puedes cambiar tu propio rol ni eliminar tu propia cuenta</li>
            <li>Al eliminar un usuario, se eliminarán también sus pedidos asociados</li>
            <li>El icono <i class="bi bi-shield-check"></i> convierte un usuario en administrador</li>
            <li>El icono <i class="bi bi-shield-slash"></i> quita privilegios de administrador</li>
        </ul>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 4px solid #0077be;
}
.border-left-danger {
    border-left: 4px solid #dc3545;
}
.border-left-success {
    border-left: 4px solid #28a745;
}
</style>

<?php
$conn->close();
include '../includes/footer.php';
?>
<?php
require_once 'config/database.php';

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['carrito']) || count($_SESSION['carrito']) === 0) {
    header('Location: tienda.php');
    exit();
}

$conn = getConnection();
$conn->begin_transaction();

try {
    $usuario_id = $_SESSION['usuario_id'];
    $ids = array_keys($_SESSION['carrito']);
    $ids_str = implode(',', array_map('intval', $ids));
    
    // Obtener productos del carrito
    $sql = "SELECT * FROM productos WHERE id IN ($ids_str) FOR UPDATE";
    $result = $conn->query($sql);
    
    $total = 0;
    $items_pedido = array();
    
    // Verificar stock y calcular total
    while ($producto = $result->fetch_assoc()) {
        $cantidad = $_SESSION['carrito'][$producto['id']];
        
        if ($producto['stock_kg'] < $cantidad) {
            throw new Exception("No hay suficiente stock de " . $producto['nombre']);
        }
        
        $subtotal = $cantidad * $producto['precio_kg'];
        $total += $subtotal;
        
        $items_pedido[] = array(
            'producto_id' => $producto['id'],
            'cantidad' => $cantidad,
            'precio_unitario' => $producto['precio_kg'],
            'subtotal' => $subtotal
        );
    }
    
    // Aplicar IVA
    $total_con_iva = $total * 1.16;
    
    // Crear pedido
    $stmt = $conn->prepare("INSERT INTO pedidos (usuario_id, total) VALUES (?, ?)");
    $stmt->bind_param("id", $usuario_id, $total_con_iva);
    $stmt->execute();
    $pedido_id = $conn->insert_id;
    $stmt->close();
    
    // Insertar detalles del pedido y actualizar stock
    $stmt_detalle = $conn->prepare("INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad_kg, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmt_stock = $conn->prepare("UPDATE productos SET stock_kg = stock_kg - ? WHERE id = ?");
    
    foreach ($items_pedido as $item) {
        $stmt_detalle->bind_param("iiddd", 
            $pedido_id, 
            $item['producto_id'], 
            $item['cantidad'], 
            $item['precio_unitario'], 
            $item['subtotal']
        );
        $stmt_detalle->execute();
        
        $stmt_stock->bind_param("di", $item['cantidad'], $item['producto_id']);
        $stmt_stock->execute();
    }
    
    $stmt_detalle->close();
    $stmt_stock->close();
    
    // Confirmar transacción
    $conn->commit();
    
    // Limpiar carrito
    $_SESSION['carrito'] = array();
    
    $_SESSION['mensaje'] = '¡Compra realizada con éxito! Pedido #' . $pedido_id;
    $_SESSION['mensaje_tipo'] = 'success';
    
    header('Location: tienda.php');
    exit();
    
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['mensaje'] = 'Error al procesar la compra: ' . $e->getMessage();
    $_SESSION['mensaje_tipo'] = 'danger';
    header('Location: carrito.php');
    exit();
}

$conn->close();
?>
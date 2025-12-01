<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Carrito</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
     <?php include "./menu.php"?>
     <div class="container mt-4">

    <h2 class="text-center mb-4">🛒 Carrito de Mariscos</h2>

    <div class="row">
        <!-- Lista del carrito -->
        <div class="col-md-8">
            <table class="table table-striped" id="cart-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="cart-body">
                    <!-- Aquí se agregan los productos -->
                </tbody>
            </table>
        </div>

        <!-- Resumen -->
        <div class="col-md-4">
            <div class="card p-3">
                <h4>Total de compra</h4>
                <h2 id="cart-total">$0</h2>
                <button class="btn btn-success w-100 mt-3">
                    Finalizar compra
                </button>
            </div>
        </div>
    </div>

</div>

<script src="./js/cart.js"></script>

</body>
</html>
<?php session_start();//Habilitar variables de secion
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Marisco</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/login.css">

</head>
<body>

    <div class="container-fluid login-container">
        <div class="row h-100">

            <!-- Columna Izquierda -->
            <div class="col-md-8 left-side d-none d-md-block">
            </div>

            <!-- Columna Derecha -->
            <div class="col-md-4 d-flex justify-content-center align-items-center">
                 <div class="login-box">
                    <div class="flex justify-content-center w-100">
                        <div><img src="./images/logo.png" alt="" width="200px"></div>
                    </div>
                    <h2 class="fw-bold mb-4">Iniciar Sesión</h2>

                    <form action="./php/auth.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input name="txtEmail" type="email" class="form-control" placeholder="ejemplo@correo.com">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input name="txtPassword" type="password" class="form-control" placeholder="•••••••••">
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <input type="checkbox" id="remember">
                                <label for="remember">Recordarme</label>
                            </div>
                            <a href="#" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
                        </div>
                        <?php if(isset($_SESSION['error'])){ ?>
                        
                                <div class="alert alert-danger">
                                    <b>Error:<?php echo $_SESSION['error'] ?> </b>
                                

                        <?php unset($_SESSION['error']); }?>
                        <button type="submit" class="btn btn-primary w-100">Entrar</button>
                    </form>

                    <div class="text-center mt-4">
                        <small>© 2025 Mariscos • Todos los derechos reservados</small>
                    </div>

                </div>
            </div>

        </div>
    </div>

</body>
</html>

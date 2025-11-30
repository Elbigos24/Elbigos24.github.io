<?php session_start();//Habilitar variables de secion
    include "./conexion.php";
    //validar
    if(isset($_POST['txtEmail'])&& isset($_POST['txtPassword'])){
        $email = $_POST['txtEmail'];
        $password = sha1($_POST['txtPassword']);
        $sql = "SELECT * FROM usser WHERE email='$email' AND password='$password'";
       // echo $sql;
       $res= $con->query($sql) or die($con->error); //ejecuta la consukta
       if(mysqli_num_rows($res)>0){//cuenta el numero de filas de el resultado
        
        //leer filas funciona cuando solo con una fila
        $fila=mysqli_fetch_array($res);
        //echo sha1($password);
        //emviar al dashboard
        header("Location: ../dash.php");
       
        echo "login exitoso, Bienvenido ".$fila['name'];
        $_SESSION['user_data']=[
            "id"=>$fila[0],
            "name"=>$fila[1],
            "email"=>$fila[2]
        ];
       }else{
        echo "usuario o contraseña incorrecta";
        $_SESSION['error']="usuario o contraseña incorrecta";
        //header("Location: ../login.php");
        echo "txtpassword: ".$password;
       }
    }else{
        echo "favor de llenar todos los datos";
        $_SESSION['error']="favor de llenar todos los datos";
        header("Location: ../login.php");
    }
        


?>
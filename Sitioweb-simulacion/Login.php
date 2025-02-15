<?php
  session_start();

  if (isset($_SESSION['user_id'])) {
    header('Location: index1.php');
    exit();
  }
  require 'database.php';

  if (!empty($_POST['email']) && !empty($_POST['password'])) {
    $records = $conn->prepare('SELECT id, email, password FROM users WHERE email = :email');
    $records->bindParam(':email', $_POST['email']);
    $records->execute();
    $results = $records->fetch(PDO::FETCH_ASSOC);

    $message = '';

    if (!empty($results) && password_verify($_POST['password'], $results['password'])) {
      $_SESSION['user_id'] = $results['id'];
      header("Location: indexAdmin.php");
      exit();
    } else {
      $message = 'Disculpa, los datos no son correctos.';
    }
  }
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/x-icon" href="/assets/logo-vt.svg" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LOGIN / APASVIAN</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx"
      crossorigin="anonymous"
    />
  </head>
  <body class="bg-info d-flex justify-content-center align-items-center vh-100">
    <div
      class="bg-white p-5 rounded-5 text-secondary shadow"
      style="width: 25rem"
    >
      <a href="index1.php" class="btn btn-secondary btn-sm mb-3">Volver a inicio</a>
      <div class="text-center fs-1 fw-bold">Login</div>

      <form id="formulario" class="login" action="login.php" method="POST">
        <div class="input-group mt-4">
          <input
            id="email"
            name="email"
            class="form-control bg-light"
            type="text"
            placeholder="Email"
            required
          />
        </div>
        <div class="input-group mt-1">
          <input
            id="password"
            name="password"
            class="form-control bg-light"
            type="password"
            placeholder="Password"
            required
          />
        </div>
        <div class="form-group mb-4">
          <input type="checkbox" id="vercon" name="vercon" aria-label="Mostrar contraseña">
          <label for="vercon">Mostrar contraseña</label>
        </div>
        <div class="d-flex justify-content-around mt-1">
        </div>
        <button class="btn btn-primary btn-lg w-100 d-flex justify-content-center align-items-center" type="submit">
          <span class="me-2">Inicia Sesión</span>
          <i class="fas fa-chevron-right"></i>
        </button>

      </form>
      
      
  </body>
   <!--Script para validar el login-->
 <script src="assets/js/login_validation.js"></script>
</html>

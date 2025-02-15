<?php
require 'database.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$query = "SELECT id, username FROM users WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $userId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$username = $user ? $user['username'] : "Usuario Desconocido";

// Consulta para obtener los datos de la tabla accesos
$query = "SELECT id, tarjeta, fecha, hora, accion FROM accesos";
$stmt = $conn->prepare($query);
$stmt->execute();
$accesos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="description" content="Miminium Admin Template v.1">
  <meta name="author" content="Isna Nur Azis">
  <meta name="keyword" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>HOME / APASVIAN</title>

  <!-- start: Css -->
  <link rel="stylesheet" type="text/css" href="asset/css/bootstrap.min.css">

  <!-- plugins -->
  <link rel="stylesheet" type="text/css" href="asset/css/plugins/font-awesome.min.css"/>
  <link rel="stylesheet" type="text/css" href="asset/css/plugins/datatables.bootstrap.min.css"/>
  <link rel="stylesheet" type="text/css" href="asset/css/plugins/animate.min.css"/>
  <link href="asset/css/style.css" rel="stylesheet">
  <!-- end: Css -->

  <link rel="shortcut icon" href="asset/img/logomi.png">
</head>

<body id="mimin" class="dashboard">
  <!-- start: Header -->
  <nav class="navbar navbar-default header navbar-fixed-top">
    <div class="col-md-12 nav-wrapper">
      <div class="navbar-header" style="width:100%;">
        <div class="opener-left-menu is-open">
          <span class="top"></span>
          <span class="middle"></span>
          <span class="bottom"></span>
        </div>
        <a href="indexAdmin.php" class="navbar-brand">
          <b>APASVIAN</b>
        </a>

        <ul class="nav navbar-nav navbar-right user-nav">
          <li class="user-name"><span><?php echo $username; ?></span></li>
          <li class="dropdown avatar-dropdown">
            <img src="asset/img/avatar.jpg" class="img-circle avatar" alt="user name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"/>
            <ul class="dropdown-menu user-dropdown">
              <li class="more">
                <ul>
                  <li><a href="logout.php"><span class="fa fa-power-off "></span> Cerrar Sesión</a></li>
                </ul>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <!-- end: Header -->

  <div class="container-fluid mimin-wrapper">
    <!-- start: Left Menu -->
    <div id="left-menu">
      <div class="sub-left-menu scroll">
        <ul class="nav nav-list">
          <li><div class="left-bg"></div></li>
          <li class="ripple">
            <a class="tree-toggle nav-header">
              <span class="fa-area-chart fa"></span> Gráficas
              <span class="fa-angle-right fa right-arrow text-right"></span>
            </a>
            <ul class="nav nav-list tree">
              <li><a href="indexAdmin.php">Flujo de Usuarios</a></li>
            </ul>
          </li>
          <li class="ripple">
            <a class="tree-toggle nav-header">
              <span class="fa fa-table"></span> Tablas
              <span class="fa-angle-right fa right-arrow text-right"></span>
            </a>
            <ul class="nav nav-list tree">
              <li><a href="datatables.php">Tabla de datos</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
    <!-- end: Left Menu -->

    <!-- start: Content -->
    <div id="content">
      <div class="panel box-shadow-none content-header">
        <div class="panel-body">
          <div class="col-md-12">
            <h3 class="animated fadeInLeft">Tabla de datos de los Usuarios</h3>
            <p class="animated fadeInDown">
              Vista <span class="fa-angle-right fa"></span> Datos totales
            </p>
          </div>
        </div>
      </div>
      <div class="col-md-12 top-20 padding-0">
        <div class="col-md-12">
          <div class="panel">
            <div class="panel-body">
              <div class="responsive-table">
                <table id="datatables-example" class="table table-striped table-bordered" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Id</th>
                      <th>Tarjeta</th>
                      <th>Fecha</th>
                      <th>Hora</th>
                      <th>Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($accesos as $acceso): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($acceso['id']); ?></td>
                      <td><?php echo htmlspecialchars($acceso['tarjeta']); ?></td>
                      <td><?php echo htmlspecialchars($acceso['fecha']); ?></td>
                      <td><?php echo htmlspecialchars($acceso['hora']); ?></td>
                      <td><?php echo $acceso['accion'] == 1 ? 'Acceso Permitido' : 'Acceso Denegado'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- end: Content -->
  </div>

  <!-- start: Javascript -->
  <script src="asset/js/jquery.min.js"></script>
  <script src="asset/js/jquery.ui.min.js"></script>
  <script src="asset/js/bootstrap.min.js"></script>

  <!-- plugins -->
  <script src="asset/js/plugins/moment.min.js"></script>
  <script src="asset/js/plugins/jquery.datatables.min.js"></script>
  <script src="asset/js/plugins/datatables.bootstrap.min.js"></script>
  <script src="asset/js/plugins/jquery.nicescroll.js"></script>

  <!-- custom -->
  <script src="asset/js/main.js"></script>
  <script type="text/javascript">
    $(document).ready(function(){
      $('#datatables-example').DataTable();
    });
  </script>
  <!-- end: Javascript -->
</body>
</html>

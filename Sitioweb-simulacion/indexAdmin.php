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

// Consulta para la gráfica "Acceso de tarjetas"
$query = "SELECT tarjeta, COUNT(*) as count FROM accesos GROUP BY tarjeta";
$stmt = $conn->prepare($query);
$stmt->execute();
$tarjetas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Consulta para la gráfica "Accesos permitidos y denegados"
$query = "SELECT DATE(fecha) as fecha, acceso, COUNT(*) as count FROM accesos GROUP BY DATE(fecha), acceso";
$stmt = $conn->prepare($query);
$stmt->execute();
$accesos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Consulta para la gráfica "Días de acceso"
$query = "SELECT DAYNAME(fecha) as dia, accion, COUNT(*) as count FROM accesos GROUP BY DAYNAME(fecha), accion";
$stmt = $conn->prepare($query);
$stmt->execute();
$dias = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
  <link rel="stylesheet" type="text/css" href="asset/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="asset/css/plugins/font-awesome.min.css"/>
  <link rel="stylesheet" type="text/css" href="asset/css/plugins/animate.min.css"/>
  <link href="asset/css/style.css" rel="stylesheet">
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
        <a href="indexAdmin.php" class="navbar-brand"><b>APASVIAN</b></a>
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
          <li class="ripple"><a class="tree-toggle nav-header"><span class="fa fa-table"></span> Tablas <span class="fa-angle-right fa right-arrow text-right"></span> </a>
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
      <div class="col-md-12 padding-0" style="margin-top:20px;">
        <div class="col-md-12 padding-0">
          <div class="col-md-6 padding-0">
            <div class="col-md-12 padding-0">
              <div class="col-md-9">
                <div class="panel chart-title">
                  <h3><span class="fa fa-area-chart"></span> Gráficos de control de Ingreso</h3>
                </div>
              </div>
              <div class="col-md-6">
                <div class="panel">
                  <div class="panel-heading-white panel-heading text-center">
                    <h4>Acceso de tarjetas</h4>
                  </div>
                  <div class="panel-body">
                    <center>
                      <canvas class="doughnut-chart"></canvas>
                    </center>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-12">
          <div class="panel">
            <div class="panel-heading-white panel-heading">
              <h4>Días de acceso</h4>
            </div>
            <div class="panel-body">
              <div class="col-md-12">
                <canvas class="bar-chart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- end: Content -->
  </div>

  <!-- end: Content -->
  <!-- start: Javascript -->
  <script src="asset/js/jquery.min.js"></script>
  <script src="asset/js/jquery.ui.min.js"></script>
  <script src="asset/js/bootstrap.min.js"></script>
  <script src="asset/js/plugins/moment.min.js"></script>
  <script src="asset/js/plugins/chart.min.js"></script>
  <script src="asset/js/plugins/jquery.nicescroll.js"></script>
  <script src="asset/js/main.js"></script>
  <script type="text/javascript">
    (function($){
      // Datos desde PHP
      var tarjetasData = <?php echo json_encode($tarjetas); ?>;
      var accesosData = <?php echo json_encode($accesos); ?>;
      var diasData = <?php echo json_encode($dias); ?>;

      // Preparar datos para "Acceso de tarjetas"
      var doughnutData = tarjetasData.map(function(item) {
        return {
          value: item.count,
          label: item.tarjeta,
          color: getRandomColor()
        };
      });

      // Preparar datos para "Accesos permitidos y denegados"
      var permitted = new Array(7).fill(0);
      var denied = new Array(7).fill(0);
      var days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
      var daysInSpanish = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];

      accesosData.forEach(function(item) {
        var dayIndex = new Date(item.fecha).getDay();
        if (item.acceso == 1) {
          permitted[dayIndex] += item.count;
        } else if (item.acceso == 2) {
          denied[dayIndex] += item.count;
        }
      });

      // Preparar datos para "Días de acceso"
      var barData = {
        labels: daysInSpanish,
        datasets: [
          {
            label: "Acciones Permitidas",
            fillColor: "rgba(21,186,103,0.5)",
            strokeColor: "rgba(220,220,220,0.8)",
            highlightFill: "rgba(220,220,220,0.75)",
            highlightStroke: "rgba(220,220,220,1)",
            data: new Array(7).fill(0)
          },
          {
            label: "Acciones Denegadas",
            fillColor: "rgba(255,0,0,0.5)",
            strokeColor: "rgba(255,0,0,0.8)",
            highlightFill: "rgba(255,0,0,0.75)",
            highlightStroke: "rgba(255,0,0,1)",
            data: new Array(7).fill(0)
          }
        ]
      };

      diasData.forEach(function(item) {
        var dayIndex = days.indexOf(item.dia);
        if (dayIndex !== -1) {
          if (item.accion == 1) {
            barData.datasets[0].data[dayIndex] += item.count;
          } else if (item.accion == 2) {
            barData.datasets[1].data[dayIndex] += item.count;
          }
        }
      });

      function getRandomColor() {
        var letters = '0123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 6; i++) {
          color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
      }

      window.onload = function(){
        var ctx = $(".doughnut-chart")[0].getContext("2d");
        window.myDoughnut = new Chart(ctx).Doughnut(doughnutData, {
          responsive: true,
          showTooltips: true
        });

        var ctx4 = $(".bar-chart")[0].getContext("2d");
        window.myBar = new Chart(ctx4).Bar(barData, {
          responsive: true,
          showTooltips: true
        });
      };
    })(jQuery);
  </script>
  <!-- end: Javascript -->
</body>
</html>

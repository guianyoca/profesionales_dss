<?php
if (empty($_SESSION['active'])) {
    header('Location: ../');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Panel de Administración</title>
    <link href="../assets/css/material-dashboard.css" rel="stylesheet" />
    <link href="../assets/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link rel="stylesheet" href="../assets/js/jquery-ui/jquery-ui.min.css">
    <script src="../assets/js/all.min.js" crossorigin="anonymous"></script>
    <script src="../assets/js/tinymce/tinymce.min.js"></script>
</head>

<body>
    <div class="wrapper ">
        <div class="sidebar" data-color="purple" data-background-color="blue" data-image="../assets/img/sidebar.jpg">
            <div class="logo bg-primary"><a href="./" class="simple-text logo-normal">
                    Profesionales DSS
                </a></div>
            <div class="sidebar-wrapper">
                <ul class="nav">
                <?php if($_SESSION['rol']==2){ ?>
                    <li class="nav-item">
                        <a class="nav-link d-flex" href="asistencia.php">
                            <i class="far fa-clock mr-2 fa-2x"></i>
                            <p> Asistencia</p>
                        </a>
                    </li>
                <?php }?>
                <?php if($_SESSION['rol']==2){ ?>
                    <li class="nav-item">
                        <a class="nav-link d-flex" href="pacientes_dia.php">
                            <i class="fas fa-users mr-2 fa-2x"></i>
                            <p> Pacientes del Dia</p>
                        </a>
                    </li>
                    <?php }?>
                    <?php if($_SESSION['rol']==2){ ?>
                    <li class="nav-item">
                        <a class="nav-link d-flex" href="turnos.php">
                            <i class="fa fa-calendar-check mr-2 fa-2x"></i>
                            <p> Cargar Turnos</p>
                        </a>
                    </li>
                    <?php }?>  
                    <?php if($_SESSION['rol']==2){ ?>
                    <li class="nav-item">
                        <a class="nav-link d-flex" href="todos_turnos.php">
                            <i class="fa fa-calendar mr-2 fa-2x"></i>
                            <p> Todos Turnos</p>
                        </a>
                    </li>
                    <?php }?> 
                    <?php if($_SESSION['rol']==2){ ?>
                    <li class="nav-item">
                        <a class="nav-link d-flex" href="cargar_historia_clinica.php">
                            <i class="fa fa-address-card mr-2 fa-2x"></i>
                            <p> Cargar Historia Clinica</p>
                        </a>
                    </li>
                    <?php }?>  
                    <?php if($_SESSION['rol']==2 || $_SESSION['rol']==1){ ?>
                    <li class="nav-item">
                        <a class="nav-link d-flex" href="historias_clinicas.php">
                            <i class="fa fa-address-book mr-2 fa-2x"></i>
                            <p> Ver Historias Clinicas</p>
                        </a>
                    </li>
                    <?php }?>    
                    <?php if($_SESSION['rol']==1){ ?>
                    <li class="nav-item">
                        <a class="nav-link d-flex" href="asistencias_profesionales.php">
                            <i class="far fa-clock mr-2 fa-2x"></i>
                            <p> Asistencias Profesionales</p>
                        </a>
                    </li>
                    <?php }?>  
                    <?php if($_SESSION['rol']==1){ ?>
                    <li class="nav-item">
                        <a class="nav-link d-flex" href="asistencias_pacientes.php">
                            <i class="fas fa-users mr-2 fa-2x"></i>
                            <p> Asistencias Pacientes</p>
                        </a>
                    </li>
                    <?php }?>  
                </ul>
            </div>
        </div>
        <div class="main-panel">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-absolute fixed-top">
                <div class="container-fluid">
                    <div class="navbar-wrapper">
                        <a class="navbar-brand" href="javascript:;">DSS</a>
                    </div>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="navbar-toggler-icon icon-bar"></span>
                        <span class="navbar-toggler-icon icon-bar"></span>
                        <span class="navbar-toggler-icon icon-bar"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end">

                        <ul class="navbar-nav">
                            <li class="nav-item dropdown">
                                <a class="nav-link" href="javascript:;" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-user"></i>
                                    <p class="d-lg-none d-md-block">
                                        Cuenta
                                    </p>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#nuevo_pass">Perfil</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="salir.php">Cerrar Sesión</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- End Navbar -->
            <div class="content bg">
                <div class="container-fluid">
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>STIinfo</title>

    <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="<?=base_url()?>assets/AdminLTE/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?=base_url()?>assets/AdminLTE/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="<?=base_url('assets/AdminLTE/')?>plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?=base_url('assets/AdminLTE/')?>plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="<?=base_url('assets/AdminLTE/')?>plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- jQuery -->
  <script src="<?=base_url()?>assets/AdminLTE/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="<?=base_url()?>assets/AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="<?=base_url()?>assets/AdminLTE/dist/js/adminlte.min.js"></script>
  <!-- DataTables  & Plugins -->
  <script src="<?=base_url('assets/AdminLTE/')?>plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="<?=base_url('assets/AdminLTE/')?>plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="<?=base_url('assets/AdminLTE/')?>plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="<?=base_url('assets/AdminLTE/')?>plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
  <script src="<?=base_url('assets/AdminLTE/')?>plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
  <script src="<?=base_url('assets/AdminLTE/')?>plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

      <li class="nav-item">
        <a href="<?=base_url('login_admin/logout')?>" class="nav-link">Log Out</a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <span class="brand-text font-weight-light">STIInfo</span>
    </a>

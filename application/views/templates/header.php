<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?= (isset($title)) ? $title : 'System Antrian'; ?></title>

    <!-- Font Vendor Khas SB Admin 2 -->
    <link href="<?= base_url('assets/admin/vendor/fontawesome-free/css/all.min.css'); ?>" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Stylesheet Utama Terintegrasi Lokal -->
    <link href="<?= base_url('assets/admin/css/sb-admin-2.min.css'); ?>" rel="stylesheet" type="text/css">
    
    <!-- Pustaka Tambahan DataTables CSS -->
    <link href="<?= base_url('assets/admin/vendor/datatables/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet" type="text/css">

    <!-- SweetAlert2 ditaruh sebelum penutup head agar fungsi alert siap dipanggil di view mana saja -->
    <script src="<?= base_url('assets/admin/vendor/sweetalert2/sweetalert2.all.min.js'); ?>"></script>

</head>

<body id="page-top">

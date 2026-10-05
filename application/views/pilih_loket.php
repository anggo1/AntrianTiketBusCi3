<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Pilih Loket Dinas - Sinar Jaya</title>

    <!-- Menggunakan aset gaya lokal proyek Anda -->
    <link href="<?php echo base_url('assets/admin/vendor/fontawesome-free/css/all.min.css'); ?>" rel="stylesheet" type="text/css">
    <link href="https://googleapis.com" rel="stylesheet">
    <link href="<?php echo base_url('assets/admin/css/sb-admin-2.min.css'); ?>" rel="stylesheet">
</head>

<body class="bg-gradient-primary">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-7 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-2 font-weight-bold">Selamat Datang, <?php echo $this->session->userdata('name'); ?>!</h1>
                                <p class="text-muted small mb-4">Silakan hubungkan perangkat Anda ke meja loket dinas yang aktif hari ini untuk memulai manajemen panggilan tiket.</p>
                            </div>

                            <form class="user" method="post" action="<?php echo base_url('auth/pilih_loket'); ?>">
                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-800 pl-2">Pilih Meja Loket Kerja:</label>
                                    <select name="id_loket" class="form-control rounded-pill px-3" style="height: 50px; font-size: 0.9rem;" required>
                                        <option value="">-- Hubungkan ke Loket Aktif --</option>
                                        <?php foreach($loket_buka as $lb) : ?>
                                            <option value="<?php echo $lb['id_loket']; ?>">
                                                Loket <?php echo $lb['loket']; ?> &rarr; Jurusan (<?php echo $lb['jurusan']; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success btn-user btn-block font-weight-bold" style="font-size: 1rem;">
                                    <i class="fas fa-door-open mr-2"></i> Buka Meja Panggilan
                                </button>
                            </form>
                            
                            <hr>
                            <div class="text-center">
                                <a class="small text-danger font-weight-bold" href="<?php echo base_url('auth/logout'); ?>">
                                    <i class="fas fa-sign-out-alt mr-1"></i>Batalkan & Keluar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pustaka JavaScript Lokal -->
    <script src="<?php echo base_url('assets/admin/vendor/jquery/jquery.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/admin/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
</body>

</html>

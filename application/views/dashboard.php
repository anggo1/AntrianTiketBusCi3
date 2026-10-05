<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
        <?php if (has_permission('is_print')) : ?>
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
            </a>
        <?php endif; ?>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Selamat Datang Card -->
        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Status Log In</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Selamat Datang kembali, <?= $user_name; ?>!</div>
                            <p class="text-muted mt-2 mb-0 font-weight-normal">Anda masuk dengan hak akses yang disesuaikan secara dinamis berdasarkan level atau pengguna spesifik.</p>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

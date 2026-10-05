<div class="container-fluid mt-4">
    <!-- Topbar Ringkasan Informasi -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold"><i class="fas fa-desktop text-primary mr-2"></i><?php echo $title; ?></h1>
        <div class="d-flex">
            <span class="badge badge-danger p-2 shadow-sm font-weight-bold mr-2" style="font-size:0.9rem;">
                <i class="fas fa-users mr-1"></i> Sisa Antrean Global: <?php echo $global_waiting; ?> Orang
            </span>
            <span class="badge badge-dark p-2 shadow-sm font-weight-bold" style="font-size:0.9rem;">
                <i class="fas fa-calendar-alt mr-2"></i><span id="live_clock"><?php echo date('d F Y'); ?></span>
            </span>
        </div>
    </div>

    <!-- Grid Kartu Loket Aktif -->
    <div class="row">
        <?php if (!empty($monitoring_loket)) : ?>
            <?php foreach($monitoring_loket as $ml) : ?>
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card shadow border-0 card-loket transition-all">
                        <!-- Kepala Kartu: Menampilkan Angka Nomor Loket -->
                        <div class="card-header bg-gradient-primary py-3 d-flex justify-content-between align-items-center rounded-top">
                            <h5 class="m-0 font-weight-bold text-white"><i class="fas fa-store-alt mr-2"></i>LOKET <?php echo $ml['loket']; ?></h5>
                            <span class="badge badge-success px-2 py-1 small shadow-2xs font-weight-bold" style="letter-spacing: 0.5px;">AKTIF MEMANGGIL</span>
                        </div>
                        
                        <!-- Badan Kartu: Informasi Utama Nomor Antrean -->
                        <div class="card-body bg-white text-center py-4 position-relative">
                            <div class="text-xs font-weight-bold text-uppercase text-muted tracking-wider mb-1">Nomor Antrean Saat Ini</div>
                            
                            <!-- Lingkaran Angka Display Besar -->
                            <div class="display-number mx-auto shadow-inner d-flex align-items-center justify-content-center my-3">
                                <span class="font-weight-bold text-gray-900"><?php echo $ml['no_antrian_sekarang']; ?></span>
                            </div>
                            
                            <!-- Informasi Rincian Penumpang yang Sedang Dilayani -->
                            <div class="mt-3 px-2 text-left small border-top pt-3 bg-light rounded p-2">
                                <div class="row mb-1">
                                    <div class="col-4 font-weight-bold text-muted">Penumpang:</div>
                                    <div class="col-8 font-weight-bold text-gray-900 text-truncate"><?php echo $ml['nama_penumpang']; ?></div>
                                </div>
                                <div class="row">
                                    <div class="col-4 font-weight-bold text-muted">Kelas Bus:</div>
                                    <div class="col-8"><span class="badge badge-info font-weight-normal"><?php echo $ml['kelas_bus']; ?></span></div>
                                </div>
                            </div>
                        </div>

                        <!-- Kaki Kartu: Daftar Semua Rute Jurusan yang Didukung Oleh Loket Ini -->
                        <div class="card-footer bg-gray-100 py-3 rounded-bottom border-top" style="min-height: 85px;">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"><i class="fas fa-route mr-1"></i> Rute Tujuan Loket:</div>
                            <p class="mb-0 text-gray-700 font-weight-bold text-justify tracking-tight" style="font-size: 0.8rem; line-height: 1.3;">
                                <?php echo $ml['jurusan']; ?>

                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="col-12">
                <div class="alert alert-warning text-center shadow-sm py-4">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>
                    <span class="font-weight-bold">Tidak ada loket yang berstatus AKTIF (Buka) di sistem saat ini!</span>
                    <br><small class="text-muted">Aktifkan status loket terlebih dahulu melalui menu pengaturan master data loket.</small>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ==========================================
     SENTUHAN GAYA CSS KOSMETIK AGAR MANIS
     ========================================== -->
<style>
    .transition-all { transition: all 0.25s ease-in-out; }
    .card-loket { border-radius: 12px !important; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1) !important; }
    .card-loket:hover { transform: translateY(-5px); box-shadow: 0 0.5rem 2rem 0 rgba(78, 115, 223, 0.2) !important; }
    
    /* Lingkaran pod sirkular display nomor antrean */
    .display-number {
        width: 120px;
        height: 120px;
        background-color: #f8f9fc;
        border: 4px solid #4e73df;
        border-radius: 50% !important;
        font-size: 3rem;
        box-shadow: inset 0 3px 5px rgba(0,0,0,0.06);
    }
    
    .tracking-wider { letter-spacing: 1px; }
    .shadow-2xs { box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
</style>

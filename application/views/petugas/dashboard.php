<div class="container-fluid mt-4">
    <h1 class="h3 mb-4 text-gray-800"><?php echo $title; ?> (<?php echo $my_counter_name; ?>)</h1>

    <div class="row">
        <!-- Papan Tampilan Nomor Sedang Dilayani -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 bg-primary text-white">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-uppercase mb-1">Nomor Sedang Dilayani</div>
                    <div style="font-size: 3.5rem; font-weight: 800;"><?php echo $current_number; ?></div>
                </div>
            </div>
        </div>

        <!-- Papan Tampilan Sisa Antrean Menunggu -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Sisa Antrean Menunggu</div>
                    <div style="font-size: 3.5rem; font-weight: 800;" class="text-gray-800"><?php echo $waiting_here; ?> <small style="font-size:1.5rem">Orang</small></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Kendali Aksi Utama Panggilan -->
    <div class="card shadow mb-4 col-lg-8 p-0">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Kios Kendali Panggilan Kasir</h6>
        </div>
        <div class="card-body text-center py-5">
            <p class="text-muted mb-4">Klik tombol di bawah ini untuk memanggil nomor antrean berikutnya berdasarkan urutan pendaftaran identitas KTP penumpang.</p>
            <a href="<?php echo base_url('petugas/call_next'); ?>" class="btn btn-success btn-lg px-5 py-3 shadow border-0" style="font-size: 1.4rem; font-weight: 700;">
                <i class="fas fa-bullhorn mr-2"></i> PANGGIL SELANJUTNYA
            </a>
        </div>
    </div>
</div>

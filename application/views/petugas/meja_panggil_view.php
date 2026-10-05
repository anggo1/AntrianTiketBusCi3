<div class="container-fluid mt-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold"><i class="fas fa-cash-register text-success mr-2"></i><?php echo $title; ?></h1>
        <span class="badge badge-primary p-2 font-weight-bold" style="font-size:0.9rem;"><i class="fas fa-user-tie mr-1"></i> Operator: <?php echo $this->session->userdata('name'); ?></span>
    </div>

    <!-- Informasi Rute Terkunci -->
    <div class="alert alert-info border-left-info shadow-sm mb-4 bg-white text-dark d-flex align-items-center">
        <div class="bg-info p-3 text-white rounded mr-3 shadow-2xs"><i class="fas fa-route fa-lg"></i></div>
        <div>
            <div class="small font-weight-bold text-info text-uppercase">Meja Tugas: <?php echo $loket_name; ?></div>
            <div class="font-weight-bold text-gray-800" style="font-size:0.95rem;">Melayani Rute: <?php echo $jurusan_loket; ?></div>
        </div>
    </div>

    <!-- Pod Utama Pemanggilan -->
    <div class="card shadow border-0 mb-4 col-lg-10 p-0 mx-auto">
        <div class="card-header bg-gradient-success text-white py-3 text-center rounded-top">
            <h5 class="m-0 font-weight-bold" style="letter-spacing:1px;"><?php echo strtoupper($loket_name); ?> - KONSOL UTAMA</h5>
        </div>
        <div class="card-body bg-white p-4 rounded-bottom">
            <div class="row align-items-center">
                <!-- Pod Sirkular Angka Besar -->
                <div class="col-md-5 text-center border-right py-3">
                    <div class="text-xs font-weight-bold text-uppercase text-muted mb-2">Nomor Sedang Dipanggil</div>
                    <div class="mx-auto d-flex align-items-center justify-content-center shadow" style="width: 160px; height: 160px; background-color:#f8f9fc; border: 5px solid #1cc88a; border-radius: 50% !important;">
                        <span class="font-weight-bold text-gray-900" style="font-size: 4.2rem;"><?php echo !empty($current_data) ? $current_data['no_antrian'] : '0'; ?></span>
                    </div>
                    <div class="badge badge-light border text-gray-700 mt-3 px-3 py-2">
                        Sisa Antrean Menunggu: <b><?php echo $total_waiting; ?> Orang</b>
                    </div>
                </div>

                <!-- Tombol Aksi Kemudi -->
                <!-- CARI DAN GANTI BLOK INPUT PENCARIAN LAMA ANDA MENJADI STRUKTUR FORM INI: -->
<div class="col-md-7 px-lg-4 text-center mt-3 mt-md-0">
    
    <!-- Bungkus input ke dalam form action POST -->
    <form class="mb-4" method="post" action="<?php echo base_url('petugas/panggil_spesifik'); ?>">
    <div class="form-group row justify-content-center mb-0">
        <label class="col-sm-3 col-form-label font-weight-bold text-gray-800 text-md-right pt-2">Cari Antrean</label>
        <div class="col-sm-9 d-flex">
            <input type="number" class="form-control mr-2 form-control-lg font-weight-bold text-center text-primary" 
                   placeholder="No..." name="no_antrian" min="1" required 
                   style="max-width: 120px; border: 2px solid #4e73df;">
            
            <!-- PERBAIKAN: GANTI KATA TOMBOL MENJADI 'Cari' DAN WARNA KOSMETIK BIRU SB ADMIN -->
            <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm">
                <i class="fas fa-search mr-1"></i>Cari
            </button>
        </div>
    </div>
</form>

    <div class="row">
        <div class="col-6">
            <!-- Tombol Panggil Ulang Suara Nomor Sekarang -->
            <button class="btn btn-info btn-block py-4 shadow font-weight-bold border-0 btn-tactile" onclick="recall()" <?php echo empty($current_data) ? 'disabled' : ''; ?> style="background: linear-gradient(to bottom, #36b9cc, #258cd1); border-radius:10px;">
                <i class="fas fa-bullhorn fa-2x d-block mb-2"></i> Panggil
            </button>
        </div>
        <div class="col-6">
            <!-- Tombol Geser Antrean Selanjutnya -->
            <a href="<?php echo base_url('petugas/panggil_berikutnya/' . $id_loket_aktif); ?>" class="btn btn-danger btn-block py-4 shadow font-weight-bold border-0 btn-tactile" style="background: linear-gradient(to bottom, #e74a3b, #be2617); border-radius:10px;">
                <i class="fas fa-forward fa-2x d-block mb-2"></i> Antrian Selanjutnya
            </a>
        </div>
    </div>
</div>

    <!-- Informasi Detail Identitas Penumpang Bawah -->
    <div class="card shadow border-0 col-lg-10 mx-auto p-0">
        <div class="card-header bg-light py-3">
            <h6 class="m-0 font-weight-bold text-gray-800"><i class="fas fa-id-card mr-2 text-success"></i>Data Penumpang Terpanggil Saat Ini</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="bg-gray-100 text-center text-dark font-weight-bold small text-uppercase">
                        <tr>
                            <th>Nama</th>
                            <th>No KTP</th>
                            <th>No Telefon</th>
                            <th>Kelas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($current_data)) : ?>
                        <tr class="text-center">
                            <td class="font-weight-bold text-gray-900"><?php echo $current_data['nama']; ?></td>
                            <td><code><?php echo $current_data['no_ktp']; ?></code></td>
                            <td><?php echo $current_data['no_tlp']; ?></td>
                            <td><span class="badge badge-success px-3 py-1"><?php echo $current_data['kelas']; ?></span></td>
                        </tr>
                        <?php else : ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4 italic">Belum ada aktivitas panggilan tiket di meja loket Anda hari ini.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-tactile { transition: transform 0.1s ease; }
    .btn-tactile:active { transform: translateY(3px) scale(0.97); }
</style>
<!-- GANTI BLOK SCRIPT DI PALING BAWAH FILE meja_panggil_view.php DENGAN VERSI BEL + SUARA LEMBUT INI -->
<!-- KODE TERBARU MENGATASI SUARA AKSEN INGGRIS PADA CASING PETUGAS LOKET -->
<script>function recall() {
    const nomor = "<?php echo !empty($current_data) ? $current_data['no_antrian'] : 0; ?>";
    const loket = "<?php echo $loket_name; ?>";
    
    if(parseInt(nomor) > 0) {
        // Memanggil fungsi playlist offline baru yang ada di footer
        playQueueSoundOffline(nomor, loket);
    }
}

</script>

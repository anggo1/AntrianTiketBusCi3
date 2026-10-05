<div class="container-fluid mt-4">
    <!-- Judul Header Halaman -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold"><i class="fas fa-bullhorn text-primary mr-2"></i><?php echo $title; ?></h1>
        <span class="badge badge-dark p-2 shadow-sm font-weight-bold" style="font-size:0.9rem;"><i class="fas fa-calendar-alt mr-2"></i><?php echo date('d F Y'); ?></span>
    </div>

    <div class="row">
        <!-- ==========================================
             KOLOM KIRI & KANAN: DAFTAR TOMBOL PILIHAN LOKET ASLI
             ========================================== -->
        <div class="col-xl-3 col-lg-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gray-900 py-3 text-center rounded-top">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-list-ol mr-2"></i>Pilih Loket Dinas</h6>
                </div>
                <div class="card-body p-2 bg-light rounded-bottom" style="max-height: 480px; overflow-y: auto;">
                    <?php foreach($all_loket as $lk) : ?>
                        <?php 
                        // Skenario tombol aktif vs tidak aktif
                        $is_active = ($lk['id_loket'] == $id_loket_aktif);
                        $btn_style = $is_active ? 'btn-primary shadow font-weight-bold scale-up' : 'btn-white text-primary border-gray-300';
                        $status_badge = ($lk['status'] == 1) ? 'badge-success' : 'badge-secondary';
                        ?>
                        <a href="<?php echo base_url('petugas?id_loket=' . $lk['id_loket']); ?>" class="btn btn-block py-3 mb-2 text-left rounded shadow-2xs transition-all <?php echo $btn_style; ?>" style="border-left: 5px solid <?php echo $is_active ? '#1cc88a' : '#4e73df'; ?>;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span style="font-size:1.1rem;">Loket <?php echo $lk['loket']; ?></span>
                                    <div class="small <?php echo $is_active ? 'text-white-50' : 'text-muted'; ?> mt-1 font-weight-normal" style="line-height:1.2;">
                                        <i class="fas fa-bus mr-1"></i> <?php echo character_limiter($lk['jurusan'], 25); ?>
                                    </div>
                                </div>
                                <span class="badge <?php echo $status_badge; ?> px-2 py-1">
                                    <?php echo ($lk['status'] == 1) ? 'Buka' : 'Tutup'; ?>
                                </span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- ==========================================
             KOLOM TENGAH: KONSOL UTAMA PANGGILAN TIKET
             ========================================== -->
        <div class="col-xl-9 col-lg-8 mb-4">
            <!-- Alert Info Kategori Rute Tujuan Jurusan Loket Aktif -->
            <div class="alert alert-info border-left-info shadow-sm mb-4 p-3 bg-white text-dark d-flex align-items-center">
                <div class="bg-info p-3 text-white rounded mr-3 shadow-xs"><i class="fas fa-route fa-lg"></i></div>
                <div>
                    <div class="small font-weight-bold text-info text-uppercase">Rute Jurusan yang Dilayani <?php echo $loket_name; ?> :</div>
                    <div class="font-weight-bold text-gray-800" style="font-size:0.95rem;"><?php echo !empty($jurusan_loket) ? $jurusan_loket : '<span class="text-danger italic">Belum Dikonfigurasi</span>'; ?></div>
                </div>
            </div>

            <!-- Konsol Tombol Aksi Kemudi Utama -->
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-gradient-primary text-white py-3 text-center">
                    <h5 class="m-0 font-weight-bold" style="letter-spacing:1px;"><?php echo strtoupper($loket_name); ?> - KONSOL UTAMA</h5>
                </div>
                <div class="card-body bg-white p-4">
                    <div class="row align-items-center">
                        <!-- Tampilan Besar Papan Skor Nomor Terpanggil -->
                        <div class="col-md-5 text-center mb-4 mb-md-0 border-right-md py-3">
                            <div class="text-xs font-weight-bold text-uppercase tracking-wider text-muted mb-2">Nomor Antrean Sekarang</div>
                            <div class="bg-light rounded p-4 border shadow-inner mx-auto d-flex align-items-center justify-content-center" style="width: 170px; height: 170px; border-radius: 50% !important; border: 4px solid #4e73df !important;">
                                <span class="font-weight-bold text-gray-900" style="font-size: 4rem; line-height:1;"><?php echo !empty($current_data) ? $current_data['no_antrian'] : '0'; ?></span>
                            </div>
                            <div class="badge badge-light border text-gray-700 mt-3 px-3 py-2">
                                <i class="fas fa-users mr-1 text-warning"></i> Sisa Antrean Rute Ini: <b><?php echo $total_waiting; ?> Orang</b>
                            </div>
                        </div>

                        <!-- Blok Tombol Aksi Utama Panggilan -->
                        <div class="col-md-7 px-lg-4 text-center">
                            <div class="form-group row justify-content-center mb-4">
                                <label class="col-sm-3 col-form-label font-weight-bold text-gray-800 text-md-right pt-2">Cari Antrean</label>
                                <div class="col-sm-9 d-flex">
                                    <input type="text" class="form-control mr-2 form-control-lg" placeholder="Masukkan No Antrean..." id="search_queue">
                                    <button class="btn btn-success px-4" id="btn_search_call"><i class="fas fa-search mr-1"></i>Cari</button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <!-- Tombol Panggil Ulang Suara -->
                                    <button class="btn btn-info btn-block py-3 shadow font-weight-bold border-0 btn-action-tactile" onclick="recallNumber()" <?php echo empty($current_data) ? 'disabled' : ''; ?> style="background: linear-gradient(to bottom, #36b9cc, #258cd1);">
                                        <i class="fas fa-bullhorn fa-lg d-block mb-2"></i> Panggil Ulang
                                    </button>
                                </div>
                                <div class="col-6">
                                    <!-- Tombol Geser Antrean Selanjutnya -->
                                    <a href="<?php echo base_url('petugas/panggil_berikutnya/' . $id_loket_aktif); ?>" class="btn btn-danger btn-block py-3 shadow font-weight-bold border-0 btn-action-tactile" style="background: linear-gradient(to bottom, #e74a3b, #be2617);">
                                        <i class="fas fa-forward fa-lg d-block mb-2"></i> Antrian Selanjutnya
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Papan Data Identitas KTP Penumpang yang Sedang Dilayani -->
            <div class="card shadow border-0">
                <div class="card-header bg-light py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-gray-800"><i class="fas fa-id-card mr-2 text-primary"></i>Informasi Detail Penumpang Terpanggil</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" width="100%" cellspacing="0">
                            <thead class="bg-gray-100 text-center text-dark font-weight-bold small text-uppercase">
                                <tr>
                                    <th>Nama Penumpang</th>
                                    <th>No KTP Identitas</th>
                                    <th>No Tlp Aktif</th>
                                    <th>Kelas Pelayanan</th>
                                    <th>Tujuan Rute</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($current_data)) : ?>
                                <tr>
                                    <td class="font-weight-bold text-gray-900 pl-3"><?php echo $current_data['nama']; ?></td>
                                    <td><code><?php echo $current_data['no_ktp']; ?></code></td>
                                    <td><?php echo $current_data['no_tlp']; ?></td>
                                    <td class="text-center"><span class="badge badge-info px-3 py-1"><?php echo $current_data['kelas']; ?></span></td>
                                    <td class="small font-weight-bold text-gray-700"><?php echo character_limiter($current_data['tujuan'], 45); ?></td>
                                </tr>
                                <?php else : ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4 italic">Belum ada aktivitas panggilan tiket pada loket ini untuk hari ini.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
.transition-all { transition: all 0.25s ease-in-out; }
.scale-up { transform: scale(1.02); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
.btn-action-tactile { border-radius: 8px; transition: transform 0.1s ease; }
.btn-action-tactile:active { transform: translateY(3px) scale(0.98); }
.border-right-md { border-right: 1px solid #e3e6f0; }
@media (max-width: 767.98px) { .border-right-md { border-right: none; border-bottom: 1px solid #e3e6f0; } }
function recallNumber() {
Swal.fire({
icon: 'info',
title: 'Memanggil Ulang',
text: 'Mengirimkan perintah suara panggil ulang untuk Nomor Antrean: ',
toast: true,
position: 'top-end',
showConfirmButton: false,
timer: 2000
});
}

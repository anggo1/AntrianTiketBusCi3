<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Pendaftaran Antrian - Sinar Jaya Group</title>
    <link href="<?php echo base_url('assets/admin/css/sb-admin-2.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/admin/vendor/fontawesome-free/css/all.min.css'); ?>" rel="stylesheet">
    <!-- Tambahan SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f4f6f9;
        font-family: 'Nunito', sans-serif;
    }

    .header-tv {
        background: linear-gradient(135deg, #1f2937, #111827);
        color: #fff;
        padding: 15px 30px;
        border-bottom: 5px solid #4e73df;
    }

    .logo-text {
        font-style: italic;
        font-weight: 900;
        font-size: 1.8rem;
        color: #e74a3b;
    }

    .logo-container {
        min-height: 50px;
    }

    .card-kios {
        border-radius: 15px;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .rounded-pill {
        border-radius: 50px !important;
    }

    /* Gaya Struk Tiket Fisik */
    .ticket-box {
        background: #fff;
        border: 2px dashed #ccd1d9;
        border-radius: 10px;
        max-width: 340px;
        margin: 0 auto;
        padding: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        position: relative;
    }

    .ticket-number {
        font-size: 4.5rem;
        font-weight: 900;
        color: #1cc88a;
        line-height: 1.1;
        margin: 15px 0;
    }

    /* Penyesuaian kustom select agar rapi tanpa merusak fungsi */
    .custom-select-kios {
        text-align-last: center;
        padding-right: 30px;
        /* Ruang untuk panah dropdown */
    }

    @media (max-width: 768px) {
        .header-tv {
            padding: 15px 15px;
            text-align: center;
            flex-direction: column;
        }

        .header-tv .text-right {
            text-align: center !important;
            margin-top: 10px;
        }

        .logo-text {
            font-size: 1.4rem;
        }
    }
    </style>
</head>

<body>

    <!-- 1. HEADER DINAMIS -->
    <div class="header-tv d-flex justify-content-between align-items-center shadow mb-4">
        <div class="d-flex align-items-center flex-column flex-md-row text-center text-md-left">
            <div
                class="bg-white px-3 py-1 rounded mb-2 mb-md-0 mr-md-3 shadow-sm d-flex align-items-center justify-content-center logo-container">
                <?php if(!empty($display_setup['logo']) && file_exists('./assets/admin/img/upload/'.$display_setup['logo'])) : ?>
                <img src="<?= base_url('assets/admin/img/upload/'.$display_setup['logo']); ?>"
                    style="max-height: 45px; width: auto;">
                <?php else: ?>
                <span class="logo-text">Sinar Jaya <span class="text-dark">GROUP</span></span>
                <?php endif; ?>
            </div>
            <div>
                <h4 class="mb-0 font-weight-bold" style="color: #87CEEB; text-transform: uppercase;">
                    <?= !empty($display_setup['judul_atas']) ? $display_setup['judul_atas'] : 'MONITOR INFORMASI ANTRIAN'; ?>
                </h4>
                <small class="text-white-50"><i
                        class="fas fa-map-marker-alt text-danger mr-1"></i><?= !empty($display_setup['detail_teks']) ? $display_setup['detail_teks'] : ''; ?></small>
            </div>
        </div>
        <div class="text-right">
            <h4 class="mb-0 font-weight-bold" id="kios_clock" style="color: #fff;">00:00:00</h4>
            <small class="text-white-50 font-weight-bold"><?= date('d F Y'); ?></small>
        </div>
    </div>    <!-- ==================================================================== -->
    <!-- KARD INFORMASI LIVE: JUMLAH ANTRIAN & NOMOR TERPANGGIL (RESPONSIF MOBILE) -->
    <!-- ==================================================================== -->
    <div class="row text-center mb-4">
        <!-- 1. KARD KIRI: SISA ANTRIAN MENUNGGU -->
        <div class="col-6 pr-2 pl-3">
            <div class="card shadow border-0" style="border-radius: 15px; border-left: 5px solid #36b9cc !important;">
                <div class="card-body px-2 py-3">
                    <div class="text-uppercase mb-1 font-weight-bold text-info" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <i class="fas fa-users mr-1"></i> Sisa Antrean
                    </div>
                    <div class="h3 mb-0 font-weight-bold text-gray-800 tracking-tight">
                        <?php echo !empty($total_waiting) ? $total_waiting : '0'; ?> <span style="font-size:0.85rem; font-weight:600;" class="text-muted">Orang</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. KARD KANAN: ANTRIAN TERAKHIR TERPANGGIL -->
        <div class="col-6 pl-2 pr-3">
            <div class="card shadow border-0" style="border-radius: 15px; border-left: 5px solid #1cc88a !important;">
                <div class="card-body px-2 py-3">
                    <div class="text-uppercase mb-1 font-weight-bold text-success" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <i class="fas fa-bullhorn mr-1"></i> Terpanggil
                    </div>
                    <div class="h3 mb-0 font-weight-bold text-gray-800 tracking-tight">
                        <span style="font-size:0.9rem; font-weight:700;" class="text-success">No.</span> <?php echo !empty($called_number) ? $called_number : '0'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. KONTEN RESPONSIF UTAMA -->
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-9 col-md-10">

                <!-- KONDISI A: TAMPILAN SUKSES CETAK NOMOR ANTREAN -->
                <?php if($this->session->flashdata('ticket_success')) : $ticket = $this->session->flashdata('ticket_success'); ?>
                <div
                    class="card card-kios shadow-lg text-center p-4 mb-4 border-left-success animated border-0 bg-white">
                    <div class="card-body">
                        <div class="text-success mb-3"><i class="fas fa-check-circle fa-3x"></i></div>
                        <h3 class="font-weight-bold text-gray-900 mb-1">Pendaftaran Berhasil!</h3>
                        <p class="text-muted small mb-4">Silakan simpan atau unduh nomor antrean Anda di bawah ini untuk
                            ditunjukkan kepada petugas loket.</p>

                        <!-- Area Struk yang akan Diunduh -->
                        <div id="capture_ticket_zone" class="ticket-box text-center mb-4">
                            <div class="border-bottom pb-2 mb-2">
                                <h5 class="font-weight-bold text-gray-900 mb-0" style="font-size:1.1rem;">SINAR JAYA
                                    GROUP</h5>
                                <small class="text-muted text-uppercase"
                                    style="font-size:0.7rem; font-weight:700; letter-spacing:0.5px;">Tiket Antrean Kios
                                    Mandiri</small>
                            </div>
                            <div class="small text-muted font-weight-bold text-uppercase mt-2">Nomor Antrean Anda</div>
                            <div class="ticket-number" id="ticket_no_val"><?= $ticket['no_antrian']; ?></div>
                            <div class="border-top pt-3 text-left small text-gray-800">
                                <table class="table table-borderless table-sm mb-0" style="font-size: 0.85rem;">
                                    <tr>
                                        <td>Nama</td>
                                        <td>:</td>
                                        <td class="font-weight-bold text-gray-900"><?= $ticket['nama']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Kelas</td>
                                        <td>:</td>
                                        <td><span class="badge badge-info"><?= $ticket['kelas']; ?></span></td>
                                    </tr>
                                    <tr>
                                        <td>Tujuan</td>
                                        <td>:</td>
                                        <td class="font-weight-bold" style="line-height:1.2;">
                                            <?= character_limiter($ticket['tujuan'], 35); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Waktu</td>
                                        <td>:</td>
                                        <td class="text-muted"><?= $ticket['waktu']; ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="border-top mt-3 pt-2 text-center text-muted"
                                style="font-size:0.75rem; font-style:italic;">
                                *Terima kasih telah memilih pelayanan kami*
                            </div>
                        </div>

                        <!-- Tombol Aksi Kontrol Kapsul -->
                        <!-- CARI BARIS TOMBOL KEMBALI DI LAMA ANDA, SEGERA GANTI MENJADI SEPERTI INI -->
                        <div class="d-flex justify-content-center flex-column flex-sm-row">
                            <button type="button" id="btn_download_ticket"
                                class="btn btn-success font-weight-bold rounded-pill shadow px-4 py-2 mb-2 mb-sm-0 mr-sm-2">
                                <i class="fas fa-download mr-1"></i> Unduh Tiket Antrean
                            </button>

                            <!-- PERBAIKAN: Menambahkan ?action=reset di ujung URL base_url -->
                            <a href="<?php echo base_url('antrian?action=reset'); ?>"
                                class="btn btn-secondary font-weight-bold rounded-pill shadow px-4 py-2">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali Ke Form
                            </a>
                        </div>

                    </div>
                </div>

                <!-- KONDISI B: TAMPILAN FORM INPUT UTAMA PENDAFTARAN -->
                <?php else: ?>
                <div class="card card-kios shadow">
                    <div class="card-header bg-white py-3 border-bottom text-center">
                        <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-id-card mr-2"></i>Form Formulir
                            Identitas Penumpang</h5>
                    </div>
                    <div class="card-body p-4 bg-white rounded-bottom">
                        <form method="post" action="<?php echo base_url('antrian'); ?>">
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Nama Lengkap Penumpang</label>
                                <input type="text"
                                    class="form-control form-control-lg rounded-pill px-3 shadow-sm text-center"
                                    name="nama" value="<?php echo set_value('nama'); ?>" required
                                    placeholder="Masukkan nama sesuai KTP...">
                                <?php echo form_error('nama', '<small class="text-danger pl-3">', '</small>'); ?>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Nomor KTP Identitas</label>
                                <input type="number"
                                    class="form-control form-control-lg rounded-pill px-3 shadow-sm text-center"
                                    name="no_ktp" value="<?php echo set_value('no_ktp'); ?>" required
                                    placeholder="Masukkan 16 digit NIK...">
                                <?php echo form_error('no_ktp', '<small class="text-danger pl-3">', '</small>'); ?>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">No Telefon / WhatsApp Aktif</label>
                                <input type="text"
                                    class="form-control form-control-lg rounded-pill px-3 shadow-sm text-center"
                                    name="no_tlp" value="<?php echo set_value('no_tlp'); ?>" required
                                    placeholder="Contoh: 08123456xxxx">
                                <?php echo form_error('no_tlp', '<small class="text-danger pl-3">', '</small>'); ?>
                            </div>

                            <!-- PERBAIKAN: Form Input Kelas -->
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Kelas Pelayanan Bus</label>
                                <!-- Dihapus: style="-webkit-appearance:none;" agar panah dropdown muncul -->
                                <select name="kelas"
                                    class="form-control form-control-lg rounded-pill px-3 shadow-sm custom-select-kios"
                                    required>
                                    <option value="">-- Pilih Kelas Pelayanan --</option>
                                    <?php if(isset($daftar_kelas)) : ?>
                                    <?php foreach($daftar_kelas as $dk) : ?>
                                    <option value="<?php echo $dk['nama_kelas']; ?>"
                                        <?php echo set_select('kelas', $dk['nama_kelas']); ?>>
                                        <?php echo $dk['nama_kelas']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <?php echo form_error('kelas', '<small class="text-danger pl-3">', '</small>'); ?>
                            </div>

                            <!-- PERBAIKAN: Form Input Tujuan -->
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Wilayah / Rute Tujuan Perjalanan</label>
                                <!-- Dihapus: style="-webkit-appearance:none;" agar panah dropdown muncul -->
                                <select name="jurusan_tujuan"
                                    class="form-control form-control-lg rounded-pill px-3 shadow-sm custom-select-kios"
                                    required>
                                    <option value="">-- Pilih Rute Tujuan Bus Anda --</option>
                                    <?php if(isset($daftar_jurusan)) : ?>
                                    <?php foreach($daftar_jurusan as $dj) : ?>
                                    <option value="<?php echo $dj['jurusan']; ?>"
                                        <?php echo set_select('jurusan_tujuan', $dj['jurusan']); ?>>
                                        <?php echo character_limiter($dj['jurusan'], 75); ?>
                                    </option>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <?php echo form_error('jurusan_tujuan', '<small class="text-danger pl-3">', '</small>'); ?>
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit"
                                    class="btn btn-primary btn-block btn-lg rounded-pill shadow font-weight-bold py-3">
                                    <i class="fas fa-ticket-alt mr-2"></i>AMBIL NOMOR ANTREAN
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- PENGGUNAAN CDN YANG BENAR -->
    <script src="<?php echo base_url('assets/admin/vendor/jquery/jquery.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/admin/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    // Jam Digital Live Header Kios
    setInterval(function() {
        let date = new Date();
        $('#kios_clock').text(date.toLocaleTimeString('id-ID'));
    }, 1000);

    // LOGIKA UNDUH GAMBAR TIKET STRUK VIA HP/PC
    $(document).on('click', '#btn_download_ticket', function() {
        const targetZone = document.getElementById('capture_ticket_zone');
        const ticketNo = $('#ticket_no_val').text();

        html2canvas(targetZone, {
            scale: 2,
            backgroundColor: "#ffffff"
        }).then(function(canvas) {
            const imageURL = canvas.toDataURL("image/png");

            const triggerLink = document.createElement('a');
            triggerLink.href = imageURL;
            triggerLink.download = 'Tiket_Antrian_SinarJaya_No_' + ticketNo + '.png';
            document.body.appendChild(triggerLink);
            triggerLink.click();
            document.body.removeChild(triggerLink);
        });
    });

    // LOGIKA SWEETALERT
    $(document).ready(function() {
        <?php if($this->session->flashdata('success')) : ?>
        Swal.fire({
            icon: 'success',
            title: 'Pendaftaran Berhasil!',
            text: '<?php echo $this->session->flashdata('success'); ?>',
            showConfirmButton: true
        });
        <?php endif; ?>
    });
    </script>
</body>

</html>
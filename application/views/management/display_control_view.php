<div class="container-fluid mt-4">
    <h1 class="h3 mb-4 text-gray-800 font-weight-bold"><i class="fas fa-sliders-h mr-2"></i><?php echo $title; ?></h1>
    <!-- FORM BARU: PENGATURAN IDENTITAS LOGO DAN HEADER TV (LEBAR PENUH) -->
    <div class="card shadow mb-4">
        <div class="card-header bg-gradient-dark text-white font-weight-bold">
            <i class="fas fa-cogs mr-2"></i>Pengaturan Identitas Header TV Monitor
        </div>
        <div class="card-body">
            <?php echo form_open_multipart('management/update_display_settings'); ?>
                <div class="row align-items-center">
                    <!-- Preview Logo Saat Ini -->
                    <div class="col-md-2 text-center border-right py-2">
                        <label class="font-weight-bold d-block text-muted small">Logo Saat Ini</label>
                        <?php if(!empty($settings['logo']) && file_exists('./assets/admin/img/upload/'.$settings['logo'])) : ?>
                            <img src="<?= base_url('assets/admin/img/upload/'.$settings['logo']); ?>" class="img-fluid rounded border p-1 shadow-2xs" style="max-height: 90px;">
                        <?php else: ?>
                            <div class="p-3 bg-light rounded text-xs border font-weight-bold text-danger">Logo Default / Teks</div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Isian Form Input -->
                    <div class="col-md-10 pl-md-4">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-dark">Ganti File Logo Perusahaan</label>
                                <input type="file" class="form-control-file border p-1 rounded bg-light" name="logo_perusahaan">
                                <small class="text-muted d-block mt-1">Format: PNG/JPG (Transparan direkomendasikan)</small>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-dark">Judul Atas Monitor TV</label>
                                <input type="text" class="form-control" name="judul_atas" value="<?= !empty($settings['judul_atas']) ? $settings['judul_atas'] : ''; ?>" required placeholder="Contoh: MONITOR INFORMASI ANTRIAN LOKET TIKET">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-dark">Teks Keterangan Detail</label>
                                <input type="text" class="form-control" name="detail_teks" value="<?= !empty($settings['detail_teks']) ? $settings['detail_teks'] : ''; ?>" required placeholder="Contoh: Kios Utama Cikedokan - Cibitung">
                            </div>
                        </div>
                        <div class="text-right mt-2">
                            <button type="submit" class="btn btn-dark shadow-sm px-4 font-weight-bold"><i class="fas fa-save mr-1"></i>Simpan Konfigurasi</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- FORM 1: INPUT UPLOAD GAMBAR SLIDER INFORMASI -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white font-weight-bold"><i class="fas fa-image mr-2"></i>Upload Gambar Slider Informasi</div>
                <div class="card-body">
                    <?php echo form_open_multipart('management/upload_slider'); ?>
                        <div class="form-group">
                            <label class="font-weight-bold">Pilih File Gambar</label>
                            <input type="file" class="form-control-file border p-2 rounded" name="gambar_slider" required>
                            <small class="text-muted d-block mt-1">Format: JPG/PNG, Maksimal Ukuran: 3 Megabytes</small>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Keterangan / Caption Gambar</label>
                            <input type="text" class="form-control" name="keterangan" required placeholder="Contoh: Jadwal Bus keberangkatan pagi rute Sumatra...">
                        </div>
                        <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-upload mr-1"></i>Mulai Upload</button>
                    </form>

                    <div class="table-responsive mt-4 border rounded">
                        <table class="table table-striped mb-0 text-center table-sm">
                            <thead class="thead-dark small">
                                <tr>
                                    <th>Preview</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php foreach($sliders as $sl) : ?>
                                <tr>
                                    <td class="align-middle"><img src="<?= base_url('assets/admin/img/upload/'.$sl['gambar']); ?>" width="80" class="rounded border"></td>
                                    <td class="align-middle font-weight-bold"><?= $sl['keterangan']; ?></td>
                                    <td class="align-middle">
                                        <!-- Tombol Sakelar On/Off Gambar -->
                                        <a href="<?= base_url('management/toggle_slider/'.$sl['id_info']); ?>" class="btn btn-sm <?= (isset($sl['is_active']) && $sl['is_active'] == 1) ? 'btn-success' : 'btn-secondary'; ?> font-weight-bold">
                                            <?= (isset($sl['is_active']) && $sl['is_active'] == 1) ? '<i class="fas fa-toggle-on mr-1"></i>On' : '<i class="fas fa-toggle-off mr-1"></i>Off'; ?>
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="<?= base_url('management/delete_slider/'.$sl['id_info']); ?>" class="btn btn-danger btn-sm btn-hapus" data-nama="Slider Gambar"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORM 2: INPUT RUNNING TEXT BERITA BERJALAN -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white font-weight-bold"><i class="fas fa-bullhorn mr-2"></i>Manajemen Berita Berjalan (Running Text)</div>
                <div class="card-body">
                    <form action="<?php echo base_url('management/add_running_text'); ?>" method="post">
                        <div class="form-group">
                            <label class="font-weight-bold">Tulis Informasi Teks Berita Baru</label>
                            <textarea class="form-control" name="berita" rows="3" required placeholder="Tulis pengumuman di sini..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success shadow-sm"><i class="fas fa-plus mr-1"></i>Tambahkan Teks</button>
                    </form>

                    <div class="table-responsive mt-4 border rounded">
                        <table class="table table-striped mb-0 small text-center">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="text-left">Isi Berita Pengumuman</th>
                                    <th width="20%">Status</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($texts as $tx) : ?>
                                <tr>
                                    <td class="font-weight-bold text-gray-800 text-left"><?= $tx['berita']; ?></td>
                                    <td class="align-middle">
                                        <!-- Tombol Sakelar On/Off Berita -->
                                        <a href="<?= base_url('management/toggle_text/'.$tx['id_text']); ?>" class="btn btn-sm <?= ($tx['is_active'] == 1) ? 'btn-success' : 'btn-secondary'; ?> font-weight-bold">
                                            <?= ($tx['is_active'] == 1) ? '<i class="fas fa-toggle-on mr-1"></i>On' : '<i class="fas fa-toggle-off mr-1"></i>Off'; ?>
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="<?= base_url('management/delete_running_text/'.$tx['id_text']); ?>" class="btn btn-danger btn-sm btn-hapus" data-nama="Teks Berita Berjalan"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

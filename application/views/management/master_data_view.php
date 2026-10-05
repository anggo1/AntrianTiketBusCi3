<div class="container-fluid mt-4">
    <h1 class="h3 mb-4 text-gray-800 font-weight-bold"><i class="fas fa-boxes mr-2 text-primary"></i><?php echo $title; ?></h1>

    <div class="row">
        <!-- ==========================================
             TABEL 1: MASTER DATA LOKET & TUJUAN JURUSAN
             ========================================== -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow" style="border-radius: 12px;">
                <div class="card-header bg-primary text-white font-weight-bold d-flex justify-content-between align-items-center" style="border-radius: 12px 12px 0 0;">
                    <span><i class="fas fa-store-alt mr-2"></i>Master Loket & Kategori Rute Tujuan</span>
                    <button class="btn btn-light btn-sm font-weight-bold text-primary shadow-sm rounded-pill px-3" data-toggle="modal" data-target="#addLoketModal">
                        <i class="fas fa-plus-circle mr-1"></i>Tambah Loket
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center mb-0 table-sm" width="100%" cellspacing="0">
                            <thead class="bg-gray-800 text-white small">
                                <tr>
                                    <th width="12%">No Loket</th>
                                    <th class="text-left">Kategori Wilayah / Rute Tujuan</th>
                                    <th width="20%">Status</th>
                                    <th width="25%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="small align-middle">
                                <?php foreach($all_loket as $lk) : ?>
                                <tr>
                                    <td class="font-weight-bold text-gray-900 align-middle">Loket <?php echo $lk['loket']; ?></td>
                                    <td class="text-left font-weight-bold text-gray-700 align-middle pl-3"><?php echo $lk['jurusan']; ?></td>
                                    <td class="align-middle">
                                        <a href="<?php echo base_url('management/toggle_status_loket/'.$lk['id_loket']); ?>" class="btn btn-sm <?php echo ($lk['status'] == 1) ? 'btn-success' : 'btn-secondary'; ?> font-weight-bold p-1 px-2 rounded-pill text-xs">
                                            <?php echo ($lk['status'] == 1) ? 'Buka' : 'Tutup'; ?>
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <!-- ==========================================
                                             TOMBOL CAPSULE GROUP LOKET (100% AMAN TANPA JS)
                                             ========================================== -->
                                        <div class="btn-group shadow-sm rounded-pill overflow-hidden" role="group">
                                            <!-- Tombol Edit langsung memanggil ID modal unik -->
                                            <button type="button" class="btn btn-warning btn-sm font-weight-bold px-3 py-1 text-white border-0" 
                                                    data-toggle="modal" data-target="#editLoketModal_<?php echo $lk['id_loket']; ?>">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </button>
                                            <a href="<?php echo base_url('management/delete_loket_master/'.$lk['id_loket']); ?>" 
                                               class="btn btn-danger btn-sm font-weight-bold px-3 py-1 border-0 btn-hapus" 
                                               data-nama="Loket <?php echo $lk['loket']; ?>">
                                                <i class="fas fa-trash mr-1"></i>Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                <!-- MODAL EDIT LOKET UNIK PER BARIS DATA (SINKRON OTOMATIS) -->
                                <div class="modal fade" id="editLoketModal_<?php echo $lk['id_loket']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content text-left" style="border-radius: 12px;">
                                            <div class="modal-header bg-warning text-white">
                                                <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Edit Master Loket <?php echo $lk['loket']; ?></h5>
                                                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                            </div>
                                            <form action="<?php echo base_url('management/edit_loket_master'); ?>" method="post">
                                                <input type="hidden" name="id_loket" value="<?php echo $lk['id_loket']; ?>">
                                                <div class="modal-body p-4">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold text-dark">Nomor Loket</label>
                                                        <input type="text" class="form-control font-weight-bold text-primary" name="nomor_loket" value="<?php echo $lk['loket']; ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="font-weight-bold text-dark">Daftar Kelompok Rute Tujuan</label>
                                                        <textarea class="form-control" name="jurusan_tujuan" rows="4" required><?php echo $lk['jurusan']; ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button class="btn btn-secondary rounded-pill px-3" type="button" data-dismiss="modal">Batal</button>
                                                    <button class="btn btn-warning text-white font-weight-bold rounded-pill px-4" type="submit"><i class="fas fa-save mr-1"></i>Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- AKHIR MODAL EDIT LOKET -->

                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             TABEL 2: MASTER DATA KELAS PELAYANAN BUS
             ========================================== -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow" style="border-radius: 12px;">
                <div class="card-header bg-success text-white font-weight-bold d-flex justify-content-between align-items-center" style="border-radius: 12px 12px 0 0;">
                    <span><i class="fas fa-bus mr-2"></i>Master Kelas Bus</span>
                    <button class="btn btn-light btn-sm font-weight-bold text-success shadow-sm rounded-pill px-3" data-toggle="modal" data-target="#addKelasModal">
                        <i class="fas fa-plus-circle mr-1"></i>Tambah Kelas
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center mb-0 table-sm" width="100%" cellspacing="0">
                            <thead class="bg-gray-800 text-white small">
                                <tr>
                                    <th>Nama Kelas Pelayanan</th>
                                    <th width="50%">Aksi</th>
                                </tr>
                            </thead>
                                                       <tbody class="small align-middle">
                                <?php if (!empty($all_kelas)) : ?>
                                    <?php foreach($all_kelas as $kl) : ?>
                                    <tr>
                                        <td class="font-weight-bold text-gray-800 align-middle text-left pl-3"><?php echo $kl['nama_kelas']; ?></td>
                                        <td class="align-middle">
                                            <!-- ==========================================
                                                 TOMBOL CAPSULE GROUP KELAS (100% AMAN TANPA JS)
                                                 ========================================== -->
                                            <div class="btn-group shadow-sm rounded-pill overflow-hidden" role="group">
                                                <button type="button" class="btn btn-warning btn-sm font-weight-bold px-3 py-1 text-white border-0" 
                                                        data-toggle="modal" data-target="#editKelasModal_<?php echo $kl['id_kelas']; ?>">
                                                    <i class="fas fa-edit mr-1"></i>Edit
                                                </button>
                                                <a href="<?php echo base_url('management/delete_kelas_master/'.$kl['id_kelas']); ?>" 
                                                   class="btn btn-danger btn-sm font-weight-bold px-3 py-1 border-0 btn-hapus" 
                                                   data-nama="<?php echo $kl['nama_kelas']; ?>">
                                                    <i class="fas fa-trash mr-1"></i>Hapus
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div> <!-- Penutup table-responsive -->
                </div> <!-- Penutup card-body -->
            </div> <!-- Penutup card shadow -->
        </div> <!-- Penutup col-lg-4 -->
    </div> <!-- Penutup row utama -->
</div> <!-- Penutup container-fluid -->

<!-- ==========================================
     STRUKTUR MODAL TAMBAH DATA (DI PERBAIKI)
     ========================================== -->
<!-- 1. Modal Tambah Loket -->
<div class="modal fade" id="addLoketModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-store-alt mr-2"></i>Tambah Master Meja Loket</h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form action="<?php echo base_url('management/add_loket_master'); ?>" method="post">
                <div class="modal-body p-4">
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Nomor Loket (Angka Teks)</label>
                        <input type="text" class="form-control" name="nomor_loket" required placeholder="Contoh: 10 atau 4A">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Daftar Kelompok Rute Tujuan (Pisahkan dengan tanda koma)</label>
                        <textarea class="form-control" name="jurusan_tujuan" rows="4" required placeholder="Contoh: Surabaya, Madura, Malang..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button class="btn btn-secondary rounded-pill px-3" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary rounded-pill px-4" type="submit"><i class="fas fa-save mr-1"></i>Simpan Loket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. Modal Tambah Kelas Bus -->
<div class="modal fade" id="addKelasModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-bus mr-2"></i>Tambah Master Kelas Bus</h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form action="<?php echo base_url('management/add_kelas_master'); ?>" method="post">
                <div class="modal-body p-4">
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Nama Kelas Pelayanan</label>
                        <input type="text" class="form-control" name="nama_kelas" required placeholder="Contoh: Super Double Decker (SDD)">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button class="btn btn-secondary rounded-pill px-3" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-success rounded-pill px-4" type="submit"><i class="fas fa-save mr-1"></i>Simpan Kelas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==========================================
     STRUKTUR MODAL EDIT DATA (DI PERBAIKI)
     ========================================== -->
<!-- 3. Loop Modal Edit Loket -->
<?php if (!empty($all_loket)) : ?>
    <?php foreach($all_loket as $lk) : ?>
    <div class="modal fade" id="editLoketModal_<?php echo $lk['id_loket']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content text-left" style="border-radius: 12px;">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Edit Master Loket <?php echo $lk['loket']; ?></h5>
                    <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <form action="<?php echo base_url('management/edit_loket_master'); ?>" method="post">
                    <input type="hidden" name="id_loket" value="<?php echo $lk['id_loket']; ?>">
                    <div class="modal-body p-4">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Nomor Loket</label>
                            <input type="text" class="form-control font-weight-bold text-primary" name="nomor_loket" value="<?php echo $lk['loket']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Daftar Kelompok Rute Tujuan</label>
                            <textarea class="form-control" name="jurusan_tujuan" rows="4" required><?php echo $lk['jurusan']; ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-secondary rounded-pill px-3" type="button" data-dismiss="modal">Batal</button>
                        <button class="btn btn-warning text-white font-weight-bold rounded-pill px-4" type="submit"><i class="fas fa-save mr-1"></i>Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- 4. Loop Modal Edit Kelas Bus -->
<?php if (!empty($all_kelas)) : ?>
    <?php foreach($all_kelas as $kl) : ?>
    <div class="modal fade" id="editKelasModal_<?php echo $kl['id_kelas']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content text-left" style="border-radius: 12px;">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Edit Master Kelas Bus</h5>
                    <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <form action="<?php echo base_url('management/edit_kelas_master'); ?>" method="post">
                    <input type="hidden" name="id_kelas" value="<?php echo $kl['id_kelas']; ?>">
                    <div class="modal-body p-4">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Nama Kelas Pelayanan</label>
                            <input type="text" class="form-control font-weight-bold text-success" name="nama_kelas" value="<?php echo $kl['nama_kelas']; ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-secondary rounded-pill px-3" type="button" data-dismiss="modal">Batal</button>
                        <button class="btn btn-warning text-white font-weight-bold rounded-pill px-4" type="submit"><i class="fas fa-save mr-1"></i>Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
     $(document).on('click', '.btn-edit-loket-master', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const loket = $(this).data('loket');
        const jurusan = $(this).data('jurusan');

        // Tembakkan isian data ke elemen form modal edit loket
        $('#form_master_id_loket').val(id);
        $('#form_master_nomor_loket').val(loket);
        $('#form_master_jurusan_tujuan').val(jurusan);

        // Paksa panggil modal popup keluar
        $('#editLoketModalMaster').modal('show');
    });
    $(document).on('click', '.btn-edit-kelas-master', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const kelas = $(this).data('kelas');

        // Tembakkan isian data ke elemen form modal edit kelas
        $('#form_master_id_kelas').val(id);
        $('#form_master_nama_kelas').val(kelas);

        // Paksa panggil modal popup keluar
        $('#editKelasModalMaster').modal('show');
    });
</script>
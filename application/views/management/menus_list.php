<div class="container-fluid mt-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo $title; ?></h1>
        <div>
            <button class="btn btn-sm btn-success shadow-sm mr-2" data-toggle="modal" data-target="#addMenuModal">
                <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Menu Utama
            </button>
            <button class="btn btn-sm btn-info shadow-sm" data-toggle="modal" data-target="#addSubMenuModal">
                <i class="fas fa-folder-plus fa-sm text-white-50"></i> Tambah Sub Menu
            </button>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Modul Navigasi Utama</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
<table class="table table-bordered table-striped" id="table_manajemen_menu" width="100%" cellspacing="0">
                    <thead class="bg-gray-100 text-dark">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Judul Menu</th>
                            <th>URL / Controller</th>
                            <th width="15%">Ikon Tampilan</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($all_menus as $m) : ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td class="font-weight-bold text-gray-800"><?php echo $m['title']; ?></td>
                            <td><code><?php echo $m['url']; ?></code></td>
                            <td>
                                <i class="<?php echo $m['icon']; ?> mr-2 text-primary"></i>
                                <small class="text-muted"><?php echo $m['icon']; ?></small>
                            </td>
                            <td class="text-center">
                                <!-- TOMBOL EDIT KUNING BARU (Mengirim data baris ke jQuery modal) -->
                                <button class="btn btn-warning btn-sm btn-edit-menu-utama"
                                        data-id="<?php echo $m['id']; ?>"
                                        data-title="<?php echo $m['title']; ?>"
                                        data-url="<?php echo $m['url']; ?>"
                                        data-icon="<?php echo $m['icon']; ?>"
                                        data-main="<?php echo isset($m['is_main_menu']) ? $m['is_main_menu'] : 1; ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>

                                <a href="<?php echo base_url('management/delete_menu/' . $m['id']); ?>" 
                                   class="btn btn-danger btn-sm btn-hapus" 
                                   data-nama="<?php echo $m['title']; ?>">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================
     MODAL POP-UP EDIT MENU UTAMA
     ========================================== -->
<div class="modal fade" id="editMenuModalUtama" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Edit Menu Utama</h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="<?php echo base_url('management/edit_menu'); ?>" method="post">
                <input type="hidden" name="id_menu" id="form_edit_id_menu">
                <div class="modal-body p-4">
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Nama Judul Menu</label>
                        <input type="text" class="form-control" name="title" id="form_edit_title" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">URL / Controller Utama</label>
                        <input type="text" class="form-control" name="url" id="form_edit_url" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Class Ikon FontAwesome</label>
                        <input type="text" class="form-control" name="icon" id="form_edit_icon" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Model Jenis Navigasi</label>
                        <select name="is_main_menu" id="form_edit_main" class="form-control" required>
        <option value="1">Menu Mandiri</option>
        <option value="0">Menu Induk Dropdown</option>
    </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-warning text-white font-weight-bold" type="submit"><i class="fas fa-save mr-1"></i>Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
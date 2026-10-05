<?php 
// Mengambil komponen data penunjang opsi select dropdown modal secara instan
$ci =& get_instance();
$roles_list = $ci->db->get('roles')->result_array();
$menus_list = $ci->db->get('menus')->result_array();
?>

<!-- ==========================================
     1. MODAL FORM ADD USER
     ========================================== -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-user-plus mr-2"></i>Tambah Pengguna Baru</h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form action="<?php echo base_url('management/add_user'); ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Nama Lengkap</label>
                        <input type="text" class="form-control" name="name" required placeholder="Masukkan nama lengkap...">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Username</label>
                        <input type="text" class="form-control" name="username" required placeholder="Masukkan username unik...">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Password</label>
                        <input type="password" class="form-control" name="password" required placeholder="Masukkan password sandi...">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Level Peran (Role)</label>
                        <select name="role_id" class="form-control" required>
                            <option value="">-- Pilih Level --</option>
                            <?php foreach($roles_list as $r) : ?>
                                <option value="<?php echo $r['id']; ?>"><?php echo $r['role_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1"></i>Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==========================================
     2. MODAL FORM ADD MENU UTAMA
     ========================================== -->
<!-- ==========================================
     2. MODAL FORM ADD MENU UTAMA (DIPERBARUI)
     ========================================== -->
<div class="modal fade" id="addMenuModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-folder-plus mr-2"></i>Tambah Menu Utama</h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form action="<?php echo base_url('management/add_menu'); ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Nama Menu Utama</label>
                        <input type="text" class="form-control" name="title" required placeholder="Contoh: Manajemen Keuangan">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">URL Menu (Nama Controller / # jika Dropdown)</label>
                        <input type="text" class="form-control" name="url" required placeholder="Contoh: billing">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Class Icon FontAwesome</label>
                        <input type="text" class="form-control" name="icon" required placeholder="Contoh: fas fa-fw fa-wallet">
                    </div>
                    
                    <!-- SELIPKAN PILIHAN BARU INI DI FORM TAMBAH MENU UTAMA -->
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Model Jenis Navigasi</label>
                        <select name="is_main_menu" class="form-control" required>
                            <option value="1">Menu Mandiri (Tunggal / Berdiri Sendiri)</option>
                            <option value="0">Menu Induk Dropdown (Menampung Sub Menu)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-success" type="submit"><i class="fas fa-plus mr-1"></i>Buat Menu</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ==========================================
     3. MODAL FORM ADD SUB MENU
     ========================================== -->
<div class="modal fade" id="addSubMenuModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-share-alt mr-2"></i>Tambah Sub Menu</h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form action="<?php echo base_url('management/add_submenu'); ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Induk Menu Utama</label>
                        <select name="menu_id" class="form-control" required>
                            <option value="">-- Pilih Induk Menu --</option>
                            <?php foreach($menus_list as $m) : ?>
                                <option value="<?php echo $m['id']; ?>"><?php echo $m['title']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Nama Sub Menu</label>
                        <input type="text" class="form-control" name="title" required placeholder="Contoh: Invoice Tagihan">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">URL Rute Sub Menu (controller/method)</label>
                        <input type="text" class="form-control" name="url" required placeholder="Contoh: billing/invoice">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Class Icon Sub Menu</label>
                        <input type="text" class="form-control" name="icon" required placeholder="Contoh: fas fa-fw fa-file-invoice">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-info" type="submit"><i class="fas fa-check mr-1"></i>Buat Sub Menu</button>
                </div>
            </form>
        </div>
    </div>
</div>

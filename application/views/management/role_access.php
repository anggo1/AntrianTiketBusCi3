<div class="container-fluid mt-4">
    <h1 class="h3 mb-4 text-gray-800"><?php echo $title; ?></h1>

    <!-- Card Filter Pencarian & Tombol Aksi -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-sliders-h mr-2"></i>Pilih Target Pengaturan Hak Akses</h6>
            
            <!-- TOMBOL BARU: MEMICU MODAL TAMBAH LEVEL -->
            <button class="btn btn-success btn-sm shadow-sm" data-toggle="modal" data-target="#addRoleModal">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Level Akses Baru
            </button>
        </div>
        <div class="card-body">
            <form method="get" action="<?php echo base_url('management/role_access'); ?>" class="form-inline">
                <div class="form-group mr-3">
                    <label for="role_id" class="mr-2 font-weight-bold">Berdasarkan Level (Role):</label>
                    <select name="role_id" id="role_id" class="form-control" onchange="if(this.value) document.getElementById('user_id').value=''">
                        <option value="">-- Pilih Level --</option>
                        <?php foreach($roles as $r) : ?>
                            <option value="<?php echo $r['id']; ?>" <?php echo ($role_id == $r['id']) ? 'selected' : ''; ?>><?php echo $r['role_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mr-3">
                    <label for="user_id" class="mr-2 font-weight-bold">ATAU Spesifik User (Override):</label>
                    <select name="user_id" id="user_id" class="form-control" onchange="if(this.value) document.getElementById('role_id').value=''">
                        <option value="">-- Pilih Nama User --</option>
                        <?php foreach($users as $u) : ?>
                            <option value="<?php echo $u['id']; ?>" <?php echo ($user_id == $u['id']) ? 'selected' : ''; ?>><?php echo $u['name']; ?> (<?php echo $u['username']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-search mr-1"></i> Buka Matriks</button>
            </form>
        </div>
    </div>

    <!-- Tabel Matriks Jalur Izin -->
    <?php if ($role_id || $user_id) : ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-light border-bottom">
            <h6 class="m-0 font-weight-bold text-dark">
                Mengatur Izin Untuk: 
                <span class="text-danger font-weight-bold">
                    <?php 
                    if($role_id) {
                        $r_name = $this->db->get_where('roles', ['id' => $role_id])->row_array();
                        echo "Seluruh Level Peran - " . $r_name['role_name'];
                    } else {
                        $u_name = $this->db->get_where('users', ['id' => $user_id])->row_array();
                        echo "Hak Khusus Individu - " . $u_name['name'];
                    }
                    ?>
                </span>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" width="100%" cellspacing="0">
                    <thead class="thead-dark text-center small text-uppercase">
                        <tr>
                            <th>Nama Menu Utama</th>
                            <th width="12%">Melihat (READ)</th>
                            <th width="12%">Tambah (CREATE)</th>
                            <th width="12%">Ubah (UPDATE)</th>
                            <th width="12%">Hapus (DELETE)</th>
                            <th width="12%">Cetak (PRINT)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($menus as $m) : ?>
                            <?php 
                            $where_check = ['menu_id' => $m['id']];
                            if($role_id) {
                                $where_check['role_id'] = $role_id;
                                $where_check['user_id'] = NULL;
                            } else {
                                $where_check['role_id'] = NULL;
                                $where_check['user_id'] = $user_id;
                            }
                            $current_access = $this->db->get_where('user_access', $where_check)->row_array();
                            ?>
                            <tr>
                                <td class="font-weight-bold text-gray-800 align-middle pl-3">
                                    <i class="<?php echo $m['icon']; ?> mr-2 text-primary"></i><?php echo $m['title']; ?>
                                </td>
                                
                                <?php 
                                $actions = ['is_read', 'is_create', 'is_update', 'is_delete', 'is_print'];
                                foreach($actions as $act) :
                                    $isChecked = (!empty($current_access) && $current_access[$act] == 1) ? 'checked' : '';
                                ?>
                                    <td class="text-center align-middle">
                                        <input type="checkbox" 
                                               class="check-access-input" 
                                               style="transform: scale(1.3); cursor:pointer;"
                                               data-menu="<?php echo $m['id']; ?>" 
                                               data-role="<?php echo $role_id; ?>" 
                                               data-user="<?php echo $user_id; ?>" 
                                               data-action="<?php echo $act; ?>" 
                                               <?php echo $isChecked; ?>>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php else: ?>
        <div class="alert alert-info shadow-sm"><i class="fas fa-info-circle mr-2"></i>Silakan pilih Level atau Nama Pengguna terlebih dahulu pada form di atas untuk menampilkan matriks hak akses.</div>
    <?php endif; ?>
</div>

<!-- ==========================================
     MODAL BARU: FORM ADD LEVEL / ROLE 
     ========================================== -->
<div class="modal fade" id="addRoleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-success text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-plus-circle mr-2"></i>Tambah Level Akses Baru</h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="<?php echo base_url('management/add_role'); ?>" method="post">
                <div class="modal-body p-4">
                    <div class="form-group">
                        <label for="role_name" class="font-weight-bold text-dark">Nama Level / Peran Baru</label>
                        <input type="text" class="form-control form-control-lg" id="role_name" name="role_name" required placeholder="Contoh: Petugas Loket 1, Supervisor, dll...">
                        <small class="text-muted mt-1 d-block"><i class="fas fa-info-circle mr-1"></i>Nama level harus unik dan tidak boleh kembar di database.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-success" type="submit"><i class="fas fa-save mr-1"></i>Simpan Level</button>
                </div>
            </form>
        </div>
    </div>
</div>

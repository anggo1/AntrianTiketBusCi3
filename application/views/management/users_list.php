<div class="container-fluid mt-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo $title; ?></h1>
        <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#addUserModal">
    <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Pengguna Baru
</button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Akun Pengguna</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead class="bg-gray-100 text-dark">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th>Level Akses (Role)</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($all_users as $u) : ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $u['name']; ?></td>
                            <td><code><?php echo $u['username']; ?></code></td>
                            <td>
                                <span class="badge <?php echo ($u['role_id'] == 1) ? 'badge-success' : 'badge-info'; ?>">
                                    <?php echo $u['role_name']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?php echo base_url('management/delete_user/' . $u['id']); ?>" 
                                   class="btn btn-danger btn-sm btn-hapus" 
                                   data-nama="<?php echo $u['name']; ?>">
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

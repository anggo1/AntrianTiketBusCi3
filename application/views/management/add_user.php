<div class="container-fluid mt-4">
    <h1 class="h3 mb-4 text-gray-800"><?php echo $title; ?></h1>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Formulir Pengguna Baru</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo base_url('management/add_user'); ?>" method="post">
                        <div class="form-group">
                            <label for="name">Nama Lengkap</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo set_value('name'); ?>">
                            <?php echo form_error('name', '<small class="text-danger">', '</small>'); ?>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo set_value('username'); ?>">
                            <?php echo form_error('username', '<small class="text-danger">', '</small>'); ?>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <?php echo form_error('password', '<small class="text-danger">', '</small>'); ?>
                        </div>
                        <div class="form-group">
                            <label for="role_id">Level / Peran (Role)</label>
                            <select name="role_id" id="role_id" class="form-control">
                                <option value="">-- Pilih Level --</option>
                                <?php foreach($roles as $r) : ?>
                                    <option value="<?php echo $r['id']; ?>"><?php echo $r['role_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php echo form_error('role_id', '<small class="text-danger">', '</small>'); ?>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan User</button>
                        <a href="<?php echo base_url('dashboard'); ?>" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

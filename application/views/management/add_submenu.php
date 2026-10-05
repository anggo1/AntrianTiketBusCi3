<div class="container-fluid mt-4">
    <h1 class="h3 mb-4 text-gray-800"><?php echo $title; ?></h1>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Formulir Sub Menu Baru</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo base_url('management/add_submenu'); ?>" method="post">
                        <div class="form-group">
                            <label for="menu_id">Induk Menu Utama</label>
                            <select name="menu_id" id="menu_id" class="form-control">
                                <option value="">-- Pilih Menu Induk --</option>
                                <?php foreach($menus as $m) : ?>
                                    <option value="<?php echo $m['id']; ?>"><?php echo $m['title']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php echo form_error('menu_id', '<small class="text-danger">', '</small>'); ?>
                        </div>
                        <div class="form-group">
                            <label for="title">Nama Sub Menu</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Contoh: Tambah User baru" value="<?php echo set_value('title'); ?>">
                            <?php echo form_error('title', '<small class="text-danger">', '</small>'); ?>
                        </div>
                        <div class="form-group">
                            <label for="url">URL Sub Menu (Format: controller/method)</label>
                            <input type="text" class="form-control" id="url" name="url" placeholder="Contoh: management/add_user" value="<?php echo set_value('url'); ?>">
                            <?php echo form_error('url', '<small class="text-danger">', '</small>'); ?>
                        </div>
                        <div class="form-group">
                            <label for="icon">Class Icon Sub Menu</label>
                            <input type="text" class="form-control" id="icon" name="icon" placeholder="Contoh: fas fa-fw fa-user-plus" value="<?php echo set_value('icon'); ?>">
                            <?php echo form_error('icon', '<small class="text-danger">', '</small>'); ?>
                        </div>
                        <button type="submit" class="btn btn-info"><i class="fas fa-folder-plus"></i> Buat Sub Menu</button>
                        <a href="<?php echo base_url('dashboard'); ?>" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

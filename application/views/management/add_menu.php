<div class="container-fluid mt-4">
    <h1 class="h3 mb-4 text-gray-800"><?php echo $title; ?></h1>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Formulir Menu Utama Baru</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo base_url('management/add_menu'); ?>" method="post">
                        <div class="form-group">
                            <label for="title">Nama Menu (Teks Tampilan)</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Contoh: Manajamen Pengguna" value="<?php echo set_value('title'); ?>">
                            <?php echo form_error('title', '<small class="text-danger">', '</small>'); ?>
                        </div>
                        <div class="form-group">
                            <label for="url">URL Menu (Nama Controller)</label>
                            <input type="text" class="form-control" id="url" name="url" placeholder="Contoh: management" value="<?php echo set_value('url'); ?>">
                            <?php echo form_error('url', '<small class="text-danger">', '</small>'); ?>
                        </div>
                        <div class="form-group">
                            <label for="icon">Class Icon FontAwesome</label>
                            <input type="text" class="form-control" id="icon" name="icon" placeholder="Contoh: fas fa-fw fa-cog" value="<?php echo set_value('icon'); ?>">
                            <?php echo form_error('icon', '<small class="text-danger">', '</small>'); ?>
                        </div>
                        <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Buat Menu</button>
                        <a href="<?php echo base_url('dashboard'); ?>" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

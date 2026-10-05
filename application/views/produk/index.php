<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Produk</h6>
            <div>
                <?php if (has_permission('is_create')) : ?>
                    <a href="<?= base_url('produk/tambah'); ?>" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Produk</a>
                <?php endif; ?>
                <?php if (has_permission('is_print')) : ?>
                    <a href="<?= base_url('produk/cetak'); ?>" class="btn btn-success btn-sm"><i class="fas fa-print"></i> Cetak Laporan</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Laptop ASUS ROG</td>
                            <td>Rp 18.000.000</td>
                            <td>
                                <?php if (has_permission('is_update')) : ?>
                                    <a href="<?= base_url('produk/ubah/1'); ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                <?php endif; ?>
                                <?php if (has_permission('is_delete')) : ?>
                                    <a href="<?= base_url('produk/hapus/1'); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin?')"><i class="fas fa-trash"></i> Hapus</a>
                                <?php endif; ?>
                                <?php if (!has_permission('is_update') && !has_permission('is_delete')) : ?>
                                    <span class="badge badge-secondary">Hanya Melihat</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

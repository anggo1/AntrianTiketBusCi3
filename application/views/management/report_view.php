<div class="container-fluid mt-4">
    <h1 class="h3 mb-4 text-gray-800 font-weight-bold"><i
            class="fas fa-file-excel mr-2 text-success"></i><?php echo $title; ?></h1>

    <!-- Card Filter Atas -->
    <div class="card shadow mb-4" style="border-radius: 12px;">
        <div class="card-header bg-gradient-primary text-white font-weight-bold" style="border-radius: 12px 12px 0 0;">
            <i class="fas fa-filter mr-2"></i>Filter Rentang Pencarian Server-Side
        </div>
        <div class="card-body bg-white">
            <form id="form_filter_report">
                <div class="row">
                    <!-- CARI DAN GANTI BAGIAN INPUT TANGGAL DI report_view.php MENJADI SEPERTI INI -->
                    <div class="col-md-3 form-group">
                        <label class="font-weight-bold text-dark">Tanggal Mulai</label>
                        <!-- Menambahkan value default tanggal hari ini -->
                        <input type="date" class="form-control rounded-pill shadow-2xs" id="tgl_mulai" name="tgl_mulai"
                            value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="font-weight-bold text-dark">Tanggal Selesai</label>
                        <!-- Menambahkan value default tanggal hari ini -->
                        <input type="date" class="form-control rounded-pill shadow-2xs" id="tgl_selesai"
                            name="tgl_selesai" value="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="col-md-3 form-group">
                        <label class="font-weight-bold text-dark">Meja Loket Panggil</label>
                        <select id="id_loket" name="id_loket" class="form-control rounded-pill shadow-2xs">
                            <option value="">-- Semua Loket --</option>
                            <?php foreach($master_loket as $ml) : ?>
                            <option value="<?php echo $ml['id_loket']; ?>">Loket <?php echo $ml['loket']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="font-weight-bold text-dark">Kelas Pelayanan</label>
                        <select id="kelas" name="kelas" class="form-control rounded-pill shadow-2xs">
                            <option value="">-- Semua Kelas Pelayanan --</option>
                            <?php foreach($master_kelas as $mk) : ?>
                            <option value="<?php echo $mk['nama_kelas']; ?>"><?php echo $mk['nama_kelas']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="text-right mt-2">
                    <div class="btn-group shadow-sm rounded-pill overflow-hidden" role="group">
                        <button type="button" id="btn_proses_filter"
                            class="btn btn-primary font-weight-bold px-4 border-0"><i
                                class="fas fa-sync mr-1"></i>Filter Data</button>
                        <button type="button" id="btn_export_excel"
                            class="btn btn-success font-weight-bold px-4 border-0"><i
                                class="fas fa-download mr-1"></i>Unduh Excel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel DataTables Server-Side -->
    <div class="card shadow mb-4" style="border-radius: 12px;">
        <div class="card-header bg-light py-3 border-bottom">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-table mr-2"></i>Log Antrian Real-Time
                (Server-Side Engine)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="table_report_serverside" class="table table-bordered table-striped text-center mb-0"
                    width="100%" cellspacing="0">
                    <thead class="bg-gray-800 text-white small">
                        <tr>
                            <th width="5%">No</th>
                            <th>Tanggal Waktu</th>
                            <th width="10%">No Antrian</th>
                            <th>Loket Panggil</th>
                            <th class="text-left">Tujuan Rute Jurusan</th>
                            <th>Nama Penumpang</th>
                            <th>No KTP</th>
                            <th>Kelas</th>
                        </tr>
                    </thead>
                    <tbody class="small text-dark">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
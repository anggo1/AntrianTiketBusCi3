            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; SJIT 2026 </span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Keluar?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Keluar dari aplikasi.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <a class="btn btn-primary" href="<?= base_url()?>auth/logout">Logout</a>
                </div>
            </div>
        </div>
    </div>



    <!-- Bootstrap core JavaScript-->
    <script src="<?= base_url()?>assets/admin/vendor/jquery/jquery.min.js"></script>
    <script src="<?= base_url()?>assets/admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?= base_url()?>assets/admin/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?= base_url()?>assets/admin/js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="<?= base_url()?>assets/admin/vendor/chart.js/Chart.min.js"></script>
    <script src="<?= base_url()?>assets/admin/vendor/sweetalert2@11.js"></script>

    <!-- Page level custom scripts 
    <script src="<?= base_url()?>assets/admin/js/demo/chart-area-demo.js"></script>
    <script src="<?= base_url()?>assets/admin/js/demo/chart-pie-demo.js"></script>-->

	 <!-- Page level plugins -->
	 <script src="<?= base_url()?>assets/admin/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="<?= base_url()?>assets/admin/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="<?= base_url()?>assets/admin/js/demo/datatables-demo.js"></script>
    

</body>
<!-- Mulai Pembatas HTML Modal Edit Profil -->
<?php 
// Mengambil data user yang sedang login secara real-time
$current_user_id = $this->session->userdata('user_id');
$logged_in_user = $this->db->get_where('users', ['id' => $current_user_id])->row_array();
?>

<div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-user-edit mr-2"></i>Edit Profil Saya</h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="<?php echo base_url('dashboard/edit_profile'); ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="modal_name" class="font-weight-bold">Nama Lengkap</label>
                        <input type="text" class="form-control" id="modal_name" name="name" value="<?php echo $logged_in_user['name']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_username" class="font-weight-bold">Username</label>
                        <input type="text" class="form-control" id="modal_username" name="username" value="<?php echo $logged_in_user['username']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_password" class="font-weight-bold">Password Baru <small class="text-muted">(Kosongkan jika tidak ingin diganti)</small></label>
                        <input type="password" class="form-control" id="modal_password" name="password" placeholder="Masukkan password baru jika ingin mengubah...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1"></i>Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Akhir HTML Modal Edit Profil -->

</html>
<?php $this->load->view('templates/modals'); ?>
<!-- Tempelkan di bagian bawah file footer.php Anda, setelah file jquery.min.js dimuat -->
<script>
$(document).ready(function() {
    // Memastikan skrip hanya berjalan jika elemen tabel report tersedia di layar
    if ($('#table_report_serverside').length > 0) {
        const tableReport = $('#table_report_serverside').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "ajax": {
                "url": "<?php echo base_url('report/get_data_server_side'); ?>",
                "type": "POST",
                "data": function(d) {
                    d.tgl_mulai   = $('#tgl_mulai').val();
                    d.tgl_selesai = $('#tgl_selesai').val();
                    d.id_loket    = $('#id_loket').val();
                    d.kelas       = $('#kelas').val();
                }
            },
            "columnDefs": [
                { "targets": 0, "orderable": false }
            ],
            "language": {
                "search": "Ketik Cari Penumpang:",
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak ada data tersedia",
                "paginate": {
                    "next": "Berikutnya",
                    "previous": "Sebelumnya"
                }
            }
        });

        // Trigger pencarian filter custom saat tombol diklik
        $('#btn_proses_filter').on('click', function() {
            tableReport.ajax.reload(); 
        });

        // Pengarah unduhan eksport berkas excel
        $('#btn_export_excel').on('click', function() {
            const queryParam = $.param({
                tgl_mulai: $('#tgl_mulai').val(),
                tgl_selesai: $('#tgl_selesai').val(),
                id_loket: $('#id_loket').val(),
                kelas: $('#kelas').val()
            });
            window.location.href = "<?php echo base_url('report/export_excel?'); ?>" + queryParam;
        });
    }
});
    
$(document).ready(function() {
    
$('.btn-edit-menu-utama').on('click', function() {
        const id = $(this).data('id');
        const title = $(this).data('title');
        const url = $(this).data('url');
        const icon = $(this).data('icon');
        const main = $(this).data('main');

        // Mengisi kolom inputan modal
        $('#form_edit_id_menu').val(id);
        $('#form_edit_title').val(title);
        $('#form_edit_url').val(url);
        $('#form_edit_icon').val(icon);
        $('#form_edit_main').val(main);

        // Munculkan modal
        $('#editMenuModalUtama').modal('show');
    });
    <?php if($this->session->flashdata('success')) : ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?php echo $this->session->flashdata('success'); ?>',
            showConfirmButton: false,
            timer: 2500
        });
        <?php $this->session->unset_userdata('success'); ?>
    <?php endif; ?>

    // Tangkap Pesan Error / Validasi Gagal (Lebih Aman & Menggunakan Template HTML)
    <?php if($this->session->flashdata('error')) : ?>
        Swal.fire({
            icon: 'error',
            title: 'Peringatan / Gagal!',
            html: '<?php echo trim(preg_replace('/\s+/', ' ', $this->session->flashdata('error'))); ?>',
            showConfirmButton: true
        });
    <?php endif; ?>


    // ==========================================
    // 2. MODUL AJAX MATRIKS HAK AKSES REAL-TIME
    // ==========================================
    $('.check-access-input').on('click', function() {
        const menuId = $(this).data('menu');
        const roleId = $(this).data('role');
        const userId = $(this).data('user');
        const action = $(this).data('action');
        const checked = $(this).is(':checked') ? 1 : 0; 

        $.ajax({
            url: "<?php echo base_url('management/change_access'); ?>",
            type: 'post',
            data: {
                menuId: menuId,
                roleId: roleId,
                userId: userId,
                action: action,
                checked: checked
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Notifikasi melayang kecil di pojok kanan atas (Toast)
                    Swal.fire({
                        icon: 'success',
                        title: response.message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'System Error',
                    text: 'Gagal terhubung ke server backend!'
                });
            }
        });
    });


    // ==========================================
    // 3. KONFIRMASI INTERAKTIF HAPUS DATA PERMANEN
    // ==========================================
    $('.btn-hapus').on('click', function(e) {
        e.preventDefault(); 
        const href = $(this).attr('href');
        const namaData = $(this).data('nama');

        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: "Data \"" + namaData + "\" akan dihapus permanen dari sistem!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b', 
            cancelButtonColor: '#858796',  
            confirmButtonText: 'Ya, Hapus Data!',
            cancelButtonText: 'Batalkan'
        }).then((result) => {
            if (result.isConfirmed) {
                document.location.href = href; 
            }
        });
    });

});
// A. LOGIKA MATEMATIKA MEMECAH ANGKA MENJADI ARRAY KATA (TERBILANG INDONESIA)
function terbilangArray(angka) {
    angka = parseInt(angka);
    let kata = [];
    
    if (angka < 12) {
        kata.push(angka.toString());
    } else if (angka < 20) {
        kata.push((angka - 10).toString());
        kata.push("belas");
    } else if (angka < 100) {
        kata.push(Math.floor(angka / 10).toString());
        kata.push("puluh");
        if (angka % 10 !== 0) kata.push((angka % 10).toString());
    } else if (angka < 200) {
        kata.push("seratus");
        if (angka % 100 !== 0) kata = kata.concat(terbilangArray(angka % 100));
    } else if (angka < 1000) {
        kata.push(Math.floor(angka / 100).toString());
        kata.push("ratus");
        if (angka % 100 !== 0) kata = kata.concat(terbilangArray(angka % 100));
    }
    return kata;
}

// B. FUNGSI UTAMA OFFLINE PLAYLIST GENERATOR (GLOBAL FUNCTION)
function playQueueSoundOffline(nomorAntrian, namaLoket) {
    const basePath = "<?php echo base_url('assets/admin/audio/'); ?>";
    let playlist = [];

    // 1. Masukkan suara Bel Lonceng Bandara
    playlist.push(basePath + "Airport_Bell.mp3");
    playlist.push(basePath + "nomor_antrian.mp3");

    // 2. Masukkan rangkaian pecahan kata angka (Mendukung nomor 1 s.d 999)
    let pecahanNomor = terbilangArray(nomorAntrian);
    pecahanNomor.forEach(function(item) {
        playlist.push(basePath + item + ".mp3");
    });

    // 3. Masukkan suara arah loket tujuan
    playlist.push(basePath + "silahkan_menuju_ke.mp3");
    playlist.push(basePath + "loket.mp3");
    
    // Ambil angka loketnya saja (Misal: "Loket 3" diambil angka "3")
    let nomorLoketHanyaAngka = namaLoket.replace(/^\D+/g, ''); 
    playlist.push(basePath + nomorLoketHanyaAngka + ".mp3");

    // 4. EKSEKUSI CHAIN-PLAYING (Memutar satu per satu setelah file sebelumnya selesai)
    let currentTrackIndex = 0;

    function playNextTrack() {
        if (currentTrackIndex < playlist.length) {
            let currentAudio = new Audio(playlist[currentTrackIndex]);
            currentAudio.volume = 1.0; // Volume maksimal penuh nyaring
            
            currentAudio.play().catch(function(err) {
                console.log("File audio tidak ditemukan atau diblokir: " + playlist[currentTrackIndex]);
                // Jika file mp3 ada yang kurang, otomatis lompat ke kata berikutnya agar tidak macet
                currentTrackIndex++;
                playNextTrack();
            });
            
            // Event Listener bawaan HTML5: Jika audio selesai berbunyi, langsung sambung track berikutnya
            currentAudio.onended = function() {
                currentTrackIndex++;
                playNextTrack();
            };
        }
    }

    // Mulai jalankan track pertama
    playNextTrack();
}

// C. PENANGKAP EVENT OPERASIONAL DESKTOP KASIR
$(document).ready(function() {
    
    // Ambil data nomor dan loket aktif saat ini dari Controller via PHP
    const nomorSekarang = "<?php echo !empty($current_data) ? $current_data['no_antrian'] : 0; ?>";
    const loketSekarang = "<?php echo isset($loket_name) ? $loket_name : '1'; ?>";

    // Pemicu otomatis begitu operator klik "Antrian Selanjutnya" / Cari Nomor
    <?php if($this->session->flashdata('success')) : ?>
        if(parseInt(nomorSekarang) > 0) {
            // Beri jeda sekejap 600ms setelah halaman selesai dirender sempurna, lalu bunyikan suara offline
            setTimeout(function() {
                playQueueSoundOffline(nomorSekarang, loketSekarang);
            }, 600);
        }
    <?php endif; ?>

    // Jam Digital Live Header Atas
    setInterval(function() {
        let date = new Date();
        $('#tv_clock').text(date.toLocaleTimeString('id-ID'));
    }, 1000);

});
$(document).ready(function() {

    // ====================================================================
    // INTERKONEKSI: MENGHUBUNGKAN INPUT NAVBAR.PHP DENGAN TABEL MANAJEMEN MENU
    // ====================================================================
    if ($('#table_manajemen_menu').length > 0) {
        // Inisialisasi DataTables untuk tabel menu
        const tableMenu = $('#table_manajemen_menu').DataTable({
            "dom": 'lrtip', // KUNCI: Menyembunyikan kotak input pencarian bawaan datatables agar tidak ganda
            "language": {
                "zeroRecords": "Menu tidak ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "paginate": {
                    "next": "Berikutnya",
                    "previous": "Sebelumnya"
                }
            }
        });

        // Pengintai aktivitas ketikan pada kelas global-navbar-search di file navbar.php
        $('.global-navbar-search').on('keyup', function() {
            tableMenu.search(this.value).draw(); // Eksekusi penyaringan live search otomatis
        });
    }

});

</script>





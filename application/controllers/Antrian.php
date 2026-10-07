<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Antrian extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('session');
    }
    public function index() {
        // KUNCI ZONA WAKTU JAKARTA INDONESIA (WIB)
        date_default_timezone_set('Asia/Jakarta');
        $this->load->helper('text');
        $hari_ini = date('Ymd');
        
        // ====================================================================
        // KUNCI SAKTI: HAPUS FLASHDATA JIKA USER MENGKLIK LINK RESET / KEMBALI
        // ====================================================================
        if ($this->input->get('action') == 'reset') {
            $this->session->unset_userdata('ticket_success');
            redirect(base_url('antrian'));
            exit();
        }
        
        // 1. Ambil data konfigurasi display logo dan judul secara real-time dari database
        $data['display_setup'] = $this->db->get_where('display_settings', ['id_setting' => 1])->row_array();

        // 2. Mengambil kelompok JURUSAN/TUJUAN dari tabel loket secara keseluruhan (Tanpa melihat loket aktif)
        $this->db->select('jurusan');
        $this->db->from('loket');
        $this->db->group_by('jurusan');
        $data['daftar_jurusan'] = $this->db->get()->result_array();

        // 3. Mengambil seluruh daftar KELAS dari tabel master_kelas
        $data['daftar_kelas'] = $this->db->get('master_kelas')->result_array();

        // 4. Hitung jumlah seluruh orang yang sedang mengantre global hari ini
        $data['total_waiting'] = $this->db->get_where('transaksi', [
            'tgl' => $hari_ini,
            'id_loket' => 0
        ])->num_rows();

        // 5. Ambil nomor antrean tertinggi yang sudah dipanggil hari ini
        $this->db->select_max('no_antrian');
        $this->db->where('tgl', $hari_ini);
        $this->db->where('id_loket >', 0);
        $query_panggil = $this->db->get('transaksi')->row_array();
        $data['called_number'] = ($query_panggil['no_antrian'] != NULL) ? $query_panggil['no_antrian'] : 0;

        // Validasi isian formulir
        $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim');
        $this->form_validation->set_rules('no_ktp', 'No KTP', 'required|trim|numeric');
        $this->form_validation->set_rules('no_tlp', 'No Telefon', 'required|trim');
        $this->form_validation->set_rules('kelas', 'Kelas Bus', 'required');
        $this->form_validation->set_rules('jurusan_tujuan', 'Tujuan Perjalanan', 'required');

        if ($this->form_validation->run() == FALSE) {
            // Jika form belum diisi / validasi gagal, tampilkan form
            $this->load->view('antrian/index', $data);
        } else {
            // Jika validasi sukses, buat tiket
            $this->_generate_ticket();
        }
    }
        public function index_sebelumnya() {
        date_default_timezone_set('Asia/Jakarta');
        $this->load->helper('text');
        $hari_ini = date('Ymd');
        
        // ====================================================================
        // KUNCI SAKTI: HAPUS FLASHDATA JIKA USER MENGKLIK LINK RESET / KEMBALI
        // ====================================================================
        if ($this->input->get('action') == 'reset') {
            $this->session->unset_userdata('ticket_success');
            redirect(base_url('antrian'));
            exit();
        }
        
        // 1. Ambil data konfigurasi display logo dan judul secara real-time dari database
        $data['display_setup'] = $this->db->get_where('display_settings', ['id_setting' => 1])->row_array();

        // 2. Mengambil kelompok JURUSAN/TUJUAN dari tabel loket yang aktif
        $this->db->select('jurusan');
        $this->db->from('loket');
        //$this->db->where('status', 1);
        $this->db->group_by('jurusan');
        $data['daftar_jurusan'] = $this->db->get()->result_array();

        // 3. Mengambil seluruh daftar KELAS dari tabel master_kelas
        $data['daftar_kelas'] = $this->db->get('master_kelas')->result_array();

        // 4. Hitung jumlah seluruh orang yang sedang mengantre global hari ini
        $data['total_waiting'] = $this->db->get_where('transaksi', [
            'tgl' => $hari_ini,
            'id_loket' => 0
        ])->num_rows();

        // 5. Ambil nomor antrean tertinggi yang sudah dipanggil hari ini
        $this->db->select_max('no_antrian');
        $this->db->where('tgl', $hari_ini);
        $this->db->where('id_loket >', 0);
        $query_panggil = $this->db->get('transaksi')->row_array();
        $data['called_number'] = ($query_panggil['no_antrian'] != NULL) ? $query_panggil['no_antrian'] : 0;

        // Validasi isian formulir
        $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim');
        $this->form_validation->set_rules('no_ktp', 'No KTP', 'required|trim|numeric');
        $this->form_validation->set_rules('no_tlp', 'No Telefon', 'required|trim');
        $this->form_validation->set_rules('kelas', 'Kelas Bus', 'required');
        $this->form_validation->set_rules('jurusan_tujuan', 'Tujuan Perjalanan', 'required');

        if ($this->form_validation->run() == FALSE) {
            // Jika form belum diisi / validasi gagal, tampilkan form
            $this->load->view('antrian/index', $data);
        } else {
            // Jika validasi sukses, buat tiket
            $this->_generate_ticket();
        }
    }


// Method tambahan untuk mengantisipasi jika form mengarah langsung ke fungsi /daftar
public function daftar() {
    // Validasi ulang singkat jika diakses via route /daftar
    $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim');
    $this->form_validation->set_rules('no_ktp', 'No KTP', 'required|trim|numeric');
    $this->form_validation->set_rules('no_tlp', 'No Telefon', 'required|trim');
    $this->form_validation->set_rules('kelas', 'Kelas Bus', 'required');
    $this->form_validation->set_rules('jurusan_tujuan', 'Tujuan Perjalanan', 'required');

    if ($this->form_validation->run() == FALSE) {
        redirect(base_url('antrian'));
    } else {
        $this->_generate_ticket();
    }
}
        private function _generate_ticket() {
        date_default_timezone_set('Asia/Jakarta');
        $hari_ini = date('Ymd');
        $pilihan_jurusan = $this->input->post('jurusan_tujuan', true);

        // 1. Ambil nomor meja loket berdasarkan rute pilihan
        $this->db->select('id_loket, loket');
        $this->db->from('loket');
        $this->db->where("REPLACE(jurusan, ' ', '') = REPLACE('".$pilihan_jurusan."', ' ', '')");
        $loket_match = $this->db->get()->row_array();
        
        $nomor_loket_tujuan = (!empty($loket_match)) ? $loket_match['loket'] : '1';
        $id_loket_target    = (!empty($loket_match)) ? $loket_match['id_loket'] : 0;

        // ====================================================================
        // KUNCI SAKTI BARU: Hitung urutan baris loket ini dari seluruh daftar loket
        // Ini menjamin loket ke-9 PASTI mendapat huruf 'I', bukan 'J'
        // ====================================================================
        $this->db->select('id_loket');
        $this->db->from('loket');
        $this->db->order_by('CAST(loket AS UNSIGNED)', 'ASC');
        $daftar_seluruh_loket = $this->db->get()->result_array();

        $posisi_urutan_abjad = 1; // Default urutan pertama (A)
        foreach ($daftar_seluruh_loket as $index => $lkt) {
            if ($lkt['id_loket'] == $id_loket_target) {
                $posisi_urutan_abjad = $index + 1; // Index dimulai dari 0, maka ditambah 1
                break;
            }
        }

        // Konversi posisi urutan murni ke abjad huruf (1=A, 2=B, ..., 9=I, 10=J)
        $prefix = chr(64 + $posisi_urutan_abjad); 

        // 2. Hitung nomor urut tertinggi khusus untuk rute ini hari ini
        $this->db->select_max('no_antrian');
        $this->db->where('tgl', $hari_ini);
        $this->db->where('tujuan', $pilihan_jurusan);
        $query = $this->db->get('transaksi')->row_array();
        
        $next_number = ($query['no_antrian'] != NULL) ? $query['no_antrian'] + 1 : 1;

        $insert_data = [
            'no_antrian' => $next_number,
            'id_loket'   => 0,
            'username'   => NULL,
            'tgl'        => $hari_ini,
            'nama'       => $this->input->post('nama', true),
            'no_ktp'     => $this->input->post('no_ktp', true),
            'no_tlp'     => $this->input->post('no_tlp', true),
            'kelas'      => $this->input->post('kelas', true),
            'tujuan'     => $pilihan_jurusan
        ];
        $this->db->insert('transaksi', $insert_data);

        // 3. Gabungkan Huruf + Angka Pad (Contoh: I-001 untuk loket ke-9)
        $no_antrian_lengkap = $prefix . '-' . str_pad($next_number, 3, '0', STR_PAD_LEFT);

        $ticket_session = [
            'no_antrian'  => $no_antrian_lengkap, 
            'nama'        => $insert_data['nama'],
            'tujuan'      => $insert_data['tujuan'],
            'kelas'       => $insert_data['kelas'],
            'nomor_loket' => $nomor_loket_tujuan,
            'waktu'       => date('d-m-Y H:i:s') . ' WIB'
        ];
        
        $this->session->set_flashdata('ticket_success', $ticket_session);
        redirect(base_url('antrian'));
    }


    
    private function _generate_ticket_sebelumnya() {
        $hari_ini = date('Ymd');
        $pilihan_jurusan = $this->input->post('jurusan_tujuan', true);

        // KUNCI UTAMA 1: Hitung nomor tertinggi khusus untuk JURUSAN YANG DIPILIH HARI INI
        $this->db->select_max('no_antrian');
        $this->db->where('tgl', $hari_ini);
        $this->db->where('tujuan', $pilihan_jurusan); // Mengunci filter per rute tujuan
        $query = $this->db->get('transaksi')->row_array();
        
        // Nomor antrean otomatis mereset dari 1 khusus rute ini jika hari berganti
        $next_number = ($query['no_antrian'] != NULL) ? $query['no_antrian'] + 1 : 1;

        $insert_data = [
            'no_antrian' => $next_number,
            'id_loket'   => 0, // 0 = Status masih menunggu antrean
            'username'   => NULL,
            'tgl'        => $hari_ini,
            'nama'       => $this->input->post('nama', true),
            'no_ktp'     => $this->input->post('no_ktp', true),
            'no_tlp'     => $this->input->post('no_tlp', true),
            'kelas'      => $this->input->post('kelas', true),
            'tujuan'     => $pilihan_jurusan 
        ];

        $this->db->insert('transaksi', $insert_data);

        $ticket_session = [
            'no_antrian' => $next_number,
            'nama'       => $insert_data['nama'],
            'tujuan'     => $insert_data['tujuan'],
            'kelas'      => $insert_data['kelas'],
            'waktu'      => date('d-m-Y H:i:s')
        ];
        
        $this->session->set_flashdata('ticket_success', $ticket_session);
        redirect(base_url('antrian'));
    }

        // --- HALAMAN DISPLAY UTAMA MONITOR TV BESAR ---
        public function display_screen() {
        $data['title'] = 'Monitor Antrian Sinar Jaya Group';
        
        // Perbaikan: Hanya mengambil gambar slider yang berstatus AKTIF (is_active = 1)
        // Jalankan perintah ALTER TABLE di phpMyAdmin terlebih dahulu
        $data['sliders'] = $this->db->get_where('display_info', ['is_active' => 1])->result_array();
        
        // Perbaikan: Hanya mengambil teks berita berjalan yang aktif (is_active = 1)
        $data['running_text'] = $this->db->get_where('running_text', ['is_active' => 1])->result_array();

        $this->load->view('antrian/display_view', $data);
    }


    // --- ENDPOINT DATA AJAX REAL-TIME UNTUK TV (DIPANGGIL JQUERY 3 DETIK SEKALI) ---
        public function get_live_display() {
        $hari_ini = date('Ymd');

        // ====================================================================
        // KUNCI SAKTI: Kembalikan filter status 0 (Hanya panggil loket yang aktif dinas)
        // ====================================================================
        $this->db->where('status', 0); 
        $this->db->order_by('CAST(loket AS UNSIGNED)', 'ASC');
        $loket_aktif = $this->db->get('loket')->result_array();

        // Jika tidak ada loket aktif sama sekali, langsung kirim array kosong agar TV merespon info standby
        if (empty($loket_aktif)) {
            echo json_encode([]);
            exit();
        }

        foreach ($loket_aktif as $key => $lk) {
            // Ambil nomor antrean terakhir yang berhasil dipanggil oleh loket ini hari ini
            $this->db->select('no_antrian, tgl_waktu');
            $this->db->from('transaksi');
            $this->db->where('tgl', $hari_ini);
            $this->db->where('id_loket', $lk['id_loket']);
            $this->db->order_by('tgl_waktu', 'DESC');
            $this->db->limit(1);
            $last_call = $this->db->get()->row_array();

            // Hitung ranking urutan alfabet dinamis murni dari baris loket yang AKTIF SAJA
            $posisi_urutan_abjad = $key + 1; 
            $prefix = chr(64 + $posisi_urutan_abjad); 

            if (!empty($last_call)) {
                $loket_aktif[$key]['no_sekarang'] = $prefix . '-' . str_pad($last_call['no_antrian'], 3, '0', STR_PAD_LEFT);
                $loket_aktif[$key]['waktu_panggil'] = $last_call['tgl_waktu'];
            } else {
                // Jika kasir aktif login tapi belum menekan tombol panggil urut pertama
                $loket_aktif[$key]['no_sekarang'] = $prefix . '-000';
                $loket_aktif[$key]['waktu_panggil'] = '';
            }
            
            $loket_aktif[$key]['rute_tujuan'] = !empty($lk['jurusan']) ? $lk['jurusan'] : 'Semua Rute';
        }

        echo json_encode($loket_aktif);
    }




        public function get_live_display_test() {
        // ====================================================================
        // MODE COBA/SIMULASI: Menyuntikkan 6 Loket Aktif Sekaligus Secara Instan
        // ====================================================================
        $simulasi_loket = [
            [
                'id_loket' => 1, 'loket' => '1', 'no_sekarang' => '024',
                'rute_tujuan' => 'SURABAYA, SEMARANG, SOLO, YOGYAKARTA, KLATEN, BOYOLALI'
            ],
            [
                'id_loket' => 2, 'loket' => '2', 'no_sekarang' => '115',
                'rute_tujuan' => 'MADURA, BANGKALAN, SAMPANG, PAMEKASAN, SUMENEP'
            ],
            [
                'id_loket' => 3, 'loket' => '3', 'no_sekarang' => '008',
                'rute_tujuan' => 'MALANG, BLITAR, KEDIRI, TULUNGAGUNG, TRENGGALEK'
            ],
            [
                'id_loket' => 4, 'loket' => '4', 'no_sekarang' => '201',
                'rute_tujuan' => 'PURWOKERTO, BANYUMAS, PURBALINGGA, BANJARNEGARA, WONOSOBO'
            ],
            [
                'id_loket' => 5, 'loket' => '5', 'no_sekarang' => '042',
                'rute_tujuan' => 'PEKALONGAN, BATANG, PEMALANG, TEGAL, BREBES, SLAVI'
            ],
            [
                'id_loket' => 6, 'loket' => '6', 'no_sekarang' => '077',
                'rute_tujuan' => 'CILACAP, KEBUMEN, PURWOREJO, KARANGANYAR, SRAGEN'
            ],
            [
                'id_loket' => 6, 'loket' => '6', 'no_sekarang' => '077',
                'rute_tujuan' => 'CILACAP, KEBUMEN, PURWOREJO, KARANGANYAR, SRAGEN'
            ],
            [
                'id_loket' => 6, 'loket' => '6', 'no_sekarang' => '077',
                'rute_tujuan' => 'CILACAP, KEBUMEN, PURWOREJO, KARANGANYAR, SRAGEN'
            ],
            [
                'id_loket' => 6, 'loket' => '6', 'no_sekarang' => '077',
                'rute_tujuan' => 'CILACAP, KEBUMEN, PURWOREJO, KARANGANYAR, SRAGEN'
            ]
        ];

        // Tembakkan langsung dalam format JSON ke browser
        echo json_encode($simulasi_loket);
    }

        public function get_live_display1() {
        $hari_ini = date('Ymd');

        // Tarik semua master data loket yang terdaftar
        $this->db->order_by('CAST(loket AS UNSIGNED)', 'ASC');
        $all_loket = $this->db->get('loket')->result_array();

        foreach ($all_loket as $key => $lk) {
            // Ambil nomor antrean terakhir yang berhasil dipanggil oleh loket ini hari ini
            $this->db->select('no_antrian');
            $this->db->from('transaksi');
            $this->db->where('tgl', $hari_ini);
            $this->db->where('id_loket', $lk['id_loket']);
            $this->db->order_by('tgl_waktu', 'DESC');
            $this->db->limit(1);
            $last_call = $this->db->get()->row_array();

            // Jika belum ada aktivitas panggilan hari ini, berikan tanda strip (-)
            $all_loket[$key]['no_sekarang'] = (!empty($last_call)) ? $last_call['no_antrian'] : '-';
            
            // Ambil data kelompok jurusan aslinya langsung dari database loket
            $all_loket[$key]['rute_tujuan'] = !empty($lk['jurusan']) ? $lk['jurusan'] : 'Semua Rute';
        }

        echo json_encode($all_loket);
    }



}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Antrian extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('session');
    }

        public function index() {
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
        $this->db->where('status', 1);
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
    $hari_ini = date('Ymd');
    $pilihan_jurusan = $this->input->post('jurusan_tujuan', true);

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

        // Tarik semua loket yang aktif memanggil
        $this->db->where('status', 0); // status 0 berarti sedang aktif melayani/dipakai petugas
        $this->db->order_by('CAST(loket AS UNSIGNED)', 'ASC');
        $loket_aktif = $this->db->get('loket')->result_array();

        foreach ($loket_aktif as $key => $lk) {
            $this->db->select('no_antrian');
            $this->db->from('transaksi');
            $this->db->where('tgl', $hari_ini);
            $this->db->where('id_loket', $lk['id_loket']);
            $this->db->order_by('tgl_waktu', 'DESC');
            $this->db->limit(1);
            $last_call = $this->db->get()->row_array();

            $loket_aktif[$key]['no_sekarang'] = (!empty($last_call)) ? $last_call['no_antrian'] : '0';
        }

        echo json_encode($loket_aktif);
    }

}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Petugas extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('access');
        $this->load->helper('text'); // Helper pembatas teks
        check_menu_access();
    }

    // --- KONSOL MEJA PANGGIL DENGAN PENYELARASAN DATA PENCARIAN ---
        public function meja_panggil() {
        $data['title'] = 'Meja Konsol Kasir';
        $hari_ini = date('Ymd');
        $id_loket_dinas = $this->session->userdata('loket_id');
        
        if (empty($id_loket_dinas)) {
            redirect(base_url('auth/pilih_loket'));
            exit();
        }

        $loket_info = $this->db->get_where('loket', ['id_loket' => $id_loket_dinas])->row_array();
        $data['id_loket_aktif'] = $id_loket_dinas;
        $data['loket_name'] = "Loket " . (!empty($loket_info) ? $loket_info['loket'] : '1');
        $data['jurusan_loket'] = !empty($loket_info) ? $loket_info['jurusan'] : '';

        // Tangkap nomor pencarian spesifik jika ada
        $search_number = $this->session->flashdata('search_number');

        if (!empty($search_number)) {
            $this->db->where('tgl', $hari_ini);
            $this->db->where('no_antrian', $search_number);
            $this->db->where("REPLACE(tujuan, ' ', '') = REPLACE('".$data['jurusan_loket']."', ' ', '')");
            $data['current_data'] = $this->db->get('transaksi')->row_array();
            
            $this->session->set_flashdata('success', 'Memanggil nomor: ' . $search_number);
        } else {
            $this->db->select('*');
            $this->db->from('transaksi');
            $this->db->where('tgl', $hari_ini);
            $this->db->where('id_loket', $id_loket_dinas);
            $this->db->order_by('tgl_waktu', 'DESC');
            $data['current_data'] = $this->db->get()->row_array();
        }

        $data['total_waiting'] = $this->db->get_where('transaksi', [
            'tgl' => $hari_ini,
            'tujuan' => $data['jurusan_loket'],
            'id_loket' => 0
        ])->num_rows();

        // ====================================================================
        // KUNCI UTAMA NYA DI SINI:
        // Panggil views tampulan, lalu paksa hapus flashdata saat itu juga
        // agar tidak tersangkut berulang-ulang ketika halaman di-refresh kasir.
        // ====================================================================
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('templates/navbar');
        $this->load->view('petugas/meja_panggil_view', $data);
        $this->load->view('templates/footer');

        // Perintah menghapus jejak flashdata sukses secara paksa di background
        $this->session->unset_userdata('success');
    }


    // --- PROSES PENCARIAN & UPDATE DATA ANTREAN ---
    public function panggil_spesifik() {
        $hari_ini = date('Ymd');
        $id_loket_dinas = $this->session->userdata('loket_id');
        $no_cari = $this->input->post('no_antrian', true);

        $loket_info = $this->db->get_where('loket', ['id_loket' => $id_loket_dinas])->row_array();
        $jurusan_loket = $loket_info['jurusan'];

        if (empty($no_cari) || !is_numeric($no_cari)) {
            $this->session->set_flashdata('error', 'Masukkan nomor antrean yang valid!');
            redirect(base_url('petugas/meja_panggil'));
            exit();
        }

        // Cari nomor antrean di database
        $this->db->where('tgl', $hari_ini);
        $this->db->where('no_antrian', $no_cari);
        $this->db->where("REPLACE(tujuan, ' ', '') = REPLACE('".$jurusan_loket."', ' ', '')");
        $cek_transaksi = $this->db->get('transaksi')->row_array();

        if ($cek_transaksi) {
            // Update status kepemilikan nomor antrean ke loket Anda saat ini
            $this->db->where('id_transaksi', $cek_transaksi['id_transaksi']);
            $this->db->update('transaksi', [
                'id_loket' => $id_loket_dinas,
                'username' => $this->session->userdata('username')
            ]);

            // KUNCI UTAMA: Kirim nomor pencarian ke flashdata agar ditangkap method meja_panggil()
            $this->session->set_flashdata('search_number', $no_cari);
        } else {
            $this->session->set_flashdata('error', 'Nomor Antrean ' . $no_cari . ' tidak ditemukan untuk rute Anda!');
        }

        redirect(base_url('petugas/meja_panggil'));
        exit();
    }

    // --- PROSES TOMBOL ANTRIAN SELANJUTNYA ---
    public function panggil_berikutnya($id_loket) {
        $hari_ini = date('Ymd');
        $loket_info = $this->db->get_where('loket', ['id_loket' => $id_loket])->row_array();

        $this->db->order_by('no_antrian', 'ASC');
        $this->db->where('tgl', $hari_ini);
        $this->db->where('id_loket', 0);
        $this->db->where("REPLACE(tujuan, ' ', '') = REPLACE('".$loket_info['jurusan']."', ' ', '')");
        $next_queue = $this->db->get('transaksi')->row_array();

        if ($next_queue) {
            $this->db->where('id_transaksi', $next_queue['id_transaksi']);
            $this->db->update('transaksi', [
                'id_loket' => $id_loket,
                'username' => $this->session->userdata('username')
            ]);
            $this->session->set_flashdata('success', 'Memanggil nomor: ' . $next_queue['no_antrian']);
        } else {
            $this->session->set_flashdata('error', 'Antrean rute ini sudah habis untuk hari ini!');
        }

        redirect(base_url('petugas/meja_panggil'));
        exit();
    }
}

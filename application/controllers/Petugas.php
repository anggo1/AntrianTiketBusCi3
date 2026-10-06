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

        public function meja_panggil() {
        $data['title'] = 'Meja Konsol Kasir';
        $hari_ini = date('Ymd');
        $id_loket_dinas = $this->session->userdata('loket_id');
        
        if (empty($id_loket_dinas)) {
            redirect(base_url('auth/pilih_loket'));
            exit();
        }

        // Ambil info detail rute jurusan yang dipegang oleh nomor loket ini
        $loket_info = $this->db->get_where('loket', ['id_loket' => $id_loket_dinas])->row_array();
        $data['id_loket_aktif'] = $id_loket_dinas;
        $data['loket_name'] = "Loket " . (!empty($loket_info) ? $loket_info['loket'] : '1');
        $data['jurusan_loket'] = !empty($loket_info) ? $loket_info['jurusan'] : '';

        // --- AKSI PANGGIL TOMBOL "ANTRIAN SELANJUTNYA" JIKA DIPICU ---
        if ($this->input->get('action') == 'next') {
            // Ambil 1 nomor antrean terlama yang statusnya masih menunggu (id_loket = 0) KHUSUS JURUSAN LOKET INI
            $this->db->select('*');
            $this->db->from('transaksi');
            $this->db->where('tgl', $hari_ini);
            $this->db->where('id_loket', 0); 
            $this->db->where("REPLACE(tujuan, ' ', '') = REPLACE('".$data['jurusan_loket']."', ' ', '')");
            $this->db->order_by('id_transaksi', 'ASC'); // FIFO (First In First Out)
            $this->db->limit(1);
            $next_queue = $this->db->get()->row_array();

            if (!empty($next_queue)) {
                // Update status transaksi: tandai bahwa nomor ini diambil oleh loket dinas ini
                $this->db->where('id_transaksi', $next_queue['id_transaksi']);
                $this->db->update('transaksi', [
                    'id_loket' => $id_loket_dinas,
                    'username' => $this->session->userdata('username')
                ]);
                $this->session->set_flashdata('success', 'Berhasil memanggil nomor antrean ' . $next_queue['no_antrian']);
            } else {
                $this->session->set_flashdata('error', 'Antrean khusus rute ' . $data['jurusan_loket'] . ' sudah habis!');
            }
            redirect(base_url('petugas/meja_panggil'));
            exit();
        }

        // Ambil data antrean yang sedang aktif dilayani loket ini sekarang (terakhir dipanggil)
        $this->db->select('*');
        $this->db->from('transaksi');
        $this->db->where('tgl', $hari_ini);
        $this->db->where('id_loket', $id_loket_dinas);
        $this->db->order_by('tgl_waktu', 'DESC');
        $data['current_data'] = $this->db->get()->row_array();

        // ====================================================================
        // KUNCI UTAMA 2: HITUNG TOTAL & SISA ANTREAN SPESIFIK JURUSAN LOKET INI
        // ====================================================================
        
        // 1. TOTAL ANTRIAN: Menghitung semua orang yang mendaftar di jurusan ini hari ini
        $this->db->from('transaksi');
        $this->db->where('tgl', $hari_ini);
        $this->db->where("REPLACE(tujuan, ' ', '') = REPLACE('".$data['jurusan_loket']."', ' ', '')");
        $data['total_antrian_jurusan'] = $this->db->get()->num_rows();

        // 2. SISA ANTRIAN: Menghitung orang di jurusan ini yang id_loket-nya MASIH 0 (Belum Terpanggil)
        $this->db->from('transaksi');
        $this->db->where('tgl', $hari_ini);
        $this->db->where('id_loket', 0);
        $this->db->where("REPLACE(tujuan, ' ', '') = REPLACE('".$data['jurusan_loket']."', ' ', '')");
        $data['sisa_antrian_jurusan'] = $this->db->get()->num_rows();

        // Render views ke browser
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('petugas/meja_panggil_view', $data);
        $this->load->view('templates/footer');

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

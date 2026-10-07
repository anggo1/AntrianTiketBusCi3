<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Petugas extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->helper('access');
        // Kunci pengaman level login kasir
        check_menu_access();
    }

    // ==========================================
    // 1. HALAMAN UTAMA MEJA KONSOL KASIR
    // ==========================================
    public function meja_panggil() {
        date_default_timezone_set('Asia/Jakarta');
        $data['title'] = 'Meja Konsol Kasir';
        $hari_ini = date('Ymd');
        $id_loket_dinas = $this->session->userdata('loket_id');
        
        if (empty($id_loket_dinas)) {
            redirect(base_url('auth/pilih_loket'));
            exit();
        }

        // Ambil info detail rute yang dipegang oleh nomor loket ini
        $loket_info = $this->db->get_where('loket', ['id_loket' => $id_loket_dinas])->row_array();
        $data['id_loket_aktif'] = $id_loket_dinas;
        $data['loket_name'] = "Loket " . (!empty($loket_info) ? $loket_info['loket'] : '1');
        $data['jurusan_loket'] = !empty($loket_info) ? $loket_info['jurusan'] : '';
        
        // ====================================================================
        // KUNCI SAKTI: Hitung urutan baris loket ini dari seluruh daftar loket
        // Menjamin loket ke-9 PASTI mendapat huruf 'I', loket ke-10 mendapat 'J', dst.
        // ====================================================================
        $this->db->select('id_loket');
        $this->db->from('loket');
        $this->db->order_by('CAST(loket AS UNSIGNED)', 'ASC');
        $daftar_lkt_kasir = $this->db->get()->result_array();

        $posisi_urutan_abjad = 1; // Default jika tidak ketemu
        foreach ($daftar_lkt_kasir as $idx => $l_kasir) {
            if ($l_kasir['id_loket'] == $id_loket_dinas) {
                $posisi_urutan_abjad = $idx + 1; // Index array + 1
                break;
            }
        }
        $prefix = chr(64 + $posisi_urutan_abjad);

        // Ambil data antrean yang sedang aktif dilayani loket ini sekarang (terakhir dipanggil)
        $this->db->select('*');
        $this->db->from('transaksi');
        $this->db->where('tgl', $hari_ini);
        $this->db->where('id_loket', $id_loket_dinas);
        $this->db->order_by('tgl_waktu', 'DESC');
        $current_q = $this->db->get()->row_array();

        if (!empty($current_q)) {
            // Gabungkan huruf prefix kustom + angka padding 3 digit (Contoh: I-024)
            $current_q['no_antrian_lengkap'] = $prefix . '-' . str_pad($current_q['no_antrian'], 3, '0', STR_PAD_LEFT);
            $data['current_data'] = $current_q;
        } else {
            $data['current_data'] = NULL;
        }

        // --- HITUNG STATISTIK JURUSAN KHUSUS LOKET INI ---
        $this->db->from('transaksi');
        $this->db->where('tgl', $hari_ini);
        $this->db->where("REPLACE(tujuan, ' ', '') = REPLACE('".$data['jurusan_loket']."', ' ', '')");
        $data['total_antrian_jurusan'] = $this->db->get()->num_rows();

        $this->db->from('transaksi');
        $this->db->where('tgl', $hari_ini);
        $this->db->where('id_loket', 0);
        $this->db->where("REPLACE(tujuan, ' ', '') = REPLACE('".$data['jurusan_loket']."', ' ', '')");
        $data['sisa_antrian_jurusan'] = $this->db->get()->num_rows();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('petugas/meja_panggil_view', $data);
        $this->load->view('templates/footer');

        // Paksa hapus flashdata sukses agar tidak berulang saat di-refresh kasir
        $this->session->unset_userdata('success');
    }

    // ==========================================
    // 2. TOMBOL ANTRIAN SELANJUTNYA (BERURUTAN JURUSAN)
    // ==========================================
    public function panggil_berikutnya() {
        date_default_timezone_set('Asia/Jakarta');
        $hari_ini = date('Ymd');
        $id_loket_dinas = $this->session->userdata('loket_id');

        if (empty($id_loket_dinas)) {
            redirect(base_url('auth/pilih_loket'));
            exit();
        }

        $loket_info = $this->db->get_where('loket', ['id_loket' => $id_loket_dinas])->row_array();
        $jurusan_loket = !empty($loket_info) ? $loket_info['jurusan'] : '';

        // Ambil 1 antrean terlama yang masih waiting (id_loket = 0) khusus JURUSAN LOKET INI
        $this->db->select('*');
        $this->db->from('transaksi');
        $this->db->where('tgl', $hari_ini);
        $this->db->where('id_loket', 0);
        $this->db->where("REPLACE(tujuan, ' ', '') = REPLACE('".$jurusan_loket."', ' ', '')");
        $this->db->order_by('id_transaksi', 'ASC'); // FIFO (First In First Out)
        $this->db->limit(1);
        $next_queue = $this->db->get()->row_array();

        if (!empty($next_queue)) {
            $this->db->where('id_transaksi', $next_queue['id_transaksi']);
            $this->db->update('transaksi', [
                'id_loket' => $id_loket_dinas,
                'username' => $this->session->userdata('username'),
                'tgl_waktu' => date('Y-m-d H:i:s')
            ]);
            $this->session->set_flashdata('success', 'Berhasil memanggil nomor antrean ' . $next_queue['no_antrian']);
        } else {
            $this->session->set_flashdata('error', 'Antrean menunggu untuk rute ' . $jurusan_loket . ' sudah habis!');
        }

        redirect(base_url('petugas/meja_panggil'));
        exit();
    }

    // ==========================================
    // 3. FITUR FORM: CARI & PANGGIL SPESIFIK
    // ==========================================
    public function panggil_spesifik() {
        date_default_timezone_set('Asia/Jakarta');
        $hari_ini = date('Ymd');
        $id_loket_dinas = $this->session->userdata('loket_id');
        
        // Ambil input nomor angka murni dari form kasir
        $no_cari = $this->input->post('no_antrian', true);

        if (empty($id_loket_dinas) || empty($no_cari)) {
            redirect(base_url('petugas/meja_panggil'));
            exit();
        }

        // Ambil informasi rute jurusan yang dipegang oleh loket ini
        $loket_info = $this->db->get_where('loket', ['id_loket' => $id_loket_dinas])->row_array();
        $jurusan_loket = !empty($loket_info) ? $loket_info['jurusan'] : '';

        // KUNCI UTAMA: Cari data berdasarkan No Antrean DAN Kesamaan Rute Jurusan Loket Hari Ini
        $this->db->select('*');
        $this->db->from('transaksi');
        $this->db->where('tgl', $hari_ini);
        $this->db->where('no_antrian', $no_cari);
        $this->db->where("REPLACE(tujuan, ' ', '') = REPLACE('".$jurusan_loket."', ' ', '')");
        $antrian_match = $this->db->get()->row_array();

        if (!empty($antrian_match)) {
            // JIKA ANTRIAN MASIH WAITING (id_loket = 0), Ambil alih dan update datanya
            if ($antrian_match['id_loket'] == 0) {
                $this->db->where('id_transaksi', $antrian_match['id_transaksi']);
                $this->db->update('transaksi', [
                    'id_loket' => $id_loket_dinas,
                    'username' => $this->session->userdata('username'),
                    'tgl_waktu' => date('Y-m-d H:i:s')
                ]);
                $this->session->set_flashdata('success', 'Berhasil memanggil nomor antrean ' . $no_cari);
            } 
            // JIKA ANTRIAN SUDAH PERNAH DIPANGGIL LOKET INI SENDIRI, Izinkan panggil ulang langsung
            else if ($antrian_match['id_loket'] == $id_loket_dinas) {
                $this->db->where('id_transaksi', $antrian_match['id_transaksi']);
                $this->db->update('transaksi', ['tgl_waktu' => date('Y-m-d H:i:s')]);
                $this->session->set_flashdata('success', 'Memanggil ulang nomor antrean ' . $no_cari);
            } 
            // JIKA ANTRIAN TERNYATA SUDAH DIAMBIL OLEH LOKET DINAS LAIN
            else {
                $this->session->set_flashdata('error', 'Gagal! Nomor ' . $no_cari . ' sudah diambil oleh Loket lain.');
            }
        } else {
            $this->session->set_flashdata('error', 'Nomor antrean ' . $no_cari . ' tidak ditemukan untuk rute ' . $jurusan_loket);
        }

        redirect(base_url('petugas/meja_panggil'));
        exit();
    }
        // ==========================================
    // 4. API UTAMA: UPDATE DATABASE SAAT KASIR KLIK RECALL
    // ==========================================
    public function ajax_recall() {
        date_default_timezone_set('Asia/Jakarta');
        $hari_ini = date('Ymd');
        $id_loket_dinas = $this->session->userdata('loket_id');

        if (!empty($id_loket_dinas)) {
            // Ambil nomor antrean terakhir yang sedang dilayani di loket ini
            $this->db->select('id_transaksi');
            $this->db->from('transaksi');
            $this->db->where('tgl', $hari_ini);
            $this->db->where('id_loket', $id_loket_dinas);
            $this->db->order_by('tgl_waktu', 'DESC');
            $this->db->limit(1);
            $last_call = $this->db->get()->row_array();

            if (!empty($last_call)) {
                // SAKTI: Paksa perbarui tgl_waktu ke detik sekarang di DB agar layar TV menangkap sinyal panggilan
                $this->db->where('id_transaksi', $last_call['id_transaksi']);
                $this->db->update('transaksi', ['tgl_waktu' => date('Y-m-d H:i:s')]);
                
                echo json_encode(['status' => 'success', 'message' => 'Database updated untuk TV']);
                exit();
            }
        }
        echo json_encode(['status' => 'error', 'message' => 'Gagal update database']);
        exit();
    }

}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produk extends CI_Controller {

    public function __construct() {
        parent::__construct();
        check_menu_access(); 
    }

    public function index() {
        $data['title'] = 'Manajemen Produk';
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('produk/index', $data);
        $this->load->view('templates/footer');
    }

    public function tambah() {
        if (!has_permission('is_create')) { redirect('auth/blocked'); }
        echo "Proses Tambah Data Berhasil.";
    }

    public function ubah($id) {
        if (!has_permission('is_update')) { redirect('auth/blocked'); }
        echo "Proses Ubah Data ID $id Berhasil.";
    }

    public function hapus($id) {
        if (!has_permission('is_delete')) { redirect('auth/blocked'); }
        echo "Proses Hapus Data ID $id Berhasil.";
    }

    public function cetak() {
        if (!has_permission('is_print')) { redirect('auth/blocked'); }
        echo "Proses Cetak Laporan PDF Berhasil.";
    }
}

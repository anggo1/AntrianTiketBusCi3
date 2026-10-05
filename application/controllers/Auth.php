<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('session');
    }

    public function index() {
        if ($this->session->userdata('user_id')) {
            if ($this->session->userdata('role_id') == 1) {
                redirect(base_url('dashboard'));
            } else {
                redirect(base_url('petugas/meja_panggil'));
            }
            exit();
        }

        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('auth/login');
        } else {
            $this->_login();
        }
    }

    private function _login() {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $user = $this->db->get_where('users', ['username' => $username])->row_array();

        if ($user && password_verify($password, $user['password'])) {
            $data = [
                'user_id'   => $user['id'],
                'username'  => $user['username'],
                'role_id'   => $user['role_id'],
                'name'      => $user['name']
            ];
            $this->session->set_userdata($data);
            session_write_close(); 

            if ($user['role_id'] == 1) { 
                $this->session->set_userdata('loket_id', NULL); 
                redirect(base_url('dashboard'));
                exit();
            } else {
                redirect(base_url('auth/pilih_loket'));
                exit();
            }
        } else {
            $this->session->set_flashdata('alert', '<div class="alert alert-danger" role="alert">Username/Password Salah!</div>');
            redirect(base_url('login_antri'));
            exit();
        }
    }

    // --- HALAMAN PILIHAN LOKET YANG MENCEGAT LOKET KEMBAR ---
    public function pilih_loket() {
        if (!$this->session->userdata('user_id')) {
            redirect(base_url('login_antri'));
            exit();
        }

        if ($this->session->userdata('role_id') == 1) {
            redirect(base_url('dashboard'));
            exit();
        }

        $data['title'] = 'Pilih Loket Dinas Anda';
        
        // PENTING: Hanya mengambil loket yang statusnya = 1 (Buka & Belum dipakai orang lain)
        $data['loket_buka'] = $this->db->get_where('loket', ['status' => 1])->result_array();

        $this->form_validation->set_rules('id_loket', 'Loket', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('auth/pilih_loket', $data);
        } else {
            $id_loket = $this->input->post('id_loket');
            $user_id = $this->session->userdata('user_id');

            // KUNCI 1: Cek kembali secara real-time apakah loket ini baru saja diambil orang lain
            $cek_loket = $this->db->get_where('loket', ['id_loket' => $id_loket, 'status' => 1])->row_array();
            
            if (!$cek_loket) {
                // Jika sudah diisi orang lain sesaat sebelum klik submit
                $this->session->set_flashdata('alert', '<div class="alert alert-danger" role="alert">Maaf, Loket tersebut baru saja dipilih petugas lain! Silakan pilih loket yang tersisa.</div>');
                redirect(base_url('auth/pilih_loket'));
                exit();
            }

            // KUNCI 2: Ubah status loket menjadi 0 (Tutup/Sedang Digunakan) di database agar terkunci
            $this->db->where('id_loket', $id_loket);
            $this->db->update('loket', ['status' => 0]);

            // Hubungkan user ke loket tersebut
            $this->db->where('id', $user_id);
            $this->db->update('users', ['loket_id' => $id_loket]);

            $this->session->set_userdata('loket_id', $id_loket);

            redirect(base_url('petugas/meja_panggil'));
            exit();
        }
    }

    // --- LOGOUT BERSIH: MELEPAS KUNCI LOKET KEMBAR ---
    public function logout() {
        $user_id = $this->session->userdata('user_id');
        $id_loket = $this->session->userdata('loket_id');

        if (!empty($user_id)) {
            // KUNCI 3: Jika yang logout adalah petugas loket, buka kembali status loketnya menjadi 1 (Buka/Kosong)
            if (!empty($id_loket)) {
                $this->db->where('id_loket', $id_loket);
                $this->db->update('loket', ['status' => 1]);
            }

            // Putuskan ikatan loket_id di tabel users
            $this->db->where('id', $user_id);
            $this->db->update('users', ['loket_id' => NULL]);
        }

        // Hancurkan session
        $this->session->sess_destroy();
        
        redirect(base_url('login_antri'));
        exit();
    }

    public function blocked() {
        $this->load->view('auth/blocked');
    }
}

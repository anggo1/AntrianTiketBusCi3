<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('access');
        check_menu_access();
    }

    public function index() {
        $data['title'] = 'Dashboard Admin';
        $data['user_name'] = $this->session->userdata('name');

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('templates/navbar');
        $this->load->view('dashboard', $data);
        $this->load->view('templates/footer');
    }
        // --- PROSES UPDATE PROFIL VIA MODAL AJAX/POST ---
    public function edit_profile() {
        $this->load->library('form_validation');
        
        $user_id = $this->session->userdata('user_id');
        $name = $this->input->post('name', true);
        $username = $this->input->post('username', true);
        $password = $this->input->post('password');

        // Validasi input
        $this->form_validation->set_rules('name', 'Nama', 'required|trim');
        
        // Ambil data user lama untuk pengecekan username unik
        $user_lama = $this->db->get_where('users', ['id' => $user_id])->row_array();
        
        if ($username != $user_lama['username']) {
            $this->form_validation->set_rules('username', 'Username', 'required|trim|is_unique[users.username]');
        } else {
            $this->form_validation->set_rules('username', 'Username', 'required|trim');
        }

                if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $update_data = [
                'name' => $name,
                'username' => $username
            ];

            if (!empty($password)) {
                $update_data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $this->db->where('id', $user_id);
            $this->db->update('users', $update_data);

            // Perbarui data session aktif
            $this->session->set_userdata('name', $name);
            $this->session->set_userdata('username', $username);

            // 1. Set flashdata seperti biasa
            $this->session->set_flashdata('success', 'Profil Anda berhasil diperbarui!');
            
            // 2. TAMBAHKAN BARIS INI: Paksa sistem menandai dan membersihkan session ini setelah dibaca sekali
            $this->session->mark_as_flash('success');

            redirect($_SERVER['HTTP_REFERER']);
            exit(); // Pastikan script berhenti di sini sebelum redirect jalan
        }
    }

}

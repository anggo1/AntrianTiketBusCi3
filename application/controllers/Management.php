<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Management extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper('access');
        check_menu_access();
    }
    // --- 1. HALAMAN UTAMA MATRIKS HAK AKSES ---
    public function role_access() {
        $data['title'] = 'Matriks Hak Akses';
        
        // Ambil data filter jika ada dari pencarian
        $data['role_id'] = $this->input->get('role_id');
        $data['user_id'] = $this->input->get('user_id');

        // Ambil data untuk opsi dropdown select
        $data['roles'] = $this->db->get('roles')->result_array();
        $data['users'] = $this->db->get('users')->result_array();
        
        // Ambil semua daftar menu utama
        $data['menus'] = $this->db->get('menus')->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('templates/navbar');
        $this->load->view('management/role_access', $data);
        $this->load->view('templates/footer');
    }

    // --- 2. ENDPOINT AJAX PROSES UPDATE DATA CHECKBOX ---
    public function change_access() {
        // Ambil parameter kiriman dari AJAX Jquery
        $menu_id = $this->input->post('menuId');
        $role_id = $this->input->post('roleId') ? $this->input->post('roleId') : NULL;
        $user_id = $this->input->post('userId') ? $this->input->post('userId') : NULL;
        $action  = $this->input->post('action'); // is_create, is_read, dll
        $checked = $this->input->post('checked'); // 1 atau 0

        // Buat kondisi pencarian yang unik
        $where = ['menu_id' => $menu_id];
        if ($role_id) {
            $where['role_id'] = $role_id;
            $where['user_id'] = NULL;
        } else if ($user_id) {
            $where['role_id'] = NULL;
            $where['user_id'] = $user_id;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Target Level atau User tidak valid!']);
            return;
        }

        // Cek apakah record data access-nya sudah ada atau belum di database
        $check = $this->db->get_where('user_access', $where);

        if ($check->num_rows() < 1) {
            // Jika belum ada, buat record baru
            $insert_data = $where;
            $insert_data[$action] = $checked;
            $this->db->insert('user_access', $insert_data);
        } else {
            // Jika sudah ada, tinggal update nilai kolom boolean-nya
            $this->db->where($where);
            $this->db->update('user_access', [$action => $checked]);
        }

        // Kembalikan respons sukses berformat JSON ke browser
        echo json_encode(['status' => 'success', 'message' => 'Hak akses berhasil diperbarui!']);
    }

    public function add_user() {
        $this->form_validation->set_rules('name', 'Nama Lengkap', 'required|trim');
        $this->form_validation->set_rules('username', 'Username', 'required|trim|is_unique[users.username]');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');
        $this->form_validation->set_rules('role_id', 'Level/Role', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
        } else {
            $insert_data = [
                'name' => $this->input->post('name', true),
                'username' => $this->input->post('username', true),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'role_id' => $this->input->post('role_id')
            ];
            $this->db->insert('users', $insert_data);
            $this->session->set_flashdata('success', 'User baru berhasil ditambahkan!');
        }
        redirect(base_url('management/users'));
    }

    // --- FORM & PROSES TAMBAH MENU ---
    public function add_menu() {
        $this->form_validation->set_rules('title', 'Nama Menu', 'required|trim');
        $this->form_validation->set_rules('url', 'URL Menu', 'required|trim');
        $this->form_validation->set_rules('icon', 'Icon', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
        } else {
            $insert_data = [
                'title' => $this->input->post('title', true),
                'url' => strtolower($this->input->post('url', true)),
                'icon' => $this->input->post('icon', true),
                
                // TAMBAHKAN BARIS INI AGAR TANGKAPAN DATA MODAL TAMBAH MASUK KE DB
                'is_main_menu' => $this->input->post('is_main_menu') 
            ];
            $this->db->insert('menus', $insert_data);
            $this->session->set_flashdata('success', 'Menu Utama baru berhasil ditambahkan!');
        }
        redirect(base_url('management/menus'));
    }


    public function add_submenu() {
        $this->form_validation->set_rules('menu_id', 'Menu Utama', 'required');
        $this->form_validation->set_rules('title', 'Nama Sub Menu', 'required|trim');
        $this->form_validation->set_rules('url', 'URL Sub Menu', 'required|trim');
        $this->form_validation->set_rules('icon', 'Icon Sub Menu', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
        } else {
            $insert_data = [
                'menu_id' => $this->input->post('menu_id'),
                'title' => $this->input->post('title', true),
                'url' => strtolower($this->input->post('url', true)),
                'icon' => $this->input->post('icon', true),
                'is_active' => 1
            ];
            $this->db->insert('sub_menus', $insert_data);
            $this->session->set_flashdata('success', 'Sub Menu baru berhasil ditambahkan!');
        }
        redirect(base_url('management/menus')); // Dikembalikan ke panel daftar menu
    }

        // ==========================================
    //          PANEL DAFTAR DATA USER
    // ==========================================
    public function users() {
        $data['title'] = 'Manajemen Data Pengguna';
        
        // Mengambil data user gabungan dengan nama role-nya
        $this->db->select('users.*, roles.role_name');
        $this->db->from('users');
        $this->db->join('roles', 'roles.id = users.role_id');
        $data['all_users'] = $this->db->get()->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('templates/navbar');
        $this->load->view('management/users_list', $data);
        $this->load->view('templates/footer');
    }

    public function delete_user($id) {
        // Amankan agar user tidak menghapus dirinya sendiri yang sedang login
        if ($id == $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'Gagal! Anda tidak bisa menghapus akun Anda sendiri.');
            redirect(base_url('management/users'));
            exit();
        }

        $this->db->delete('users', ['id' => $id]);
        $this->session->set_flashdata('success', 'Data Pengguna berhasil dihapus!');
        redirect(base_url('management/users'));
    }

    // ==========================================
    //          PANEL DAFTAR DATA MENU
    // ==========================================
    public function menus() {
        $data['title'] = 'Manajemen Menu Utama';
        $data['all_menus'] = $this->db->get('menus')->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('templates/navbar');
        $this->load->view('management/menus_list', $data);
        $this->load->view('templates/footer');
    }

    public function delete_menu($id) {
        $this->db->delete('menus', ['id' => $id]);
        $this->session->set_flashdata('success', 'Menu Utama berhasil dihapus!');
        redirect(base_url('management/menus'));
    }
        // --- PROSES EDIT / UPDATE MENU UTAMA ---
    public function edit_menu() {
        $id = $this->input->post('id_menu');
        
        $this->form_validation->set_rules('title', 'Nama Menu', 'required|trim');
        $this->form_validation->set_rules('url', 'URL Menu', 'required|trim');
        $this->form_validation->set_rules('icon', 'Icon', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
        } else {
            $update_data = [
                'title' => $this->input->post('title', true),
                'url' => strtolower($this->input->post('url', true)),
                'icon' => $this->input->post('icon', true)
            ];
            
            $this->db->where('id', $id);
            $this->db->update('menus', $update_data);
            $this->session->set_flashdata('success', 'Menu Utama berhasil diperbarui!');
        }
        redirect(base_url('management/menus'));
        exit();
    }

        // --- PROSES SIMPAN LEVEL / ROLE BARU DARI MATRIKS ---
    public function add_role() {
        $this->form_validation->set_rules('role_name', 'Nama Level/Role', 'required|trim|is_unique[roles.role_name]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
        } else {
            $role_name = $this->input->post('role_name', true);
            
            // Simpan level baru ke tabel roles
            $this->db->insert('roles', ['role_name' => $role_name]);
            $new_role_id = $this->db->insert_id();

            // OTOMATISASI: Daftarkan seluruh menu yang ada ke level baru ini dengan status awal tidak aktif (0)
            // Ini agar level baru langsung muncul barisnya di tabel matriks
            $all_menus = $this->db->get('menus')->result_array();
            foreach ($all_menus as $m) {
                $this->db->insert('user_access', [
                    'menu_id'   => $m['id'],
                    'role_id'   => $new_role_id,
                    'user_id'   => NULL,
                    'is_create' => 0,
                    'is_read'   => 0, // Admin tinggal mencentang untuk mengaktifkan
                    'is_update' => 0,
                    'is_delete' => 0,
                    'is_print'  => 0
                ]);
            }

            $this->session->set_flashdata('success', 'Level Akses baru "' . $role_name . '" berhasil ditambahkan!');
        }
        
        // Kembalikan ke halaman matriks hak akses semula
        redirect(base_url('management/role_access'));
        exit();
    }
        // ==========================================
    //    FORM MANAJEMEN SLIDER & RUNNING TEXT
    // ==========================================
        // --- 1. MEMPERBARUI METHOD DISPLAY CONTROL YANG SUDAH ADA ---
    public function display_control() {
        $data['title'] = 'Kontrol Media Informasi TV';
        $data['sliders'] = $this->db->get('display_info')->result_array();
        $data['texts'] = $this->db->get('running_text')->result_array();
        
        // Tambahan: Ambil data konfigurasi display dari baris pertama (id 1)
        $data['settings'] = $this->db->get_where('display_settings', ['id_setting' => 1])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('templates/navbar');
        $this->load->view('management/display_control_view', $data);
        $this->load->view('templates/footer');
    }

    // --- 2. METHOD BARU: PROSES UPDATE LOGO DAN TEKS IDENTITAS HEADER TV ---
    public function update_display_settings() {
        $id = 1;
        $judul_atas  = $this->input->post('judul_atas', true);
        $detail_teks = $this->input->post('detail_teks', true);

        // Ambil data lama untuk berjaga-jaga jika logo tidak diganti
        $old_setting = $this->db->get_where('display_settings', ['id_setting' => $id])->row_array();
        $file_logo = $old_setting['logo'];

        // Konfigurasi Upload jika Admin memilih file logo baru
        if (!empty($_FILES['logo_perusahaan']['name'])) {
            $config['upload_path']   = './assets/admin/img/upload/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size']      = 2048; // 2MB
            $config['encrypt_name']  = TRUE;

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('logo_perusahaan')) {
                // Hapus file logo fisik yang lama agar htdocs bersih (kecuali file bawaan awal)
                if ($old_setting['logo'] != 'default_logo.png' && file_exists('./assets/admin/img/upload/' . $old_setting['logo'])) {
                    unlink('./assets/admin/img/upload/' . $old_setting['logo']);
                }
                $upload_data = $this->upload->data();
                $file_logo = $upload_data['file_name'];
            } else {
                $this->session->set_flashdata('error', 'Gagal Upload Logo: ' . $this->upload->display_errors());
                redirect(base_url('management/display_control'));
                exit();
            }
        }

        // Jalankan Query Update
        $update_data = [
            'logo'        => $file_logo,
            'judul_atas'  => $judul_atas,
            'detail_teks' => $detail_teks
        ];

        $this->db->where('id_setting', $id);
        $this->db->update('display_settings', $update_data);

        $this->session->set_flashdata('success', 'Identitas Monitor TV berhasil diperbarui!');
        redirect(base_url('management/display_control'));
        exit();
    }


    // PROSES SIMPAN UPLOAD GAMBAR SLIDER
    public function upload_slider() {
        $config['upload_path']   = './assets/admin/img/upload/';
        $config['allowed_types'] = 'jpg|jpeg|png|gif';
        $config['max_size']      = 3072; // 3MB
        $config['encrypt_name']  = TRUE; // Enkripsi nama file agar aman

        // Buat folder otomatis jika belum tersedia di assets
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, TRUE);
        }

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('gambar_slider')) {
            $this->session->set_flashdata('error', $this->upload->display_errors());
        } else {
            $file_data = $this->upload->data();
            $insert_data = [
                'gambar' => $file_data['file_name'],
                'keterangan' => $this->input->post('keterangan', true)
            ];
            $this->db->insert('display_info', $insert_data);
            $this->session->set_flashdata('success', 'Gambar Informasi Berhasil Diunggah!');
        }
        redirect(base_url('management/display_control'));
    }

    // PROSES SIMPAN BERITA BERJALAN
    public function add_running_text() {
        $berita = $this->input->post('berita', true);
        if(!empty($berita)) {
            $this->db->insert('running_text', ['berita' => $berita, 'is_active' => 1]);
            $this->session->set_flashdata('success', 'Teks Berita Berjalan Berhasil Ditambahkan!');
        }
        redirect(base_url('management/display_control'));
    }

    // PROSES HAPUS MEDIA SLIDER
    public function delete_slider($id) {
        $row = $this->db->get_where('display_info', ['id_info' => $id])->row_array();
        if($row) {
            unlink('./assets/admin/img/upload/' . $row['gambar']); // Hapus file fisik dari folder
            $this->db->delete('display_info', ['id_info' => $id]);
            $this->session->set_flashdata('success', 'Gambar Slider berhasil dihapus!');
        }
        redirect(base_url('management/display_control'));
    }

        // ==========================================
    //   FITUR TAMBAHAN: TOGGLE STATUS & DELETE
    // ==========================================

    // 1. Toggle Status Gambar Slider (On/Off)
    // Pastikan Anda sudah menambahkan kolom `is_active` tinyint(1) DEFAULT 1 di tabel `display_info` via phpMyAdmin jika ingin menyembunyikannya secara dinamis.
    public function toggle_slider($id) {
        $row = $this->db->get_where('display_info', ['id_info' => $id])->row_array();
        if ($row) {
            // Jika kolom belum ada di database Anda, jalankan ALTER TABLE display_info ADD is_active tinyint(1) DEFAULT 1;
            // Namun jika tidak ingin menambah kolom, tombol ini bisa difokuskan untuk berita saja.
            // Di sini kita asumsikan kolom sudah siap atau jika belum, fungsi ini akan mengubah status di DB.
            $status_baru = ($row['is_active'] == 1) ? 0 : 1;
            $this->db->where('id_info', $id);
            $this->db->update('display_info', ['is_active' => $status_baru]);
            $this->session->set_flashdata('success', 'Status gambar berhasil diperbarui!');
        }
        redirect(base_url('management/display_control'));
    }

    // 2. Toggle Status Berita Berjalan (On/Off)
    public function toggle_text($id) {
        $row = $this->db->get_where('running_text', ['id_text' => $id])->row_array();
        if ($row) {
            $status_baru = ($row['is_active'] == 1) ? 0 : 1;
            $this->db->where('id_text', $id);
            $this->db->update('running_text', ['is_active' => $status_baru]);
            $this->session->set_flashdata('success', 'Status berita berhasil diperbarui!');
        }
        redirect(base_url('management/display_control'));
    }

    // 3. Hapus Berita Berjalan
    public function delete_running_text($id) {
        $this->db->delete('running_text', ['id_text' => $id]);
        $this->session->set_flashdata('success', 'Teks berita berjalan berhasil dihapus!');
        redirect(base_url('management/display_control'));
    }

    // ==========================================
    //      HALAMAN MASTER DATA LOKET & KELAS
    // ==========================================
    public function master_data() {
        $data['title'] = 'Manajemen Master Data Loket & Kelas';
        $data['all_loket'] = $this->db->order_by('CAST(loket AS UNSIGNED)', 'ASC')->get('loket')->result_array();
        $data['all_kelas'] = $this->db->get('master_kelas')->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('templates/navbar');
        $this->load->view('management/master_data_view', $data);
        $this->load->view('templates/footer');
    }

    // 1. PROSES TAMBAH LOKET BARU (Termasuk Tujuan Rute Jurusannya)
    public function add_loket_master() {
        $insert_data = [
            'loket' => $this->input->post('nomor_loket', true),
            'jurusan' => $this->input->post('jurusan_tujuan', true),
            'status' => 1 // Mula-mula berstatus 1 (Buka/Kosong)
        ];
        $this->db->insert('loket', $insert_data);
        $this->session->set_flashdata('success', 'Loket dan Tujuan Rute berhasil ditambahkan!');
        redirect(base_url('management/master_data'));
    }

    // 2. TOGGLE ON/OFF STATUS BUKA/TUTUP LOKET
    public function toggle_status_loket($id) {
        $row = $this->db->get_where('loket', ['id_loket' => $id])->row_array();
        if ($row) {
            $status_baru = ($row['status'] == 1) ? 0 : 1;
            $this->db->where('id_loket', $id)->update('loket', ['status' => $status_baru]);
            $this->session->set_flashdata('success', 'Status buka/tutup loket berhasil diperbarui!');
        }
        redirect(base_url('management/master_data'));
    }

    // 3. HAPUS DATA LOKET
    public function delete_loket_master($id) {
        $this->db->delete('loket', ['id_loket' => $id]);
        $this->session->set_flashdata('success', 'Data loket berhasil dihapus!');
        redirect(base_url('management/master_data'));
    }

    // 4. PROSES TAMBAH KELAS PELAYANAN BUS
    public function add_kelas_master() {
        $nama_kelas = $this->input->post('nama_kelas', true);
        $this->db->insert('master_kelas', ['nama_kelas' => $nama_kelas]);
        $this->session->set_flashdata('success', 'Kelas pelayanan bus baru berhasil ditambahkan!');
        redirect(base_url('management/master_data'));
    }

    // 5. HAPUS KELAS PELAYANAN BUS
    public function delete_kelas_master($id) {
        $this->db->delete('master_kelas', ['id_kelas' => $id]);
        $this->session->set_flashdata('success', 'Kelas pelayanan bus berhasil dihapus!');
        redirect(base_url('management/master_data'));
    }
        // --- PROSES EDIT LOKET & JURUSAN MASTER ---
    public function edit_loket_master() {
        $id = $this->input->post('id_loket');
        
        $update_data = [
            'loket'   => $this->input->post('nomor_loket', true),
            'jurusan' => $this->input->post('jurusan_tujuan', true)
        ];

        $this->db->where('id_loket', $id);
        $this->db->update('loket', $update_data);
        
        $this->session->set_flashdata('success', 'Data Loket & Rute berhasil diperbarui!');
        redirect(base_url('management/master_data'));
        exit();
    }

    // --- PROSES EDIT KELAS BUS MASTER ---
    public function edit_kelas_master() {
        $id = $this->input->post('id_kelas');
        
        $update_data = [
            'nama_kelas' => $this->input->post('nama_kelas', true)
        ];

        $this->db->where('id_kelas', $id);
        $this->db->update('master_kelas', $update_data);
        
        $this->session->set_flashdata('success', 'Nama Kelas Bus berhasil diperbarui!');
        redirect(base_url('management/master_data'));
        exit();
    }



}

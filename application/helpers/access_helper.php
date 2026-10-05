<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function check_menu_access() {
    $ci = get_instance();
    
    // Ambil nama controller utama saat ini
    $current_controller = strtolower($ci->router->fetch_class());

    // 1. BYPASS UTAMA: Kios tiket depan (antrian) dan rute login (auth) mutlak bebas akses
    if ($current_controller == 'antrian' || $current_controller == 'auth') {
        return true; 
    }

    // 2. Jika session user_id kosong, tendang langsung ke rute login_antri
    if (!$ci->session->userdata('user_id')) {
        redirect(base_url('login_antri'));
        exit();
    }
    $role_id = $ci->session->userdata('role_id');
    $user_id = $ci->session->userdata('user_id');

    // =========================================================
    //  KUNCI UTAMA: JIKA DIA SUPER ADMIN (ROLE_ID = 1), 
    //  OTOMATIS BERIKAN BYPASS IJIN UNTUK SEMUA MENU MANAGEMENT
    // =========================================================
    if ($role_id == 1 && $current_controller == 'management') {
        return true; 
    }

     // 3. Kueri proteksi reguler untuk petugas loket / kasir non-admin
    $ci->db->select('user_access.id');
    $ci->db->from('user_access');
    $ci->db->join('menus', 'menus.id = user_access.menu_id');
    $ci->db->group_start();
        $ci->db->where('user_access.role_id', $role_id);
        $ci->db->or_where('user_access.user_id', $user_id);
    $ci->db->group_end();
    $ci->db->where('user_access.is_read', 1);
    $ci->db->where('LOWER(menus.url)', $current_controller); 
    
    $access = $ci->db->get();

    if ($access->num_rows() < 1) {
        redirect(base_url('auth/blocked'));
        exit();
    }
}
function has_permission($action) {
    $ci = get_instance();
    $role_id = $ci->session->userdata('role_id');
    $user_id = $ci->session->userdata('user_id');
    $current_controller = strtolower($ci->router->fetch_class());

    $ci->db->select_max("user_access.$action", 'allowed');
    $ci->db->from('user_access');
    $ci->db->join('menus', 'menus.id = user_access.menu_id');
    $ci->db->group_start();
        $ci->db->where('user_access.role_id', $role_id);
        $ci->db->or_where('user_access.user_id', $user_id);
    $ci->db->group_end();
    $ci->db->where('menus.url', $current_controller);
    
    $result = $ci->db->get()->row_array();
    return (!empty($result) && $result['allowed'] == 1);
}

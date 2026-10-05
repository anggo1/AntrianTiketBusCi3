<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('access');
        check_menu_access();
    }

    public function index() {
        $data['title'] = 'Laporan Transaksi Antrian';
        $data['master_loket'] = $this->db->get('loket')->result_array();
        $data['master_kelas'] = $this->db->get('master_kelas')->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('templates/navbar');
        $this->load->view('management/report_view', $data);
        $this->load->view('templates/footer');
    }

    // ==========================================
    // SEPAHAMAN UTAMA: MESIN SERVER-SIDE DATA TABLES
    // ==========================================
        public function get_data_server_side() {
        // Ambil parameter filter dari DataTables / Request POST
        $tgl_mulai   = $this->input->post('tgl_mulai');
        $tgl_selesai = $this->input->post('tgl_selesai');
        $id_loket    = $this->input->post('id_loket');
        $kelas       = $this->input->post('kelas');

        // KONDISI PINTAR: Jika kosong, otomatis kunci ke tanggal hari ini (YYYYMMDD)
        $db_mulai   = !empty($tgl_mulai) ? date('Ymd', strtotime($tgl_mulai)) : date('Ymd');
        $db_selesai = !empty($tgl_selesai) ? date('Ymd', strtotime($tgl_selesai)) : date('Ymd');

        $limit  = $this->input->post('length');
        $start  = $this->input->post('start');
        $search = $this->input->post('search')['value'];

        // Susun Query Dasar
        $this->_build_query_report($db_mulai, $db_selesai, $id_loket, $kelas, $search);
        
        if ($limit != -1) {
            $this->db->limit($limit, $start);
        }
        
        $query_data = $this->db->get()->result_array();

        // Hitung Total Data Setelah Filter Search
        $this->_build_query_report($db_mulai, $db_selesai, $id_loket, $kelas, $search);
        $recordsFiltered = $this->db->get()->num_rows();

        $recordsTotal = $this->db->count_all_results('transaksi');

        $result = [];
        $no = $start + 1;
        
        foreach ($query_data as $row) {
            $loket_badge = !empty($row['nomor_loket']) 
                ? '<span class="badge badge-light border text-dark font-weight-bold">Loket '.$row['nomor_loket'].'</span>' 
                : '<span class="badge badge-danger">Waiting</span>';

            $sub_array = [];
            $sub_array[] = $no++;
            $sub_array[] = '<code>'.$row['tgl_waktu'].'</code>';
            $sub_array[] = '<b class="text-primary">'.$row['no_antrian'].'</b>';
            $sub_array[] = $loket_badge;
            $sub_array[] = '<span class="font-weight-bold">'.$row['tujuan'].'</span>';
            $sub_array[] = '<b class="text-gray-900">'.$row['nama'].'</b>';
            $sub_array[] = '<code>'.$row['no_ktp'].'</code>';
            $sub_array[] = '<span class="badge badge-info px-2 py-1">'.$row['kelas'].'</span>';
            $result[] = $sub_array;
        }

        $output = [
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data"            => $result,
        ];

        echo json_encode($output);
    }


    // Helper Generator Query untuk menghindari duplikasi kode kueri
    private function _build_query_report($db_mulai, $db_selesai, $id_loket, $kelas, $search) {
        $this->db->select('transaksi.*, loket.loket as nomor_loket');
        $this->db->from('transaksi');
        $this->db->join('loket', 'loket.id_loket = transaksi.id_loket', 'left');

        // Jalankan Filter Custom Form
        if (!empty($db_mulai)) $this->db->where('transaksi.tgl >=', $db_mulai);
        if (!empty($db_selesai)) $this->db->where('transaksi.tgl <=', $db_selesai);
        if (!empty($id_loket)) $this->db->where('transaksi.id_loket', $id_loket);
        if (!empty($kelas)) $this->db->where('transaksi.kelas', $kelas);

        // Jalankan Fitur Live Search Kolom Ketik Batangan DataTables
        if (!empty($search)) {
            $this->db->group_start();
                $this->db->like('transaksi.nama', $search);
                $this->db->or_like('transaksi.no_ktp', $search);
                $this->db->or_like('transaksi.tujuan', $search);
                $this->db->or_like('transaksi.no_antrian', $search);
            $this->db->group_end();
        }
        $this->db->order_by('transaksi.tgl_waktu', 'DESC');
    }

    // --- PROSES EXPORT EXCEL (TETAP SAMA SEPERTI SEBELUMNYA) ---
        public function export_excel() {
        $tgl_mulai   = $this->input->get('tgl_mulai');
        $tgl_selesai = $this->input->get('tgl_selesai');
        $id_loket    = $this->input->get('id_loket');
        $kelas       = $this->input->get('kelas');

        // KONDISI PINTAR: Jika kosong, otomatis kunci ke tanggal hari ini
        $db_mulai   = !empty($tgl_mulai) ? date('Ymd', strtotime($tgl_mulai)) : date('Ymd');
        $db_selesai = !empty($tgl_selesai) ? date('Ymd', strtotime($tgl_selesai)) : date('Ymd');

        $this->db->select('transaksi.tgl_waktu, transaksi.no_antrian, loket.loket as nomor_loket, transaksi.tujuan, transaksi.nama, transaksi.no_ktp, transaksi.no_tlp, transaksi.kelas');
        $this->db->from('transaksi');
        $this->db->join('loket', 'loket.id_loket = transaksi.id_loket', 'left');
        
        if (!empty($db_mulai)) $this->db->where('transaksi.tgl >=', $db_mulai);
        if (!empty($db_selesai)) $this->db->where('transaksi.tgl <=', $db_selesai);
        if (!empty($id_loket)) $this->db->where('transaksi.id_loket', $id_loket);
        if (!empty($kelas)) $this->db->where('transaksi.kelas', $kelas);
        
        $this->db->order_by('transaksi.tgl_waktu', 'ASC');
        $data = $this->db->get()->result_array();

        header("Content-Type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Laporan_Antrian_SinarJaya_".date('Ymd_His').".xls");
        
        echo '<table border="1"><tr style="background-color:#4e73df; color:#fff; font-weight:bold;"><th>No</th><th>Tanggal Waktu</th><th>Nomor Antrian</th><th>Loket</th><th>Rute Wilayah Tujuan</th><th>Nama Penumpang</th><th>No KTP</th><th>No Telp</th><th>Kelas</th></tr>';
        $no = 1;
        foreach ($data as $d) {
            echo "<tr><td>".($no++)."</td><td>".$d['tgl_waktu']."</td><td style='text-align:center;'>".$d['no_antrian']."</td><td>".(!empty($d['nomor_loket']) ? 'Loket '.$d['nomor_loket'] : 'Waiting')."</td><td>".$d['tujuan']."</td><td>".$d['nama']."</td><td style='mso-number-format:\"\\@\";'>'".$d['no_ktp']."</td><td style='mso-number-format:\"\\@\";'>'".$d['no_tlp']."</td><td>".$d['kelas']."</td></tr>";
        }
        echo '</table>';
    }

}

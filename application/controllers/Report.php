<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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

        // Mengambil isian filter pasca-submit untuk menghitung rangkuman pencarian di form atas
        $tgl_mulai   = $this->input->get('tgl_mulai');
        $tgl_selesai = $this->input->get('tgl_selesai');
        $id_loket    = $this->input->get('id_loket');
        $kelas       = $this->input->get('kelas');

        $db_mulai   = !empty($tgl_mulai) ? date('Ymd', strtotime($tgl_mulai)) : date('Ymd');
        $db_selesai = !empty($tgl_selesai) ? date('Ymd', strtotime($tgl_selesai)) : date('Ymd');

        // PERBAIKAN 1: Hitung total baris data spesifik yang dicari berdasarkan form filter atas
        $this->db->from('transaksi');
        if (!empty($db_mulai)) $this->db->where('tgl >=', $db_mulai);
        if (!empty($db_selesai)) $this->db->where('tgl <=', $db_selesai);
        if (!empty($id_loket)) $this->db->where('id_loket', $id_loket);
        if (!empty($kelas)) $this->db->where('kelas', $kelas);
        $data['total_pencarian_filter'] = $this->db->get()->num_rows();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('templates/navbar');
        $this->load->view('management/report_view', $data);
        $this->load->view('templates/footer');
    }

    // ====================================================================
    // MESIN SERVER-SIDE DATA TABLES (DENGAN PREFIX HURUF)
    // ====================================================================
    public function get_data_server_side() {
        date_default_timezone_set('Asia/Jakarta');
        
        $tgl_mulai   = $this->input->post('tgl_mulai');
        $tgl_selesai = $this->input->post('tgl_selesai');
        $id_loket    = $this->input->post('id_loket');
        $kelas       = $this->input->post('kelas');

        $db_mulai   = !empty($tgl_mulai) ? date('Ymd', strtotime($tgl_mulai)) : date('Ymd');
        $db_selesai = !empty($tgl_selesai) ? date('Ymd', strtotime($tgl_selesai)) : date('Ymd');

        $limit  = $this->input->post('length');
        $start  = $this->input->post('start');
        $search = $this->input->post('search')['value'];

        $this->_build_query_report($db_mulai, $db_selesai, $id_loket, $kelas, $search);
        
        if ($limit != -1) {
            $this->db->limit($limit, $start);
        }
        
        $query_data = $this->db->get()->result_array();

        $this->_build_query_report($db_mulai, $db_selesai, $id_loket, $kelas, $search);
        $recordsFiltered = $this->db->get()->num_rows();

        $recordsTotal = $this->db->count_all_results('transaksi');

        $this->db->select('id_loket');
        $this->db->from('loket');
        $this->db->order_by('CAST(loket AS UNSIGNED)', 'ASC');
        $peta_loket_db = $this->db->get()->result_array();
        
        $peta_huruf = [];
        foreach ($peta_loket_db as $idx => $lkt) {
            $peta_huruf[$lkt['id_loket']] = chr(64 + ($idx + 1));
        }

        $result = [];
        $no = $start + 1;
        
        foreach ($query_data as $row) {
            $loket_badge = !empty($row['nomor_loket']) 
                ? '<span class="badge badge-light border text-dark font-weight-bold">Loket '.$row['nomor_loket'].'</span>' 
                : '<span class="badge badge-danger">Waiting</span>';

            $prefix = 'A';
            
            $this->db->select('id_loket');
            $this->db->from('loket');
            $this->db->where("REPLACE(jurusan, ' ', '') = REPLACE('".$row['tujuan']."', ' ', '')");
            $cari_id_asal = $this->db->get()->row_array();
            
            if (!empty($cari_id_asal) && isset($peta_huruf[$cari_id_asal['id_loket']])) {
                $prefix = $peta_huruf[$cari_id_asal['id_loket']];
            }
            
            $no_antrian_lengkap = $prefix . '-' . str_pad($row['no_antrian'], 3, '0', STR_PAD_LEFT);

            $sub_array = [];
            $sub_array[] = $no++;
            $sub_array[] = '<code>'.$row['tgl_waktu'].'</code>';
            $sub_array[] = '<b class="text-primary">'.$no_antrian_lengkap.'</b>';
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

    private function _build_query_report($db_mulai, $db_selesai, $id_loket, $kelas, $search) {
        $this->db->select('transaksi.*, loket.loket as nomor_loket');
        $this->db->from('transaksi');
        $this->db->join('loket', 'loket.id_loket = transaksi.id_loket', 'left');

        if (!empty($db_mulai)) $this->db->where('transaksi.tgl >=', $db_mulai);
        if (!empty($db_selesai)) $this->db->where('transaksi.tgl <=', $db_selesai);
        if (!empty($id_loket)) $this->db->where('transaksi.id_loket', $id_loket);
        if (!empty($kelas)) $this->db->where('transaksi.kelas', $kelas);

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

    // ====================================================================
    // PERBAIKAN 2: INTEGRASI PHPEXCEL UNTUK FORMAT BERKAS .XLSX PREMIUM
    // ====================================================================
        // ====================================================================
    // SOLUSI TOTAL PHP 8: EKSPOR NATIVE SPREADSHEET (100% OFFLINE TANPA LIBRARY)
    // ====================================================================
    public function export_excel() {
        date_default_timezone_set('Asia/Jakarta');
        
        $tgl_mulai   = $this->input->get('tgl_mulai');
        $tgl_selesai = $this->input->get('tgl_selesai');
        $id_loket    = $this->input->get('id_loket');
        $kelas       = $this->input->get('kelas');

        $db_mulai   = !empty($tgl_mulai) ? date('Ymd', strtotime($tgl_mulai)) : date('Ymd');
        $db_selesai = !empty($tgl_selesai) ? date('Ymd', strtotime($tgl_selesai)) : date('Ymd');

        $this->db->select('transaksi.*, loket.loket as nomor_loket');
        $this->db->from('transaksi');
        $this->db->join('loket', 'loket.id_loket = transaksi.id_loket', 'left');
        
        if (!empty($db_mulai)) $this->db->where('transaksi.tgl >=', $db_mulai);
        if (!empty($db_selesai)) $this->db->where('transaksi.tgl <=', $db_selesai);
        if (!empty($id_loket)) $this->db->where('transaksi.id_loket', $id_loket);
        if (!empty($kelas)) $this->db->where('transaksi.kelas', $kelas);
        
        $this->db->order_by('transaksi.tgl_waktu', 'ASC');
        $data = $this->db->get()->result_array();

        // Tarik susunan abjad dinamis loket berdasarkan urutan baris
        $this->db->select('id_loket');
        $this->db->from('loket');
        $this->db->order_by('CAST(loket AS UNSIGNED)', 'ASC');
        $peta_loket_excel = $this->db->get()->result_array();
        
        $peta_huruf = [];
        foreach ($peta_loket_excel as $idx => $lkt) {
            $peta_huruf[$lkt['id_loket']] = chr(64 + ($idx + 1));
        }

        // Protokol Header Browser untuk memaksa unduhan berkas spreadsheet (.xls / .xlsx)
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Laporan_Antrian_SinarJaya_".date('Ymd_His').".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        // Mulai cetak struktur tabel HTML murni yang akan dibaca otomatis oleh Excel
        echo '
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://w3.org">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <!--[if gte mso 9]>
            <xml>
                <x:ExcelWorkbook>
                    <x:ExcelWorksheets>
                        <x:ExcelWorksheet>
                            <x:Name>Data Antrian</x:Name>
                            <x:WorksheetOptions>
                                <x:DisplayGridlines/>
                            </x:WorksheetOptions>
                        </x:ExcelWorksheet>
                    </x:ExcelWorksheets>
                </x:ExcelWorkbook>
            </xml>
            <![endif]-->
            <style>
                /* Gaya CSS Khusus Excel untuk memaksa kolom bertipe teks (Mencegah Angka 0 Hilang) */
                .text-format { mso-number-format:"\@"; }
                .center-format { text-align: center; mso-number-format:"\@"; }
            </style>
        </head>
        <body>
            <table border="1">
                <tr style="background-color:#4e73df; color:#ffffff; font-weight:bold; text-align:center;">
                    <th>No</th>
                    <th>Tanggal Waktu</th>
                    <th>Nomor Antrian</th>
                    <th>Loket</th>
                    <th>Rute Wilayah Tujuan</th>
                    <th>Nama Penumpang</th>
                    <th>No KTP</th>
                    <th>No Telp</th>
                    <th>Kelas</th>
                </tr>';
        
        $no = 1;
        foreach ($data as $d) {
            $prefix = 'A';
            
            // Cari tahu id_loket asal untuk rute tujuan baris ini
            $this->db->select('id_loket');
            $this->db->from('loket');
            $this->db->where("REPLACE(jurusan, ' ', '') = REPLACE('".$d['tujuan']."', ' ', '')");
            $cari_id_asal = $this->db->get()->row_array();
            
            if (!empty($cari_id_asal) && isset($peta_huruf[$cari_id_asal['id_loket']])) {
                $prefix = $peta_huruf[$cari_id_asal['id_loket']];
            }
            
            // Susun gabungan nomor antrean berhuruf lengkap kustom (Contoh: I-024)
            $no_antrian_lengkap = $prefix . '-' . str_pad($d['no_antrian'], 3, '0', STR_PAD_LEFT);
            $loket_text = !empty($d['nomor_loket']) ? 'Loket '.$d['nomor_loket'] : 'Waiting';

            echo "<tr>";
            echo "<td style='text-align:center;'>".$no++."</td>";
            echo "<td>".$d['tgl_waktu']."</td>";
            
            // KUNCI UTAMA: Menggunakan class center-format & text-format agar string dikunci aman tanpa hancur di Excel
            echo "<td class='center-format' style='font-weight:bold;'>".$no_antrian_lengkap."</td>";
            echo "<td>".$loket_text."</td>";
            echo "<td>".$d['tujuan']."</td>";
            echo "<td>".$d['nama']."</td>";
            echo "<td class='text-format'>".$d['no_ktp']."</td>";
            echo "<td class='text-format'>".$d['no_tlp']."</td>";
            echo "<td>".$d['kelas']."</td>";
            echo "</tr>";
        }
        
        echo '
            </table>
        </body>
        </html>';
        exit();
    }

}
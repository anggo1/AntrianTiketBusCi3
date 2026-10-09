<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
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

        // ====================================================================
        // PROSES 1: AMBIL DATA RIIL UNTUK HALAMAN SEKARANG
        // ====================================================================
        $this->_build_query_report($db_mulai, $db_selesai, $id_loket, $kelas, $search);
        
        if ($limit != -1) {
            $this->db->limit($limit, $start);
        }
        
        $query_data = $this->db->get()->result_array();

        // ====================================================================
        // PROSES 2: HITUNG TOTAL DATA HASIL PENCARIAN (PANGGIL ULANG QUERY)
        // ====================================================================
        $this->_build_query_report($db_mulai, $db_selesai, $id_loket, $kelas, $search);
        $recordsFiltered = $this->db->count_all_results();

        // Sesuai permintaan: Potong total global, samakan dengan hasil pencarian saja
        $recordsTotal = $recordsFiltered;

        // ====================================================================
        
        // Ambil peta loket huruf
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

    public function export_excel() {
        date_default_timezone_set('Asia/Jakarta');

        // 1. Ambil saringan filter form aktif via GET
        $tgl_mulai   = $this->input->get('tgl_mulai');
        $tgl_selesai = $this->input->get('tgl_selesai');
        $id_loket    = $this->input->get('id_loket');
        $kelas       = $this->input->get('kelas');
        $search      = $this->input->get('search');

        $db_mulai   = !empty($tgl_mulai) ? date('Ymd', strtotime($tgl_mulai)) : date('Ymd');
        $db_selesai = !empty($tgl_selesai) ? date('Ymd', strtotime($tgl_selesai)) : date('Ymd');

        // 2. Tarik data riil hasil saringan pencarian pencarian
        $this->_build_query_report($db_mulai, $db_selesai, $id_loket, $kelas, $search);
        $query_data = $this->db->get()->result_array();

        // 3. Inisialisasi Objek Baru PhpSpreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 4. Set Header Kolom Excel
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'TANGGAL WAKTU');
        $sheet->setCellValue('C1', 'NO ANTRIAN');
        $sheet->setCellValue('D1', 'LOKET');
        $sheet->setCellValue('E1', 'TUJUAN');
        $sheet->setCellValue('F1', 'NAMA');
        $sheet->setCellValue('G1', 'NO KTP');
        $sheet->setCellValue('H1', 'KELAS');

        // Beri style cetak tebal pada baris header pertama
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        // 5. Susun Peta Master Huruf untuk Nomor Antrian Antrian
        $this->db->select('id_loket');
        $this->db->from('loket');
        $this->db->order_by('CAST(loket AS UNSIGNED)', 'ASC');
        $peta_loket_db = $this->db->get()->result_array();
        
        $peta_huruf = [];
        foreach ($peta_loket_db as $idx => $lkt) {
            $peta_huruf[$lkt['id_loket']] = chr(64 + ($idx + 1));
        }

        // 6. Masukkan data ke cell baris demi baris baris
        $row_num = 2;
        $no = 1;

        foreach ($query_data as $row) {
            $prefix = 'A';
            $this->db->select('id_loket');
            $this->db->from('loket');
            $this->db->where("REPLACE(jurusan, ' ', '') = REPLACE('".$row['tujuan']."', ' ', '')");
            $cari_id_asal = $this->db->get()->row_array();
            
            if (!empty($cari_id_asal) && isset($peta_huruf[$cari_id_asal['id_loket']])) {
                $prefix = $peta_huruf[$cari_id_asal['id_loket']];
            }
            $no_antrian_lengkap = $prefix . '-' . str_pad($row['no_antrian'], 3, '0', STR_PAD_LEFT);

            $sheet->setCellValue('A' . $row_num, $no++);
            $sheet->setCellValue('B' . $row_num, $row['tgl_waktu']);
            $sheet->setCellValue('C' . $row_num, $no_antrian_lengkap);
            $sheet->setCellValue('D' . $row_num, !empty($row['nomor_loket']) ? 'Loket '.$row['nomor_loket'] : 'Waiting');
            $sheet->setCellValue('E' . $row_num, $row['tujuan']);
            $sheet->setCellValue('F' . $row_num, $row['nama']);
            
            // Set tipe data No KTP eksplisit STRING agar angka 0 di depan tidak terpotong di Excel
            $sheet->setCellValueExplicit('G' . $row_num, $row['no_ktp'], DataType::TYPE_STRING);
            
            $sheet->setCellValue('H' . $row_num, $row['kelas']);
            $row_num++;
        }

        // Auto-size lebar kolom biar rapi tidak terpotong text text-nya
        foreach (range('A', 'H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // 7. Stream File Unduhan Langsung ke Browser Browser
        $filename = "Laporan_Transaksi_" . date('Ymd_His') . ".xls";
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xls($spreadsheet);
        $writer->save('php://output');
        exit;
    }

}
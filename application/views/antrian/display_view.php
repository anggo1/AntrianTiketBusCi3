<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $title; ?></title>
    <link href="<?php echo base_url('assets/admin/css/sb-admin-2.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/admin/vendor/fontawesome-free/css/all.min.css'); ?>" rel="stylesheet">
    <style>
        /* Mengunci layar penuh anti-scroll */
        body { 
            background-color: #0f172a; 
            color: #f8fafc;
            height: 100vh; 
            display: flex;
            flex-direction: column;
            overflow: hidden; 
            font-family: 'Nunito', sans-serif; 
        }
        
        /* Header Tinggi Statis */
        .header-tv { 
            background: linear-gradient(135deg, #1e293b, #0f172a); 
            color: #fff; 
            padding: 10px 25px; 
            border-bottom: 4px solid #3b82f6; 
            flex-shrink: 0;
            height: 10vh;
        }
        .logo-text { font-style: italic; font-weight: 900; font-size: 1.8rem; color: #ef4444; }
        .logo-box { background-color: #ffffff; border-radius: 8px; padding: 2px 12px; display: inline-flex; align-items: center; justify-content: center; height: 100%; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        
        /* Konten Utama Mengambil Sisa Tinggi Layar */
        .main-content-tv {
            height: 84vh;
            padding: 15px;
            overflow: hidden;
        }
        
        /* Slider Gambar Menyesuaikan Tinggi 100% dari Kolom */
        .carousel-container-tv { height: 100%; }
        .monitor-card { 
            border-radius: 16px !important; 
            border: none; 
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3); 
            background-color: #1e293b;
            overflow: hidden;
            height: 100%;
        }
        .carousel-inner-tv { height: 100%; }
        .carousel-item-tv { height: 100%; position: relative; }
        .carousel-item-tv img { height: 100%; width: 100%; object-fit: cover; border-radius: 16px; }
        .carousel-caption-tv { background: linear-gradient(to top, rgba(15,23,42,0.95), rgba(15,23,42,0)); left:0; right:0; bottom:0; padding: 25px 20px 15px 20px; text-align: left; }
        
        /* Kunci Utama Grid Loket Tanpa Scroll */
        .grid-container-tv {
            height: 100%;
            display: flex;
            align-content: flex-start;
            overflow: hidden;
        }
        
        /* Elemen Loket Fleksibel Fleksibel Berbasis Flexbox */
        .loket-card {
            background: #1e293b;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            border: 1px solid #334155;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%; /* Tinggi diatur penuh mengisi grid box */
        }
        .loket-title {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: #ffffff;
            font-weight: 800;
            font-size: 1.25rem;
            padding: 6px 12px;
            letter-spacing: 0.5px;
            flex-shrink: 0;
        }
        .loket-body-tv {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 8px;
        }
        .number-box { 
            font-weight: 900; 
            color: #10b981; 
            background-color: #0f172a; 
            width: 90%;
            border: 2px solid #10b981; 
            border-radius: 10px; 
            padding: 4px 0; 
            text-shadow: 0 0 10px rgba(16, 185, 129, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            /* Font-size akan dikontrol dinamis oleh Javascript agar pas halaman */
        }
        .jurusan-text-tv {
            font-weight: 700;
            color: #eab308;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 6px;
            line-height: 1.3;
            width: 100%;
            padding: 0 5px;
            /* Font-size diatur dinamis oleh Javascript */
        }
        
        /* Footer Tinggi Statis */
        .footer-marquee { 
            height: 6vh;
            background: #ef4444; 
            color: #fff; 
            font-size: 1.4rem; 
            font-weight: 700; 
            display: flex;
            align-items: center;
            box-shadow: 0 -10px 15px -3px rgba(0, 0, 0, 0.3); 
            z-index: 10;
        }
        
        /* Efek Berkedip Menarik Ketika Ada Antrean Baru */
        .flash-active { animation: blinker 0.6s linear 4; }
        @keyframes blinker { 50% { opacity: 0.2; background-color: #eab308; } }
    </style>
</head>
<body>
<?php $display_setup = $this->db->get_where('display_settings', ['id_setting' => 1])->row_array(); ?>

    <!-- 1. HEADER (10% VH) -->
    <div class="header-tv d-flex justify-content-between align-items-center shadow-sm">
        <div class="d-flex align-items-center h-100">
            <div class="logo-box mr-3">
                <?php if(!empty($display_setup['logo']) && file_exists('./assets/admin/img/upload/'.$display_setup['logo'])) : ?>
                    <img src="<?= base_url('assets/admin/img/upload/'.$display_setup['logo']); ?>" style="max-height: 100%; width: auto;">
                <?php else: ?>
                    <span class="logo-text">Sinar Jaya <span class="text-dark">GROUP</span></span>
                <?php endif; ?>
            </div>
            <div>
                <h3 class="mb-0 font-weight-bold tracking-wide" style="color: #60a5fa; font-size: 1.5rem;"><?= !empty($display_setup['judul_atas']) ? $display_setup['judul_atas'] : 'MONITOR INFORMASI ANTRIAN'; ?></h3>
                <small class="text-white-50 font-weight-bold" style="font-size: 0.85rem;"><i class="fas fa-map-marker-alt text-danger mr-1"></i><?= !empty($display_setup['detail_teks']) ? $display_setup['detail_teks'] : ''; ?></small>
            </div>
        </div>
        <div class="text-right">
            <h2 class="mb-0 font-weight-bold" id="tv_clock" style="color: #ffffff; font-size: 1.8rem; letter-spacing: 1px;">00:00:00</h2>
            <small class="text-white-50 font-weight-bold" style="font-size: 0.85rem;"><?= date('D, d F Y'); ?></small>
        </div>
    </div>

    <!-- 2. KONTEN (84% VH) -->
    <div class="main-content-tv">
        <div class="row w-100 h-100 m-0">
            <!-- KOLOM KIRI: SLIDER MEDIA INFORMASI -->
            <div class="col-xl-6 col-lg-6 px-2 carousel-container-tv">
                <div id="infoCarousel" class="carousel slide monitor-card" data-ride="carousel" data-interval="5000">
                    <div class="carousel-inner carousel-inner-tv">
                        <?php if(!empty($sliders)) : ?>
                            <?php $i=0; foreach($sliders as $sl) : ?>
                                <div class="carousel-item h-100 carousel-item-tv <?= ($i==0)?'active':''; ?>">
                                    <img src="<?= base_url('assets/admin/img/upload/'.$sl['gambar']); ?>" class="d-block">
                                    <div class="carousel-caption-tv">
                                        <h4 class="font-weight-bold text-white mb-0" style="font-size: 1.25rem; line-height: 1.4;"><?= $sl['keterangan']; ?></h4>
                                    </div>
                                </div>
                            <?php $i++; endforeach; ?>
                        <?php else: ?>
                            <div class="carousel-item active h-100">
                                <div class="d-flex align-items-center justify-content-center bg-dark text-white font-weight-bold h-100" style="border-radius:16px;">
                                    <h3>Media Informasi Sinar Jaya Group</h3>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: GRID LOKET ADAPTIF ANTI SCROLL -->
            <div class="col-xl-6 col-lg-6 px-2 h-100">
                <div class="grid-container-tv w-100" id="display_loket_container">
                    <!-- Diisi otomatis secara dinamis oleh AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- 3. FOOTER RUNNING TEXT (6% VH) -->
    <div class="footer-marquee">
        <marquee scrollamount="6" behavior="scroll" direction="left">
            <?php foreach($running_text as $rt) : ?>
                <span class="mx-5" style="letter-spacing: 0.5px;"><i class="fas fa-bullhorn text-warning mr-2"></i><?= $rt['berita']; ?></span>
            <?php endforeach; ?>
        </marquee>
    </div>

    <?php 
    // Memanggil partial view skrip pengolah data AJAX
    $this->load->view('antrian/part_display_script'); 
    ?>
</body>
</html>

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
        
        /* Header Tinggi Statis (10% VH) */
        .header-tv { 
            background: linear-gradient(135deg, #1e293b, #0f172a); 
            color: #fff; 
            padding: 8px 25px; 
            border-bottom: 4px solid #3b82f6; 
            flex-shrink: 0;
            height: 10vh;
        }
        .logo-box { background-color: #ffffff; border-radius: 8px; padding: 2px 12px; display: inline-flex; align-items: center; justify-content: center; height: 100%; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .logo-text { font-style: italic; font-weight: 900; font-size: 1.8rem; color: #ef4444; }

        /* Area Konten Utama Fleksibel Mengambil Sisa Tinggi Layar (84% VH) */
        .main-content-tv {
            height: 84vh;
            padding: 10px 15px;
            overflow: hidden;
        }
        
                /* KOLOM KIRI: SLIDER GAMBAR MEDIA INFORMASI (AUTO STRETCHING 100%) */
                /* ==================================================================== */
        /* PERBAIKAN: MENERAPKAN RASIO ASPEK 16:9 (1920:1080) PADA BANNER SLIDER */
        /* ==================================================================== */
        .carousel-container-tv { 
            height: 78vh; /* Mengunci tinggi maksimal wadah agar sejajar dengan batas bawah 5 baris loket kanan */
            display: flex;
            flex-direction: column;
        }
        
        .monitor-card { 
            border-radius: 12px !important; 
            border: none; 
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4); 
            background-color: #1e293b;
            overflow: hidden;
            width: 100%;
            height: 100%;
            
            /* KUNCI UTAMA RASIO: Menjaga rasio kotak tetap stabil di TV 1920x1080 */
            aspect-ratio: 16 / 9; 
        }
        
        .carousel-inner-tv { 
            height: 100%; 
            width: 100%;
        }
        
        .carousel-item-tv { 
            height: 100%; 
            width: 100%; 
            position: relative; 
        }
        
        /* ENGINE STRETCHING GAMBAR MENGUTAMAKAN PROPORSIONALITAS */
        .carousel-item-tv img { 
            height: 100% !important; 
            width: 100% !important; 
            
            /* object-fit: fill memaksa gambar meregang penuh menutup seluruh rasio 16:9 */
            object-fit: fill !important; 
            border-radius: 12px; 
        }
        
        .carousel-caption-tv { 
            background: linear-gradient(to top, rgba(15,23,42,0.95), rgba(15,23,42,0)); 
            left: 0; 
            right: 0; 
            bottom: 0; 
            padding: 20px 20px 10px 20px; 
            text-align: left; 
            z-index: 5;
        }


        /* KOLOM KANAN: GRID BOX LOKET VERTIKAL ANTI SCROLL (TINGGI 100%) */
        .grid-container-tv {
            height: 100%;
            display: flex;
            align-content: space-between; /* Membagi baris loket merata ke bawah */
            overflow: hidden;
        }
        
        /* Elemen Kotak Loket Kapsul Premium Tipis Proporsional */
        .loket-card {
            background: #1e293b;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            border: 1px solid #334155;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }
        .loket-title {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: #ffffff;
            font-weight: 800;
            font-size: 1.05rem;
            padding: 3px 10px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            flex-shrink: 0;
        }
        .loket-body-tv {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 4px;
        }
        .number-box { 
            font-weight: 900; 
            color: #10b981; 
            background-color: #0f172a; 
            width: 96%;
            border: 2px solid #10b981; 
            border-radius: 6px; 
            padding: 1px 0; 
            text-shadow: 0 0 8px rgba(16, 185, 129, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .jurusan-text-tv {
            font-weight: 700;
            color: #eab308;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-top: 3px;
            line-height: 1.2;
            width: 100%;
            padding: 0 4px;
            white-space: normal; 
            word-wrap: break-word; 
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2; /* Batasi maksimal 2 baris agar muat halaman */
            -webkit-box-orient: vertical;
        }
        
        /* Footer Running Text (6% VH) */
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

    <!-- 2. KONTEN (84% VH - INTEGRASI GAMBAR DAN GRID BERDAMPINGAN) -->
    <div class="main-content-tv">
        <div class="row w-100 h-100 m-0">
            <!-- KOLOM KIRI: SLIDER GAMBAR MEDIA INFORMASI (LEBAR 50% LAYAR) -->
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
                                <div class="d-flex align-items-center justify-content-center bg-dark text-white font-weight-bold h-100" style="border-radius:12px;">
                                    <h3>Media Informasi Sinar Jaya Group</h3>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: TARGET INJEKSI GRID LOKET VERTIKAL ADAPTIF (LEBAR 50% LAYAR) -->
            <div class="col-xl-6 col-lg-6 px-2 h-100">
                <div class="grid-container-tv w-100" id="display_loket_container">
                    <!-- Data dirender otomatis secara dinamis oleh AJAX -->
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

    <?php $this->load->view('antrian/part_display_script'); ?>
</body>
</html>

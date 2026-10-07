<script src="<?php echo base_url('assets/admin/vendor/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/admin/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>

<script>
let localCacheNumbers = {};

function playTvAudio(nomor, loketId) {
    if (typeof Audio !== "undefined") {
        const bell = new Audio("<?php echo base_url('assets/admin/audio/Airport_Bell.mp3'); ?>");
        bell.volume = 0.9;
        bell.play().catch(err => console.log("Autoplay ditolak"));

        bell.onended = function() {
            if ('speechSynthesis' in window) {
                const kalimat = "Nomor antrean " + nomor + ", silakan menuju ke Loket " + loketId;
                const speech = new SpeechSynthesisUtterance(kalimat);
                speech.lang = 'id-ID';
                speech.rate = 0.85;
                speech.pitch = 1.05;
                window.speechSynthesis.speak(speech);
            }
        };
    }
}

function updateLiveDisplay() {
    $.ajax({
        url: "<?php echo base_url('antrian/get_live_display'); ?>",
        type: 'get',
        dataType: 'json',
        success: function(data) {
    if (!data || data.length === 0) {
        // Tampilan standby jika semua kasir sedang logout / tutup toko
        $('#display_loket_container').html(
            '<div class="w-100 text-center text-muted m-auto py-5">' +
            '<i class="fas fa-lock fa-3x mb-3 d-block text-danger"></i>' +
            '<h4 class="font-weight-bold text-white">LOKET PELAYANAN BELUM DIBUKA</h4>' +
            '<span class="text-white-50">Silakan mengambil nomor antrean melalui HP anda dengan scan </span>' +
            '</div>'
        );
        return;
    }

    let totalLoket = data.length;
            
                        // ====================================================================
            // SINKRONISASI TINGGI: Menyelaraskan Grid Kanan dengan Gambar Kiri
            // ====================================================================
            let bootstrapCol = "col-6"; 
            let rowHeightPercent = "15.0vh"; // Dikunci 15vh agar total 5 baris pas setinggi 75vh sisa ruang
            let fontSizeNumber = "2.1rem";    
            let fontSizeJurusan = "0.65rem";  

            if (totalLoket <= 2) {
                bootstrapCol = "col-12"; 
                rowHeightPercent = (totalLoket === 1) ? "75vh" : "37vh";
                fontSizeNumber = (totalLoket === 1) ? "7.0rem" : "4.5rem";
                fontSizeJurusan = (totalLoket === 1) ? "1.1rem" : "0.90rem";
            } else if (totalLoket > 2 && totalLoket <= 4) {
                bootstrapCol = "col-6";
                rowHeightPercent = "37vh";
                fontSizeNumber = "3.5rem";
                fontSizeJurusan = "0.78rem";
            } else if (totalLoket > 4 && totalLoket <= 6) {
                rowHeightPercent = "24vh"; 
                fontSizeNumber = "2.6rem";
                fontSizeJurusan = "0.70rem";
            } else if (totalLoket > 6 && totalLoket <= 8) {
                rowHeightPercent = "18.5vh"; 
                fontSizeNumber = "2.3rem";
                fontSizeJurusan = "0.68rem";
            }

            // Gunakan margin-bottom tipis 0.5vh agar pas menutup rapat halaman bawah TV
            let html = '<div class="row w-100 m-0 h-100 align-content-start">';

            
            data.forEach(function(lk) {
                let key_nomor = 'loket_num_' + lk.id_loket;
                let key_waktu = 'loket_time_' + lk.id_loket;
                let isNew = false;
                
                let nomorSekarang = lk.no_sekarang ? lk.no_sekarang : '-';
                let waktuPanggil = lk.waktu_panggil ? lk.waktu_panggil : '';

                if (localCacheNumbers[key_waktu] !== undefined && localCacheNumbers[key_waktu] != waktuPanggil && nomorSekarang != '-') {
                    isNew = true;
                    if (typeof playTvAudio === "function") {
                        playTvAudio(nomorSekarang, lk.loket);
                    }
                }
                
                localCacheNumbers[key_nomor] = nomorSekarang;
                localCacheNumbers[key_waktu] = waktuPanggil;

                let flashClass = isNew ? 'flash-active' : '';

                // Judul nama loket kustom dinamis dari database
                let titleLoket = lk.loket.toLowerCase().includes('eksekutif') ? lk.loket : 'LOKET ' + lk.loket;

                // PERBAIKAN MUTLAK: Mengunci margin-bottom ke 0.6vh dan membatasi tinggi maksimal komponen internal
                html += '<div class="' + bootstrapCol + ' px-1" style="height: ' + rowHeightPercent + '; margin-bottom: 0.6vh;">' +
                            '<div class="loket-card text-center" style="height: 100%;">' +
                                '<div class="loket-title text-truncate" style="padding: 2px 8px; font-size: 0.95rem; line-height: 1.2; flex-shrink: 0;">' + titleLoket + '</div>' +
                                '<div class="loket-body-tv ' + flashClass + '" style="display: flex; flex-direction: column; justify-content: center; height: calc(100% - 22px); padding: 2px 4px;">' +
                                    '<div class="number-box shadow-inner" style="font-size: ' + fontSizeNumber + '; line-height: 1.1; margin-bottom: 2px; flex-shrink: 0;">' + nomorSekarang + '</div>' +
                                    '<div class="jurusan-text-tv text-center" style="font-size: ' + fontSizeJurusan + '; margin-top: 1px;" title="' + lk.rute_tujuan + '">' +
                                        '<i class="fas fa-road mr-1 text-white-50"></i>' + lk.rute_tujuan +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>';
            });
            
            html += '</div>';
            $('#display_loket_container').html(html);
        },
        error: function(xhr, status, error) {
            console.log("Error AJAX Display Loket: " + error);
        }
    });
}


$(document).ready(function() {
    updateLiveDisplay();
    setInterval(updateLiveDisplay, 3000);

    setInterval(function() {
        let date = new Date();
        $('#tv_clock').text(date.toLocaleTimeString('id-ID'));
    }, 1000);
});
</script>

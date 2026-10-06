<!-- Pustaka JavaScript Utama -->
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
                $('#display_loket_container').html(
                    '<div class="w-100 text-center text-muted m-auto py-5">' +
                    '<i class="fas fa-info-circle fa-2x mb-2 d-block text-warning"></i>' +
                    'Belum ada data meja loket yang aktif dinas saat ini.' +
                    '</div>'
                );
                return;
            }

            let totalLoket = data.length;
            
            // ====================================================================
            // ENGINE HITUNG DIMENSI DINAMIS SECARA MATEMATIS BERDASARKAN JUMLAH DATA
            // ====================================================================
            let bootstrapCol = "col-6"; // Jika loket banyak, default 2 kolom berdampingan
            let rowHeightPercent = "48%"; // Tinggi default kotak loket (muat 4 loket)
            let fontSizeNumber = "4.2rem"; // Ukuran default font nomor panggilan
            let fontSizeJurusan = "0.85rem"; // Ukuran default teks jurusan

            if (totalLoket <= 2) {
                bootstrapCol = "col-12"; // Jika hanya 1-2 loket, buat melebar penuh 1 kolom
                rowHeightPercent = (totalLoket === 1) ? "100%" : "48%";
                fontSizeNumber = (totalLoket === 1) ? "8rem" : "5.5rem";
                fontSizeJurusan = (totalLoket === 1) ? "1.2rem" : "0.95rem";
            } else if (totalLoket > 4 && totalLoket <= 6) {
                rowHeightPercent = "31%"; // Jika ada 5-6 loket, tinggi mengecil jadi 3 baris
                fontSizeNumber = "3.2rem";
                fontSizeJurusan = "0.75rem";
            } else if (totalLoket > 6) {
                rowHeightPercent = "23%"; // Jika di atas 6 loket, tinggi mengecil jadi 4 baris
                fontSizeNumber = "2.5rem";
                fontSizeJurusan = "0.7rem";
            }

            let html = '<div class="row w-100 m-0 h-100 align-content-between">';
            
            data.forEach(function(lk) {
                let key = 'loket_' + lk.id_loket;
                let isNew = false;
                let nomorSekarang = lk.no_sekarang ? lk.no_sekarang : '-';

                if (localCacheNumbers[key] !== undefined && localCacheNumbers[key] != nomorSekarang && nomorSekarang != '-') {
                    isNew = true;
                    playTvAudio(nomorSekarang, lk.loket);
                }
                localCacheNumbers[key] = nomorSekarang;

                let flashClass = isNew ? 'flash-active' : '';

                // Suntikkan style height dinamis secara inline per kotak loket
                html += '<div class="' + bootstrapCol + ' px-1 mb-2" style="height: ' + rowHeightPercent + ';">' +
                            '<div class="loket-card text-center">' +
                                '<div class="loket-title text-uppercase">LOKET ' + lk.loket + '</div>' +
                                '<div class="loket-body-tv ' + flashClass + '">' +
                                    '<div class="number-box shadow-inner" style="font-size: ' + fontSizeNumber + ';">' + nomorSekarang + '</div>' +
                                    '<!-- Teks jurusan lebar penuh memanjang ke bawah (tanpa limit & tanpa terpotong ...) -->' +
                                    '<div class="jurusan-text-tv text-center" style="font-size: ' + fontSizeJurusan + '; white-space: normal; word-wrap: break-word; overflow: visible; display: block; flex-grow: 1; margin-top: 4px;">' +
                                        '<i class="fas fa-road mr-1 text-white-50"></i> ' + lk.rute_tujuan +
                                    '</div>'
                                    +
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

<!-- Pustaka JavaScript Utama -->
<script src="<?php echo base_url('assets/admin/vendor/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/admin/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>

<script>
let localCacheNumbers = {};

function playTvAudio(nomor, loketId) {
    const bell = new Audio("<?php echo base_url('assets/admin/audio/Airport_Bell.mp3'); ?>");
    bell.volume = 0.9;
    bell.play();

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

function updateLiveDisplay() {
    $.ajax({
        url: "<?php echo base_url('antrian/get_live_display'); ?>",
        type: 'get',
        dataType: 'json',
        success: function(data) {
            if (!data || data.length === 0) {
                $('#display_loket_container').html(
                    '<div class="col-12 text-center text-muted py-5">' +
                    '<i class="fas fa-info-circle fa-2x mb-2 d-block text-warning"></i>' +
                    'Belum ada meja loket petugas yang aktif dinas saat ini.' +
                    '</div>'
                );
                return;
            }

            let html = '<div class="row w-100 m-0">';
            data.forEach(function(lk) {
                let key = 'loket_' + lk.id_loket;
                let isNew = false;
                let nomorSekarang = lk.no_sekarang ? lk.no_sekarang : '0';

                if (localCacheNumbers[key] !== undefined && localCacheNumbers[key] != nomorSekarang && nomorSekarang != '0') {
                    isNew = true;
                    playTvAudio(nomorSekarang, lk.loket);
                }
                localCacheNumbers[key] = nomorSekarang;

                let flashClass = isNew ? 'flash-active' : '';

                html += '<div class="col-xl-6 col-md-6 mb-4 px-2">' +
                            '<div class="loket-card text-center">' +
                                '<div class="loket-title text-uppercase">LOKET ' + lk.loket + '</div>' +
                                '<div class="py-2 ' + flashClass + '">' +
                                    '<div class="number-box shadow-inner">' + nomorSekarang + '</div>' +
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

// Menjalankan Semua Fungsi Otomatis Saat Dokumen Siap
$(document).ready(function() {
    updateLiveDisplay();
    setInterval(updateLiveDisplay, 3000);

    setInterval(function() {
        let date = new Date();
        $('#tv_clock').text(date.toLocaleTimeString('id-ID'));
    }, 1000);
});
</script>

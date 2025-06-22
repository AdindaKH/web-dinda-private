$(document).ready(function () {
    // Aktifkan date range picker
    $('#daterange').daterangepicker({
        locale: {
            format: 'DD-MM-YYYY',
            applyLabel: "Pilih",
            cancelLabel: "Batal",
            daysOfWeek: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
            monthNames: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
            firstDay: 1
        },
        showDropdowns: true // 👈 ini bagian tambahan biar tahun bisa dipilih
    });


    const form = $('#filterForm');

    // Submit otomatis saat apply date
    $('#daterange').on('apply.daterangepicker', function(ev, picker) {
        form.submit();
    });

    // Ajax submit pencarian
    form.on('submit', function(e) {
        const isDownload = form.find('button[name=export]').is(':focus');
        if (isDownload) return; // allow Excel/PDF download

        e.preventDefault(); // prevent reload
        $.ajax({
            url: form.attr('action') || window.location.href,
            data: form.serialize(),
            success: function(response) {
                $('#tabelTransaksi').html(response);
            },
            error: function() {
                alert('Gagal memuat data');
            }
        });
    });

    // Auto-submit pencarian
    let timeout = null;
    $('#q').on('input', function () {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            form.submit();
        }, 500);
    });
});

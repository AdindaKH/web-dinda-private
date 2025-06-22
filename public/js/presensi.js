document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById('searchKaryawan');

    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const keyword = this.value.toLowerCase();
            const rows = document.querySelectorAll("table tbody tr");

            rows.forEach(row => {
                const nama = row.querySelector("td:nth-child(2)")?.textContent.toLowerCase();
                if (nama.includes(keyword)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    }
});

// pilih kloter
document.getElementById('kloter_id').addEventListener('change', function () {
    const id = this.value;
    if (id) {
        window.location.href = `/operator/presensi/${id}`;
    }
});

document.getElementById('btnLihatKloter').addEventListener('click', function () {
    const select = document.getElementById('kloter_id');
    const id = select.value;

    if (id) {
        window.location.href = `/operator/gaji/${id}`;
    } else {
        alert('Pilih kloter terlebih dahulu');
    }
});

// checkbox mode otomatis
const checkbox = document.getElementById('modeOtomatis');
const STORAGE_KEY = 'mode_presensi_otomatis';

// Inisialisasi: ambil dari localStorage
let modeOtomatis = localStorage.getItem(STORAGE_KEY);
if (modeOtomatis === null) {
    // default true jika belum pernah disetel
    modeOtomatis = 'true';
    localStorage.setItem(STORAGE_KEY, modeOtomatis);
}

checkbox.checked = modeOtomatis === 'true';
toggleInputManualJam(checkbox.checked);

checkbox.addEventListener('change', function () {
    const isChecked = this.checked;
    localStorage.setItem(STORAGE_KEY, isChecked.toString());
    toggleInputManualJam(isChecked);
});

function toggleInputManualJam(isOtomatis) {
    document.querySelectorAll('.inputManualJam').forEach(el => {
        el.classList.toggle('d-none', isOtomatis);
    });
}
function getCurrentTime() {
    const now = new Date();
    return now.toTimeString().slice(0, 8);
}

document.querySelectorAll('.formPresensi').forEach(form => {
    form.addEventListener('submit', function (e) {
        const hiddenInput = form.querySelector('.inputJam');
        const manualInput = form.querySelector('.inputManualJam');
        const mode = localStorage.getItem(STORAGE_KEY) === 'true';

        const jamSekarang = getCurrentTime();
        let jamInput = '';

        if (mode) {
            jamInput = jamSekarang;
        } else {
            jamInput = manualInput.value;
            if (!jamInput) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Jam tidak valid!',
                    text: 'Jam manual tidak boleh kosong.',
                    timer: 5000,
                    timerProgressBar: true,
                    confirmButtonText: 'Oke',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                });
                return;
            }

            // Lengkapi format menjadi HH:mm:ss jika hanya jam dan menit
            if (jamInput.length === 5) {
                jamInput += ':00';
            }

            if (jamInput > jamSekarang) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Jam tidak valid!',
                    text: 'Jam tidak boleh lebih dari waktu sekarang.',
                    timer: 3000, // 5 detik
                    timerProgressBar: true,
                    confirmButtonText: 'Oke',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        const content = Swal.getHtmlContainer();
                        if (content) {
                            content.querySelector('button.swal2-confirm')?.focus();
                        }
                    }
                });
                return;
            }
        }

        hiddenInput.value = jamInput;
    });
});



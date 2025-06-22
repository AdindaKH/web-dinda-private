document.addEventListener('DOMContentLoaded', function () {
    
    const form = document.getElementById('filterForm');
    const genderInput = document.getElementById('genderInput');
    const toggleBtn = document.getElementById('toggleGenderBtn');
    const namaInput = document.getElementById('namaInput');

    // Untuk siklus filter gender
    const states = ['L', 'P', '']; // Terakhir berarti Semua
    let currentIndex = states.indexOf(genderInput.value || '');

    toggleBtn.addEventListener('click', function () {
        // Ganti index
        currentIndex = (currentIndex + 1) % states.length;
        const gender = states[currentIndex];
        genderInput.value = gender;

        // Ubah teks dan warna tombol
        if (gender === 'L') {
            toggleBtn.className = 'btn btn-sm btn-outline-primary';
            toggleBtn.innerText = 'L';
        } else if (gender === 'P') {
            toggleBtn.className = 'btn btn-sm btn-outline-danger';
            toggleBtn.innerText = 'P';
        } else {
            toggleBtn.className = 'btn btn-sm btn-outline-secondary';
            toggleBtn.innerText = 'Semua Gender';
        }

        form.submit();
    });

    // Nama: auto submit setelah delay
    let timer;
    namaInput.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(() => {
            form.submit();
        }, 500);
    });
    
    document.querySelectorAll('.formDelete').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
});

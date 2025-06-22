// filter supplier by name and alamat
document.addEventListener('DOMContentLoaded', function () {
    const keywordInput = document.getElementById('keywordInput');
    const kategoriInput = document.getElementById('kategoriInput');
    const kategoriButtons = document.querySelectorAll('.kategoriBtn');
    const form = document.getElementById('filterForm');

    let timer;
    const delay = 500;

    // Auto submit saat ketik keyword
    if (keywordInput) {
        keywordInput.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(() => {
                form.submit();
            }, delay);
        });
    }

    // Klik tombol kategori
    kategoriButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const selected = this.getAttribute('data-value');
            kategoriInput.value = selected;
            form.submit();
        });
    });
});

// SweetAlert konfirmasi hapus
document.querySelectorAll('.formDeleteSupplier').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
});

// Populate modal edit
document.querySelectorAll('.btn-edit').forEach(button => {
    button.addEventListener('click', function () {
        document.getElementById('editNama').value = this.dataset.nama;
        document.getElementById('editAlamat').value = this.dataset.alamat;
        document.getElementById('editTelepon').value = this.dataset.telepon;
        document.getElementById('editRekening').value = this.dataset.rekening || '';
        document.getElementById('editKategori').value = this.dataset.kategori;
        document.getElementById('editForm').action = this.dataset.action;
    });
});
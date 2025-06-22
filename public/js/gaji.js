document.addEventListener("DOMContentLoaded", function () {
    const editModal = document.getElementById('editModal');
    const inputId = document.getElementById('inputKloterId');
    const inputJumlahTon = document.getElementById('inputJumlahTon');
    const inputHargaPerTon = document.getElementById('inputHargaPerTon');

    document.querySelectorAll('[data-bs-target="#editModal"]').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const jumlah = this.getAttribute('data-jumlah');
            const harga = this.getAttribute('data-harga');

            inputId.value = id;
            inputJumlahTon.value = jumlah;
            inputHargaPerTon.value = harga;
        });
    });

    document.querySelectorAll('[data-bs-target^="#editModal"]').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const jumlah = this.getAttribute('data-jumlah');
            const harga = this.getAttribute('data-harga');

            const modalId = this.getAttribute('data-bs-target'); // contoh: "#editModal5"
            const modal = document.querySelector(modalId);

            if (!modal) return; // cegah error jika modal tidak ditemukan

            const inputId = modal.querySelector('#inputKloterId');
            const inputJumlahTon = modal.querySelector('#inputJumlahTon');
            const inputHargaPerTon = modal.querySelector('#inputHargaPerTon');

            inputId.value = id;
            inputJumlahTon.value = jumlah;
            inputHargaPerTon.value = harga;
        });
    });

    const searchInput = document.getElementById('searchInput');
    let typingTimer;
    const doneTypingInterval = 500; // 0.5 detik setelah user berhenti mengetik

    if (searchInput) {
        const baseUrl = searchInput.dataset.url;

        searchInput.addEventListener('keyup', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                const keyword = searchInput.value.trim();

                if (keyword === '') {
                    window.location.href = baseUrl;
                } else {
                    const url = new URL(baseUrl, window.location.origin);
                    url.searchParams.set('search', keyword);
                    window.location.href = url.toString();
                }
            }, doneTypingInterval);
        });

        searchInput.addEventListener('keydown', function () {
            clearTimeout(typingTimer);
        });
    }

    const forms = document.querySelectorAll(".form-kloter-selesai");
    forms.forEach(form => {
        form.addEventListener("submit", function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Yakin ingin menyelesaikan kloter ini?',
                text: "Data akan diproses ke riwayat gaji.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Selesaikan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});

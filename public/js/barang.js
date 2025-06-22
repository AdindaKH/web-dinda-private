document.addEventListener('DOMContentLoaded', function () {
   
    // filter untuk input nama barang
    const namaInput = document.getElementById('namaInput');
    if (namaInput) {
        let typingTimer;
        const doneTypingInterval = 500; // Delay submit 0.5 detik setelah ketikan berhenti

        namaInput.addEventListener('input', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, doneTypingInterval);
        });
    }

    // Peringatan Ubah Kateogri saat Edit Barang
    document.querySelectorAll('.formEditBarang').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const kategoriLama = form.querySelector('.kategori-lama').value;
            const kategoriBaru = form.querySelector('.kategori-select').value;

            const melibatkanProduk = (kategoriLama === 'produk' || kategoriBaru === 'produk');

            if (melibatkanProduk && kategoriLama !== kategoriBaru) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Anda akan mengubah kategori yang melibatkan Produk. Yakin ingin melanjutkan?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjutkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const inputConfirm = document.createElement("input");
                        inputConfirm.type = "hidden";
                        inputConfirm.name = "confirm_produk";
                        inputConfirm.value = "1";
                        form.appendChild(inputConfirm);
                        form.submit();
                    }
                });
            } else {
                form.submit();
            }
        });
    });

    // Peringatan Hapus Barang
    document.querySelectorAll('.formDeleteBarang').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Hapus Data?',
                text: "Data akan dihapus secara permanen. Lanjutkan?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Cek Nama Barang Sudah Ada atau Belum
    const dataDiv = document.getElementById('barang-data');
    if (dataDiv?.dataset.barangSudahAda === '1') {
        Swal.fire({
            icon: 'warning',
            title: 'Barang Sudah Ada!',
            text: 'Barang dengan nama tersebut sudah ada. Sistem akan menyesuaikan stok.',
            confirmButtonText: 'Mengerti'
        });
    }
    
    // Fungsi debounce (untuk input cepat)
    function debounce(func, delay = 300) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Cek Nama Barang Saat Modal Dibuka
    const createModal = document.getElementById('createModal');
    if (createModal) {
        const debouncedHandler = debounce(handleNamaBarangChange, 300);

        createModal.addEventListener('shown.bs.modal', () => {
            const namaBarangInput = document.querySelector('#nama_barang');
            if (!namaBarangInput) return;

            // Pastikan hanya satu event listener terpasang
            namaBarangInput.removeEventListener('input', debouncedHandler);
            namaBarangInput.addEventListener('input', debouncedHandler);
        });
    }

    // fungsi cek nama barang dan tampilkan kode
    function handleNamaBarangChange() {
        const nama = this.value.trim();
        const filter = document.querySelector('input[name="filter"]')?.value || '';

        if (nama.length < 3) return; // minimal 3 karakter agar tidak spam server

        fetch(`/barang/check?nama_barang=${encodeURIComponent(nama)}&filter=${filter}`)
            .then(response => {
                if (!response.ok) throw new Error("Gagal fetch");
                return response.json();
            })
            .then(data => {
                const inputBarangSudahAda = document.getElementById('barangSudahAda');
                const inputNamaBarang = document.getElementById('namaBarang');
                const inputNewKode = document.getElementById('newKode');

                if (inputBarangSudahAda && inputNamaBarang && inputNewKode) {
                    inputBarangSudahAda.value = data.barang_sudah_ada ? '1' : '0';
                    inputNamaBarang.value = data.nama_barang || '';
                    inputNewKode.value = data.kode || '';
                    updateKodeInput();
                } else {
                    console.warn("Elemen hidden input tidak ditemukan");
                }
            })
            .catch(error => {
                console.error("Gagal mengambil data dari /barang/check:", error);
            });
    }

    // Fungsi untuk Update Tampilan Kode Barang
    function updateKodeInput() {
        const kodeInputWrapper = document.getElementById('kodeInputWrapper');
        if (!kodeInputWrapper) return;

        const barangSudahAda = document.getElementById('barangSudahAda')?.value === '1';
        const namaBarang = document.getElementById('namaBarang')?.value || '';
        const newKode = document.getElementById('newKode')?.value || '';

        kodeInputWrapper.innerHTML = ''; // Kosongkan

        const label = document.createElement('label');
        label.className = 'form-label';
        label.setAttribute('for', 'kode');
        label.innerText = 'Kode';
        kodeInputWrapper.appendChild(label);

        const input = document.createElement('input');
        input.id = 'kode';
        input.type = 'text';
        input.className = 'form-control';
        input.readOnly = true;

        if (barangSudahAda && namaBarang !== '') {
            input.classList.add('is-invalid');
            input.value = newKode || 'Barang sudah ada';
            input.disabled = true;

            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.innerHTML = `Barang <strong>${namaBarang}</strong> sudah ada. Kode: <strong>${newKode}</strong>`;

            kodeInputWrapper.appendChild(input);
            kodeInputWrapper.appendChild(feedback);
        } else {
            input.value = newKode;
            kodeInputWrapper.appendChild(input);
        }
    }

});

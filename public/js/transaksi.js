document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('searchTransaksi');
    const rows = document.querySelectorAll('table tbody tr');

    input.addEventListener('input', function () {
        const keyword = this.value.toLowerCase();

        rows.forEach(row => {
            const kodeTransaksi = row.cells[2].textContent.toLowerCase();
            const kodeBarang    = row.cells[3].textContent.toLowerCase();
            const mitra         = row.cells[4].textContent.toLowerCase();
            const namaBarang    = row.cells[5].textContent.toLowerCase();

            const isMatch = [kodeTransaksi, kodeBarang, mitra, namaBarang].some(text => text.includes(keyword));
            row.style.display = isMatch ? '' : 'none';
        });
    });

    const form = document.getElementById('formFilterTanggal');
    const tanggalMulai = document.getElementById('tanggalMulai');
    const tanggalAkhir = document.getElementById('tanggalAkhir');

    // Saat tanggal akhir berubah, kirim form otomatis
    tanggalAkhir.addEventListener('change', function () {
        if (tanggalMulai.value && tanggalAkhir.value) {
            form.submit();
        }
    });
});

$(document).ready(function () {
    $('#searchTransaksi').on('input', function () {
        $('#exportSearchQuery').val($(this).val());
    });
});

// Data dari backend (harus disediakan dari Blade)
const barangsData = window.barangsData || {};
const suppliersData = window.suppliersData || {};

function konversiSatuanKeKg(satuan, qtyHistori) {
    switch (satuan) {
        case 'ton': return qtyHistori * 1000;
        case 'kg': return qtyHistori;
        case 'g': return qtyHistori / 1000;
        default: return qtyHistori;
    }
}

// --------------- CREATE MODAL ---------------
const kategoriSelect = document.getElementById('kategori');
const barangSelect = document.getElementById('barang_id');
const supplierSelect = document.getElementById('supplier_id');
const qtyInput = document.getElementById('qtyHistori');
const satuanSelect = document.getElementById('satuan');
const jumlahRpInput = document.getElementById('jumlahRp');



function fillBarangOptions(kategori) {
    const tipe = kategori === 'pengeluaran' ? 'pendukung' : 'produk';
    barangSelect.innerHTML = '<option value="">-- Pilih Barang --</option>';

    if (barangsData[tipe]) {
        barangsData[tipe].forEach(barang => {
            const option = document.createElement('option');
            option.value = barang.id;
            option.textContent = barang.nama;
            option.dataset.harga = barang.harga;
            barangSelect.appendChild(option);
        });
    }
    jumlahRpInput.value = '';
}

function fillSupplierOptions(kategori) {
    supplierSelect.innerHTML = '<option value="">-- Pilih Supplier --</option>';
    const tipe = kategori === 'pengeluaran' ? 'konsumen' : 'pemasok';
    suppliersData[tipe]?.forEach(supplier => {
        const option = document.createElement('option');
        option.value = supplier.id;
        option.textContent = supplier.nama;
        supplierSelect.appendChild(option);
    });
}

function hitungJumlahRpCreate() {
    const hargaBarang = parseFloat(barangSelect.selectedOptions[0]?.dataset.harga || 0);
    const qty = parseFloat(qtyInput.value || 0);
    const satuan = satuanSelect.value;
    const qtyKg = konversiSatuanKeKg(satuan, qty);

    // Kardus
    const kardusSelect = document.getElementById('jenis_kardus');
    const jumlahKardusInput = document.getElementById('jumlah_kardus');
    const hargaKardus = parseFloat(kardusSelect?.selectedOptions[0]?.dataset.harga || 0);
    const jumlahKardus = parseFloat(jumlahKardusInput?.value || 0);

    const totalBarang = hargaBarang * qtyKg;
    const totalKardus = hargaKardus * jumlahKardus;

    jumlahRpInput.value = (totalBarang + totalKardus).toFixed(0);
}

if (kategoriSelect) {
    kategoriSelect.addEventListener('change', () => {
        fillBarangOptions(kategoriSelect.value);
        fillSupplierOptions(kategoriSelect.value);
        toggleInputKardus(kategoriSelect.value);
    });
    barangSelect.addEventListener('change', hitungJumlahRpCreate);
    qtyInput.addEventListener('input', hitungJumlahRpCreate);
    satuanSelect.addEventListener('change', hitungJumlahRpCreate);
    document.getElementById('jenis_kardus')?.addEventListener('change', hitungJumlahRpCreate);
    document.getElementById('jumlah_kardus')?.addEventListener('input', hitungJumlahRpCreate);


    $('#createModal').on('show.bs.modal', () => {
        kategoriSelect.value = '';
        barangSelect.innerHTML = '<option value="">-- Pilih Barang --</option>';
        supplierSelect.innerHTML = '<option value="">-- Pilih Supplier --</option>';
        qtyInput.value = '';
        satuanSelect.value = '';
        jumlahRpInput.value = '';
    });
}

// --------------- EDIT MODAL ---------------
const kategoriEdit = document.getElementById('editKategori');
const barangEdit = document.getElementById('editBarang');
const supplierEdit = document.getElementById('editSupplier');
const qtyInputEdit = document.getElementById('editQty');
const satuanEdit = document.getElementById('editSatuan');
const jumlahRpInputEdit = document.getElementById('editJumlahRp');

// Tambahkan listener agar saat kategori diubah saat edit, barang & supplier di-reload
kategoriEdit?.addEventListener('change', function () {
    fillBarangOptionsEdit(this.value, barangEdit.value);
    fillSupplierOptionsEdit(this.value, supplierEdit.value);
    toggleInputKardusEdit(this.value);
    hitungJumlahRpEdit();
});


function fillBarangOptionsEdit(kategori, selectedId) {
    const tipe = kategori === 'pengeluaran' ? 'pendukung' : 'produk';
    barangEdit.innerHTML = '<option value="">-- Pilih Barang --</option>';

    if (barangsData[tipe]) {
        barangsData[tipe].forEach(barang => {
            const option = document.createElement('option');
            option.value = barang.id;
            option.textContent = barang.nama;
            option.dataset.harga = barang.harga;
            if (barang.id == selectedId) option.selected = true;
            barangEdit.appendChild(option);
        });
    }
}

function fillSupplierOptionsEdit(kategori, selectedId) {
    supplierEdit.innerHTML = '<option value="">-- Pilih Supplier --</option>';
    const tipe = kategori === 'pengeluaran' ? 'konsumen' : 'pemasok';
    suppliersData[tipe]?.forEach(supplier => {
        const option = document.createElement('option');
        option.value = supplier.id;
        option.textContent = supplier.nama;
        if (supplier.id == selectedId) option.selected = true;
        supplierEdit.appendChild(option);
    });
}

function hitungJumlahRpEdit() {
    const hargaBarang = parseFloat(barangEdit.selectedOptions[0]?.dataset.harga || 0);
    const qty = parseFloat(qtyInputEdit.value || 0);
    const satuan = satuanEdit.value;
    const qtyKg = konversiSatuanKeKg(satuan, qty);

    // Kardus
    const kardusSelect = document.getElementById('edit_jenis_kardus');
    const jumlahKardusInput = document.getElementById('edit_jumlah_kardus');
    const hargaKardus = parseFloat(kardusSelect?.selectedOptions[0]?.dataset.harga || 0);
    const jumlahKardus = parseFloat(jumlahKardusInput?.value || 0);

    const totalBarang = hargaBarang * qtyKg;
    const totalKardus = hargaKardus * jumlahKardus;

    jumlahRpInputEdit.value = (totalBarang + totalKardus).toFixed(0);
}

function toggleInputKardus(kategori) {
    const showKardus = kategori === 'pemasukan';

    const jenisKardusGroup = document.getElementById('group_jenis_kardus');
    const jumlahKardusGroup = document.getElementById('group_jumlah_kardus');

    jenisKardusGroup.style.display = showKardus ? 'block' : 'none';
    jumlahKardusGroup.style.display = showKardus ? 'block' : 'none';

    if (!showKardus) {
        document.getElementById('jenis_kardus').value = '';
        document.getElementById('jumlah_kardus').value = '';
    }
}
toggleInputKardus(''); // pastikan disembunyikan saat awal

function toggleInputKardusEdit(kategori) {
    const show = kategori === 'pemasukan';
    document.getElementById('edit_group_jenis_kardus').style.display = show ? 'block' : 'none';
    document.getElementById('edit_group_jumlah_kardus').style.display = show ? 'block' : 'none';

    if (!show) {
        document.getElementById('edit_jenis_kardus').value = '';
        document.getElementById('edit_jumlah_kardus').value = '';
        toggleInputKardusEdit(data.kategori);
    }
}
// toggleInputKardusEdit(''); // pastikan disembunyikan saat awal


$('.btn-edit').on('click', function () {
    const data = this.dataset;
    fillBarangOptionsEdit(data.kategori, data.barang_id);
    fillSupplierOptionsEdit(data.kategori, data.supplier_id);

    kategoriEdit.value = data.kategori;
    qtyInputEdit.value = data.qty;
    satuanEdit.value = data.satuan;
    jumlahRpInputEdit.value = data.jumlahrp;
    document.getElementById('editWaktu').value = data.waktu;
    document.getElementById('formEdit').action = `/operator/transaksi/${data.id}`;
    // 🔧 Tunggu sampai opsi selesai dimuat
    setTimeout(() => {
        document.getElementById('edit_jenis_kardus').value = data.jenis_kardus_id || '';
        document.getElementById('edit_jumlah_kardus').value = data.jumlah_kardus || '';
        toggleInputKardusEdit(data.kategori);
    }, 50); // bisa 0–100ms, tergantung kompleksitas

    // Debug opsional
    console.log("Jenis Kardus ID:", data.jenis_kardus_id);
    console.log("Jumlah Kardus:", data.jumlah_kardus);

    console.log('Kategori:', kategori);
    console.log('Barang ID:', editBarang);
    console.log('BarangsData:', barangsData);
    console.log('Suppliers:', suppliersData);
    console.log('Selected Barang:', barangEdit.value);
    console.log('Selected Supplier:', supplierEdit.value);
    console.log('Qty:', qtyInputEdit.value);
    console.log('Satuan:', satuanEdit.value);
    console.log('Jumlah Rp:', jumlahRpInputEdit.value);
    console.log("Barang options:", $('#editBarang').html());
    console.log("Supplier options:", $('#editSupplier').html());
    console.log("Kategori:", kategoriEdit.value);
    console.log("Qty Input:", qtyInputEdit.value);
    console.log("Satuan:", satuanEdit.value);
});

barangEdit?.addEventListener('change', hitungJumlahRpEdit);
qtyInputEdit?.addEventListener('input', hitungJumlahRpEdit);
satuanEdit?.addEventListener('change', hitungJumlahRpEdit);
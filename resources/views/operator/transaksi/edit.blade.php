<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" id="formEdit">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Transaksi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <!-- Kategori -->
          <div class="mb-3">
            <label for="editKategori" class="form-label">Kategori</label>
            <select id="editKategori" name="kategori" class="form-control">
              <option value="pemasukan">Pemasukan</option>
              <option value="pengeluaran">Pengeluaran</option>
            </select>
          </div>

          <!-- Barang (akan diisi dinamis via JS) -->
          <div class="mb-3">
            <label for="editBarang" class="form-label">Barang</label>
            <select id="editBarang" name="barang_id" class="form-control"></select>
          </div>

          <!-- Supplier -->
          <div class="mb-3">
            <label for="editSupplier" class="form-label">Mitra</label>
            <select id="editSupplier" name="supplier_id" class="form-control">
              @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
              @endforeach
            </select>
          </div>

          <!-- Qty -->
          <div class="mb-3">
            <label for="editQty" class="form-label">Qty</label>
            <input type="number" name="qtyHistori" id="editQty" class="form-control">
          </div>

          <!-- Satuan -->
          <div class="mb-3">
            <label for="editSatuan" class="form-label">Satuan</label>
            <select name="satuan" id="editSatuan" class="form-control" required>
              <option value="" disabled selected>-- Pilih Satuan --</option>
              <option value="ton">Ton</option>
              <option value="kg">Kg</option>
              <option value="g">Gram</option>
            </select>
          </div>

          <!-- Jenis Kardus -->
          <div class="mb-3" id="edit_group_jenis_kardus">
            <label for="edit_jenis_kardus">Jenis Kardus</label>
            <select name="jenis_kardus" id="edit_jenis_kardus" class="form-control">
              <option value="">-- Pilih Kardus --</option>
              @foreach($kardusList as $kardus)
                <option value="{{ $kardus->barang->id }}" data-harga="{{ $kardus->barang->harga }}">
                  {{ $kardus->barang->nama_barang }}
                </option>
              @endforeach
            </select>
          </div>

          <!-- Jumlah Kardus -->
          <div class="mb-3" id="edit_group_jumlah_kardus">
            <label for="edit_jumlah_kardus">Jumlah Kardus</label>
            <input type="number" name="jumlah_kardus" id="edit_jumlah_kardus" class="form-control" min="0">
          </div>

          <!-- Jumlah (Rp) -->
          <div class="mb-3">
            <label for="editJumlahRp" class="form-label">Jumlah (Rp)</label>
            <input type="number" name="jumlahRp" id="editJumlahRp" class="form-control" required>
          </div>

          <!-- Waktu Transaksi -->
          <div class="mb-3">
            <label for="editWaktu" class="form-label">Waktu Transaksi</label>
            <input type="datetime-local" name="waktu_transaksi" id="editWaktu" class="form-control">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </div>
    </form>
  </div>
</div>

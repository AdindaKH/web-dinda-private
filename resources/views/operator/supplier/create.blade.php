<!-- Modal Create Supplier -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('supplier.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Mitra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Mitra</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" required>{{ old('alamat') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="no_tlp" class="form-label">No Telepon</label>
                        <input type="text" class="form-control" id="no_tlp" name="no_tlp" value="{{ old('no_tlp') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="no_rekening" class="form-label">No Rekening</label>
                        <input type="text" class="form-control" id="no_rekening" name="no_rekening" value="{{ old('no_rekening') }}">
                    </div>

                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori Mitra Bisnis</label>
                        <select name="kategori" id="kategori" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="pemasok" {{ old('kategori') == 'pemasok' ? 'selected' : '' }}>Pemasok</option>
                            <option value="konsumen" {{ old('kategori') == 'konsumen' ? 'selected' : '' }}>Konsumen</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

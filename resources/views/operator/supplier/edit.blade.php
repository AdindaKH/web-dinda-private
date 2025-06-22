<!-- Modal Edit Supplier -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Mitra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editNama" class="form-label">Nama Mitra</label>
                        <input type="text" class="form-control" id="editNama" name="nama" required>
                    </div>

                    <div class="mb-3">
                        <label for="editAlamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="editAlamat" name="alamat" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="editTelepon" class="form-label">No Telepon</label>
                        <input type="text" class="form-control" id="editTelepon" name="no_tlp" required>
                    </div>

                    <div class="mb-3">
                        <label for="editRekening" class="form-label">No Rekening</label>
                        <input type="text" class="form-control" id="editRekening" name="no_rekening">
                    </div>

                    <div class="mb-3">
                        <label for="editKategori" class="form-label">Kategori Mitra Bisnis</label>
                        <select name="kategori" id="editKategori" class="form-control" required>
                            <option value="pemasok">Pemasok</option>
                            <option value="konsumen">Konsumen</option>
                        </select>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
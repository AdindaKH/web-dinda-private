
<div class="modal modal-ct fade" id="editModal{{ $barang->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $barang->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel{{ $barang->id }}">Edit Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form class="formEditBarang" method="POST" action="{{ route('barang.update', $barang->id) }}">
            @csrf
            @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" required>
                    </div>

                    @php
                        $kategori = 'lainnya';
                        if ($barang->produk) $kategori = 'produk';
                        elseif ($barang->pendukung) $kategori = 'pendukung'; // Sesuaikan jika hanya ada pendukung
                    @endphp

                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="kategori" class="form-select kategori-select" required>
                            <option value="pendukung" {{ $kategori == 'pendukung' ? 'selected' : '' }}>Pendukung</option>
                            <option value="produk" {{ $kategori == 'produk' ? 'selected' : '' }}>Produk</option>
                        </select>
                        <input type="hidden" class="kategori-lama" value="{{ $kategori }}">
                    </div>

                    @if(request('filter') != 'pendukung')
                        <div class="mb-3">
                            <label class="form-label">Tanggal Expired</label>
                            <input type="date" class="form-control" name="exp" value="{{ old('exp', $barang->exp ? date('Y-m-d', strtotime($barang->exp)) : '') }}">
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Harga (Rp)</label>
                        <input type="number" class="form-control" name="harga" value="{{ old('harga', $barang->harga) }}" required>
                    </div>
                    
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

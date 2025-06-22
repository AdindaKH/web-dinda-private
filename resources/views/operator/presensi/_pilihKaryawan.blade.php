<!-- Modal Pilih Karyawan -->
<div class="modal fade" id="pilihKaryawanModal" tabindex="-1" aria-labelledby="pilihKaryawanLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" action="{{ route('presensi.pilih-karyawan') }}">
        @csrf
        <input type="hidden" name="kloter_id" value="{{ $selectedKloter->id }}">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Pilih Karyawan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <div class="row">
                  @foreach($semuaKaryawan as $karyawan)
                  <div class="col-md-4">
                      <div class="form-check">
                          <input class="form-check-input" type="checkbox" name="karyawan_ids[]" value="{{ $karyawan->id }}"
                            {{ in_array($karyawan->id, $karyawans->pluck('id')->toArray()) ? 'checked' : '' }}>
                          <label class="form-check-label">{{ $karyawan->nama }}</label>
                      </div>
                  </div>
                  @endforeach
              </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Simpan</button>
          </div>
        </div>
    </form>
  </div>
</div>
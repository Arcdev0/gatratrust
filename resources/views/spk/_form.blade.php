@csrf

<div class="row">
    <div class="col-md-6 form-group">
        <label>Nomor SPK</label>
        <input type="text" name="nomor" class="form-control @error('nomor') is-invalid @enderror"
            value="{{ old('nomor', $spk->nomor ?? '') }}" required>
        @error('nomor')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 form-group">
        <label>Tanggal</label>
        <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror"
            value="{{ old('tanggal', isset($spk) && $spk->tanggal ? $spk->tanggal->format('Y-m-d') : '') }}" required>
        @error('tanggal')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<h5 class="mt-3">Data Pegawai</h5>
<div class="row">
    <div class="col-md-6 form-group">
        <label>Nama</label>
        <input type="text" name="pegawai_nama" class="form-control" value="{{ old('pegawai_nama', $spk->pegawai_nama ?? '') }}"
            required>
    </div>
    <div class="col-md-6 form-group">
        <label>Jabatan</label>
        <input type="text" name="pegawai_jabatan" class="form-control"
            value="{{ old('pegawai_jabatan', $spk->pegawai_jabatan ?? '') }}" required>
    </div>
    <div class="col-md-6 form-group">
        <label>Divisi</label>
        <input type="text" name="pegawai_divisi" class="form-control"
            value="{{ old('pegawai_divisi', $spk->pegawai_divisi ?? '') }}">
    </div>
    <div class="col-md-6 form-group">
        <label>NIK / ID Pegawai</label>
        <input type="text" name="pegawai_nik_id" class="form-control"
            value="{{ old('pegawai_nik_id', $spk->pegawai_nik_id ?? '') }}">
    </div>
</div>

<h5 class="mt-3">Data Perjalanan</h5>
<div class="row">
    <div class="col-md-6 form-group">
        <label>Tujuan Dinas</label>
        <input type="text" name="tujuan_dinas" class="form-control"
            value="{{ old('tujuan_dinas', $spk->tujuan_dinas ?? '') }}" required>
    </div>
    <div class="col-md-6 form-group">
        <label>Lokasi Perusahaan Tujuan</label>
        <input type="text" name="lokasi_perusahaan_tujuan" class="form-control"
            value="{{ old('lokasi_perusahaan_tujuan', $spk->lokasi_perusahaan_tujuan ?? '') }}">
    </div>
    <div class="col-md-12 form-group">
        <label>Alamat Lokasi</label>
        <textarea name="alamat_lokasi" rows="2" class="form-control">{{ old('alamat_lokasi', $spk->alamat_lokasi ?? '') }}</textarea>
    </div>
    <div class="col-md-12 form-group">
        <label>Maksud / Ruang Lingkup</label>
        <textarea name="maksud_ruang_lingkup" rows="2" class="form-control">{{ old('maksud_ruang_lingkup', $spk->maksud_ruang_lingkup ?? '') }}</textarea>
    </div>
    <div class="col-md-4 form-group">
        <label>Tanggal Berangkat</label>
        <input type="date" name="tanggal_berangkat" class="form-control"
            value="{{ old('tanggal_berangkat', isset($spk) && $spk->tanggal_berangkat ? $spk->tanggal_berangkat->format('Y-m-d') : '') }}"
            required>
    </div>
    <div class="col-md-4 form-group">
        <label>Tanggal Kembali</label>
        <input type="date" name="tanggal_kembali" class="form-control"
            value="{{ old('tanggal_kembali', isset($spk) && $spk->tanggal_kembali ? $spk->tanggal_kembali->format('Y-m-d') : '') }}"
            required>
    </div>
    <div class="col-md-4 form-group">
        <label>Lama Perjalanan (hari)</label>
        <input type="number" min="1" max="365" name="lama_perjalanan" class="form-control"
            value="{{ old('lama_perjalanan', $spk->lama_perjalanan ?? 1) }}" required>
    </div>
    <div class="col-md-4 form-group">
        <label>Sumber Biaya</label>
        <input type="text" name="sumber_biaya" class="form-control"
            value="{{ old('sumber_biaya', $spk->sumber_biaya ?? '') }}">
    </div>
    <div class="col-md-4 form-group">
        <label>Moda Transportasi</label>
        <select name="moda_transportasi" class="form-control" required>
            @foreach (['darat' => 'Darat', 'laut' => 'Laut', 'udara' => 'Udara'] as $value => $label)
                <option value="{{ $value }}"
                    {{ old('moda_transportasi', $spk->moda_transportasi ?? 'darat') === $value ? 'selected' : '' }}>
                    {{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 form-group">
        <label>Sumber Biaya Opsi</label>
        <select name="sumber_biaya_opsi" class="form-control" required>
            @foreach (['perusahaan' => 'Perusahaan', 'project' => 'Project', 'lainnya' => 'Lainnya'] as $value => $label)
                <option value="{{ $value }}"
                    {{ old('sumber_biaya_opsi', $spk->sumber_biaya_opsi ?? 'perusahaan') === $value ? 'selected' : '' }}>
                    {{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>

<h5 class="mt-3">Ditugaskan Oleh</h5>
<div class="row">
    <div class="col-md-6 form-group">
        <label>Nama</label>
        <input type="text" name="ditugaskan_oleh_nama" class="form-control"
            value="{{ old('ditugaskan_oleh_nama', $spk->ditugaskan_oleh_nama ?? 'Direktur Utama') }}" required>
    </div>
    <div class="col-md-6 form-group">
        <label>Jabatan</label>
        <input type="text" name="ditugaskan_oleh_jabatan" class="form-control"
            value="{{ old('ditugaskan_oleh_jabatan', $spk->ditugaskan_oleh_jabatan ?? 'Direktur') }}" required>
    </div>
</div>

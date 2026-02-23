@csrf

<div class="row">
    <div class="col-md-6 form-group">
        <label>Nomor SPK</label>
        <input type="text" name="nomor" class="form-control @error('nomor') is-invalid @enderror"
            value="{{ old('nomor', $spk?->nomor ?? ($newSpkNo ?? '')) }}" readonly>
        @error('nomor')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 form-group">
        <label>Tanggal</label>
        <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror"
            value="{{ old('tanggal', isset($spk) && $spk?->tanggal ? $spk?->tanggal->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
        @error('tanggal')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<h5 class="mt-3">Data Pegawai</h5>
<div class="row">
    <div class="col-md-6 form-group">
        <label>Nama</label>
        <input type="text" name="pegawai_nama" class="form-control @error('pegawai_nama') is-invalid @enderror"
            value="{{ old('pegawai_nama', $spk?->pegawai_nama ?? '') }}" required>
        @error('pegawai_nama')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 form-group">
        <label>Jabatan</label>
        <input type="text" name="pegawai_jabatan" class="form-control @error('pegawai_jabatan') is-invalid @enderror"
            value="{{ old('pegawai_jabatan', $spk?->pegawai_jabatan ?? '') }}" required>
        @error('pegawai_jabatan')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 form-group">
        <label>Divisi</label>
        <input type="text" name="pegawai_divisi" class="form-control @error('pegawai_divisi') is-invalid @enderror"
            value="{{ old('pegawai_divisi', $spk?->pegawai_divisi ?? '') }}">
        @error('pegawai_divisi')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 form-group">
        <label>NIK / ID Pegawai</label>
        <input type="text" name="pegawai_nik_id" class="form-control @error('pegawai_nik_id') is-invalid @enderror"
            value="{{ old('pegawai_nik_id', $spk?->pegawai_nik_id ?? '') }}">
        @error('pegawai_nik_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<h5 class="mt-3">Data Perjalanan</h5>
<div class="row">
    <div class="col-md-6 form-group">
        <label>Tujuan Dinas</label>
        <input type="text" name="tujuan_dinas" class="form-control @error('tujuan_dinas') is-invalid @enderror"
            value="{{ old('tujuan_dinas', $spk?->tujuan_dinas ?? '') }}" required>
        @error('tujuan_dinas')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 form-group">
        <label>Lokasi Perusahaan Tujuan</label>
        <input type="text" name="lokasi_perusahaan_tujuan"
            class="form-control @error('lokasi_perusahaan_tujuan') is-invalid @enderror"
            value="{{ old('lokasi_perusahaan_tujuan', $spk?->lokasi_perusahaan_tujuan ?? '') }}">
        @error('lokasi_perusahaan_tujuan')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-12 form-group">
        <label>Alamat Lokasi</label>
        <textarea name="alamat_lokasi" rows="2" class="form-control @error('alamat_lokasi') is-invalid @enderror">{{ old('alamat_lokasi', $spk?->alamat_lokasi ?? '') }}</textarea>
        @error('alamat_lokasi')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-12 form-group">
        <label>Maksud / Ruang Lingkup</label>
        <textarea name="maksud_ruang_lingkup" rows="2" class="form-control @error('maksud_ruang_lingkup') is-invalid @enderror">{{ old('maksud_ruang_lingkup', $spk?->maksud_ruang_lingkup ?? '') }}</textarea>
        @error('maksud_ruang_lingkup')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4 form-group">
        <label>Tanggal Berangkat</label>
        <input type="date" name="tanggal_berangkat" class="form-control @error('tanggal_berangkat') is-invalid @enderror"
            value="{{ old('tanggal_berangkat', isset($spk) && $spk?->tanggal_berangkat ? $spk?->tanggal_berangkat->format('Y-m-d') : '') }}"
            required>
        @error('tanggal_berangkat')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4 form-group">
        <label>Tanggal Kembali</label>
        <input type="date" name="tanggal_kembali" class="form-control @error('tanggal_kembali') is-invalid @enderror"
            value="{{ old('tanggal_kembali', isset($spk) && $spk?->tanggal_kembali ? $spk?->tanggal_kembali->format('Y-m-d') : '') }}"
            required>
        @error('tanggal_kembali')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4 form-group">
        <label>Lama Perjalanan (hari)</label>
        <input type="number" min="1" max="365" name="lama_perjalanan"
            class="form-control @error('lama_perjalanan') is-invalid @enderror"
            value="{{ old('lama_perjalanan', $spk?->lama_perjalanan ?? 1) }}" required>
        @error('lama_perjalanan')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4 form-group">
        <label>Sumber Biaya</label>
        <input type="text" name="sumber_biaya" class="form-control @error('sumber_biaya') is-invalid @enderror"
            value="{{ old('sumber_biaya', $spk?->sumber_biaya ?? '') }}">
        @error('sumber_biaya')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4 form-group">
        <label>Moda Transportasi</label>
        <select name="moda_transportasi" class="form-control @error('moda_transportasi') is-invalid @enderror" required>
            @foreach (['darat' => 'Darat', 'laut' => 'Laut', 'udara' => 'Udara'] as $value => $label)
                <option value="{{ $value }}"
                    {{ old('moda_transportasi', $spk?->moda_transportasi ?? 'darat') === $value ? 'selected' : '' }}>
                    {{ $label }}</option>
            @endforeach
        </select>
        @error('moda_transportasi')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4 form-group">
        <label>Sumber Biaya Opsi</label>
        <select name="sumber_biaya_opsi" class="form-control @error('sumber_biaya_opsi') is-invalid @enderror" required>
            @foreach (['perusahaan' => 'Perusahaan', 'project' => 'Project', 'lainnya' => 'Lainnya'] as $value => $label)
                <option value="{{ $value }}"
                    {{ old('sumber_biaya_opsi', $spk?->sumber_biaya_opsi ?? 'perusahaan') === $value ? 'selected' : '' }}>
                    {{ $label }}</option>
            @endforeach
        </select>
        @error('sumber_biaya_opsi')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<h5 class="mt-3">Ditugaskan Oleh</h5>
<div class="row">
    <div class="col-md-6 form-group">
        <label>Nama</label>
        <input type="text" name="ditugaskan_oleh_nama"
            class="form-control @error('ditugaskan_oleh_nama') is-invalid @enderror"
            value="{{ old('ditugaskan_oleh_nama', $spk?->ditugaskan_oleh_nama ?? 'Direktur Utama') }}" required>
        @error('ditugaskan_oleh_nama')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 form-group">
        <label>Jabatan</label>
        <input type="text" name="ditugaskan_oleh_jabatan"
            class="form-control @error('ditugaskan_oleh_jabatan') is-invalid @enderror"
            value="{{ old('ditugaskan_oleh_jabatan', $spk?->ditugaskan_oleh_jabatan ?? 'Direktur') }}" required>
        @error('ditugaskan_oleh_jabatan')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

@php $p = $prefix ?? ''; @endphp

<div class="form-row">
    <div class="form-group">
        <label class="form-label">No. Plat *</label>
        <input name="plat" id="{{ $p }}plat" class="form-control" placeholder="Contoh: WXY 1234" required value="{{ old('plat') }}">
    </div>
    <div class="form-group">
        <label class="form-label">Model Kenderaan *</label>
        <input name="model" id="{{ $p }}model" class="form-control" placeholder="Contoh: Toyota Hilux" required value="{{ old('model') }}">
    </div>
</div>
<div class="form-row">
    <div class="form-group">
        <label class="form-label">Jenis</label>
        <input name="type" id="{{ $p }}type" class="form-control" placeholder="Sedan / SUV / Pikap" value="{{ old('type') }}">
    </div>
    <div class="form-group">
        <label class="form-label">Tahun</label>
        <input name="year" id="{{ $p }}year" class="form-control" type="number" placeholder="2024" value="{{ old('year') }}">
    </div>
</div>
<div class="form-row">
    <div class="form-group">
        <label class="form-label">Warna</label>
        <input name="color" id="{{ $p }}color" class="form-control" placeholder="Putih" value="{{ old('color') }}">
    </div>
    <div class="form-group">
        <label class="form-label">Jabatan</label>
        <select name="department" id="{{ $p }}department" class="form-control">
            <option value="">Pilih jabatan...</option>
            @foreach(['Operasi','Pemasaran','IT','Kewangan','HR','Pengurusan','Pentadbiran'] as $dept)
                <option {{ old('department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-row">
    <div class="form-group">
        <label class="form-label">No. Enjin</label>
        <input name="engine_no" id="{{ $p }}engine_no" class="form-control" value="{{ old('engine_no') }}">
    </div>
    <div class="form-group">
        <label class="form-label">No. Casis</label>
        <input name="chassis_no" id="{{ $p }}chassis_no" class="form-control" value="{{ old('chassis_no') }}">
    </div>
</div>
<div class="form-row">
    <div class="form-group">
        <label class="form-label">Tarikh Luput Road Tax</label>
        <input name="roadtax_expiry" id="{{ $p }}roadtax_expiry" class="form-control" type="date" value="{{ old('roadtax_expiry') }}">
    </div>
    <div class="form-group">
        <label class="form-label">Tarikh Luput Insuran</label>
        <input name="insurance_expiry" id="{{ $p }}insurance_expiry" class="form-control" type="date" value="{{ old('insurance_expiry') }}">
    </div>
</div>
<div class="form-row">
    <div class="form-group">
        <label class="form-label">Odometer (km)</label>
        <input name="odometer_km" id="{{ $p }}odometer_km" class="form-control" type="number" placeholder="0" value="{{ old('odometer_km') }}">
    </div>
    @if($p)
    <div class="form-group">
        <label class="form-label">Status</label>
        <select name="status" id="{{ $p }}status" class="form-control">
            <option value="aktif">Aktif</option>
            <option value="servis">Dalam Servis</option>
            <option value="rosak">Rosak</option>
            <option value="tidak_aktif">Tidak Aktif</option>
        </select>
    </div>
    @endif
</div>

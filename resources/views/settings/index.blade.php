<x-fleet-layout title="Tetapan Sistem">
    <div class="page-header">
        <h2>Tetapan Sistem</h2>
        <p>Konfigurasi sistem pengurusan fleet</p>
    </div>

    <div class="grid-2">
        <div class="card mb20">
            <div class="card-header"><span class="card-title">🏢 Maklumat Syarikat</span></div>
            <div class="card-body">
                <div class="form-group"><label class="form-label">Nama Syarikat</label><input class="form-control" value="MSD Sdn Bhd"></div>
                <div class="form-group"><label class="form-label">No. Telefon</label><input class="form-control" value="03-5566 7788"></div>
                <div class="form-group"><label class="form-label">Emel</label><input class="form-control" value="fleet@msd.com.my"></div>
                <button class="btn btn-primary" onclick="showToast('Maklumat disimpan')">Simpan</button>
            </div>
        </div>

        <div class="card mb20">
            <div class="card-header"><span class="card-title">🔔 Tetapan Notifikasi</span></div>
            <div class="card-body">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;margin-bottom:10px">
                    <input type="checkbox" checked> <span style="font-size:13px">Hantar emel peringatan</span>
                </label>
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;margin-bottom:10px">
                    <input type="checkbox" checked> <span style="font-size:13px">Notifikasi dalam sistem</span>
                </label>
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;margin-bottom:10px">
                    <input type="checkbox"> <span style="font-size:13px">Notifikasi WhatsApp</span>
                </label>
                <button class="btn btn-primary" onclick="showToast('Tetapan disimpan')">Simpan</button>
            </div>
        </div>
    </div>
</x-fleet-layout>

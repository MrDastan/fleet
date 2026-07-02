<x-fleet-layout title="Tetapan Sistem">
    <div class="page-header">
        <h1>Tetapan Sistem</h1>
        <p>Konfigurasi sistem pengurusan fleet</p>
    </div>

    <form method="POST" action="{{ route('settings.store') }}">
        @csrf
        <div class="grid-2">
            <div class="card mb20">
                <div class="card-header"><span class="card-title"><span class="icon-accent"><x-icon name="file-text" :size="17" /></span>Maklumat Syarikat</span></div>
                <div class="card-body">
                    <div class="form-group"><label class="form-label">Nama Syarikat</label>
                        <input name="company_name" class="form-control" value="{{ $settings['company_name'] ?? 'MSD Sdn Bhd' }}">
                    </div>
                    <div class="form-group"><label class="form-label">No. Telefon</label>
                        <input name="company_phone" class="form-control" value="{{ $settings['company_phone'] ?? '' }}">
                    </div>
                    <div class="form-group"><label class="form-label">Emel</label>
                        <input name="company_email" class="form-control" value="{{ $settings['company_email'] ?? '' }}">
                    </div>
                    <div class="form-group"><label class="form-label">Alamat</label>
                        <input name="company_address" class="form-control" value="{{ $settings['company_address'] ?? '' }}">
                    </div>
                </div>
            </div>

            <div class="card mb20">
                <div class="card-header"><span class="card-title"><span class="icon-accent"><x-icon name="bell" :size="17" /></span>Tetapan Notifikasi</span></div>
                <div class="card-body">
                    <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:16px">
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
                            <input type="checkbox" name="notif_email" value="1" {{ ($settings['notif_email'] ?? '1') === '1' ? 'checked' : '' }}>
                            <span style="font-size:13px">Hantar emel peringatan</span>
                        </label>
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
                            <input type="checkbox" name="notif_system" value="1" {{ ($settings['notif_system'] ?? '1') === '1' ? 'checked' : '' }}>
                            <span style="font-size:13px">Notifikasi dalam sistem</span>
                        </label>
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
                            <input type="checkbox" name="notif_whatsapp" value="1" {{ ($settings['notif_whatsapp'] ?? '0') === '1' ? 'checked' : '' }}>
                            <span style="font-size:13px">Notifikasi WhatsApp</span>
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Emel Penerima Peringatan</label>
                        <input name="notif_recipients" class="form-control" placeholder="emel1@syarikat.com, emel2@syarikat.com" value="{{ $settings['notif_recipients'] ?? '' }}">
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;margin-bottom:20px">
            <button type="submit" class="btn btn-primary"><x-icon name="check" :size="16" /> Simpan Semua Tetapan</button>
        </div>
    </form>

    <!-- Reminder Config (read-only display) -->
    <div class="card">
        <div class="card-header"><span class="card-title"><span class="icon-accent"><x-icon name="settings" :size="17" /></span>Tetapan Peringatan Automatik</span></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                @foreach([
                    ['title' => 'ROAD TAX', 'items' => [['Peringatan 1','30 hari sebelum luput'],['Peringatan 2','14 hari'],['Peringatan 3','7 hari'],['Notifikasi','Admin + Fleet + Staff']]],
                    ['title' => 'INSURAN', 'items' => [['Peringatan 1','60 hari sebelum luput'],['Peringatan 2','30 hari'],['Peringatan 3','14 hari'],['Notifikasi','Admin + Fleet']]],
                    ['title' => 'SERVIS BERKALA', 'items' => [['Trigger','5,000 km sebelum jadual'],['Trigger 2','14 hari sebelum'],['Notifikasi','Fleet + Penjaga']]],
                    ['title' => 'PUSPAKOM', 'items' => [['Peringatan 1','45 hari sebelum luput'],['Peringatan 2','14 hari'],['Notifikasi','Admin + Fleet']]],
                ] as $cfg)
                <div>
                    <div style="font-size:12px;color:var(--c-muted);font-weight:600;margin-bottom:8px">{{ $cfg['title'] }}</div>
                    @foreach($cfg['items'] as $item)
                    <div class="detail-row"><div class="detail-label">{{ $item[0] }}</div><div class="detail-val">{{ $item[1] }}</div></div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-fleet-layout>

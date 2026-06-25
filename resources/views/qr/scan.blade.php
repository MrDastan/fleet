<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $vehicle->plat }} — MSD Fleet</title>
    <style>
        :root { --c-sky: #e85d00; --c-navy: #1a1a1a; --c-ok: #00b894; --c-warn: #e17055; --c-danger: #d63031; --c-muted: #636e72; --c-border: #dde3ee; --c-bg: #f4f6fb; --shadow: 0 2px 12px rgba(15,37,69,0.08); }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: var(--c-bg); min-height: 100vh; }
        .header { background: var(--c-navy); padding: 16px 20px; display: flex; align-items: center; gap: 12px; position: sticky; top: 0; z-index: 10; }
        .header .title { font-size: 15px; font-weight: 700; color: #fff; flex: 1; }
        .header .sub { font-size: 11px; color: rgba(255,255,255,.5); }
        .header .emoji { font-size: 20px; }
        .banner { margin: 16px; border-radius: 14px; padding: 16px; background: #fff; box-shadow: var(--shadow); display: flex; gap: 14px; align-items: center; }
        .banner-emoji { width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px; background: linear-gradient(135deg,#e8f0fb,#d0def8); flex-shrink: 0; }
        .banner .plat { font-size: 20px; font-weight: 700; letter-spacing: 1px; }
        .banner .model { font-size: 12px; color: var(--c-muted); margin-top: 2px; }
        .banner .badges { display: flex; gap: 8px; margin-top: 8px; flex-wrap: wrap; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-ok { background: #d4faf0; color: #007a5e; }
        .badge-warn { background: #fff0e0; color: #a05000; }
        .badge-danger { background: #ffe0e0; color: #b30000; }
        .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin: 0 16px 16px; }
        .stat { background: #fff; border-radius: 12px; padding: 12px 8px; text-align: center; box-shadow: var(--shadow); }
        .stat .val { font-size: 15px; font-weight: 700; }
        .stat .lbl { font-size: 10px; color: var(--c-muted); margin-top: 2px; }
        .section-label { font-size: 11px; font-weight: 600; color: var(--c-muted); text-transform: uppercase; letter-spacing: .5px; margin: 0 16px 10px; }
        .actions { margin: 0 16px 16px; display: flex; flex-direction: column; gap: 10px; }
        .action-item { background: #fff; border-radius: 14px; padding: 16px; display: flex; align-items: center; gap: 14px; cursor: pointer; box-shadow: var(--shadow); border: 1.5px solid transparent; transition: border-color .15s; }
        .action-item:active { border-color: var(--c-sky); }
        .action-icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
        .action-text .name { font-size: 14px; font-weight: 600; }
        .action-text .desc { font-size: 12px; color: var(--c-muted); margin-top: 2px; }
        .action-arrow { margin-left: auto; font-size: 18px; color: var(--c-muted); }
        .form-panel { display: none; margin: 0 16px 16px; background: #fff; border-radius: 14px; padding: 16px; box-shadow: var(--shadow); }
        .form-panel.show { display: block; }
        .form-panel h3 { font-size: 15px; font-weight: 700; margin-bottom: 12px; }
        .form-group { margin-bottom: 12px; }
        .form-label { font-size: 12px; font-weight: 600; color: var(--c-muted); margin-bottom: 5px; display: block; }
        .form-control { width: 100%; padding: 12px 14px; border: 1.5px solid var(--c-border); border-radius: 12px; font-size: 16px; font-family: inherit; outline: none; background: var(--c-bg); }
        .form-control:focus { border-color: var(--c-sky); background: #fff; }
        .submit-btn { width: 100%; padding: 14px; background: var(--c-sky); color: #fff; border: none; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; font-family: inherit; margin-top: 8px; }
        .submit-btn:active { background: #c94b00; transform: scale(.98); }
        .alert { border-radius: 10px; padding: 12px 14px; font-size: 13px; margin: 0 16px 12px; display: flex; gap: 10px; }
        .alert-ok { background: #d4faf0; color: #007a5e; border: 1px solid #a8dfc8; }
        .alert-danger { background: #ffe8e8; color: #8b0000; border: 1px solid #ffb3b3; }
        .user-bar { margin: 0 16px 24px; background: #fff; border-radius: 12px; padding: 12px 14px; display: flex; align-items: center; gap: 10px; box-shadow: var(--shadow); }
        .user-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--c-sky); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 12px; font-weight: 700; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="title">{{ $vehicle->plat }}</div>
            <div class="sub">MSD Fleet Management</div>
        </div>
        <div class="emoji">{{ $vehicle->emoji }}</div>
    </div>

    <!-- Vehicle Banner -->
    <div class="banner">
        <div class="banner-emoji">{{ $vehicle->emoji }}</div>
        <div>
            <div class="plat">{{ $vehicle->plat }}</div>
            <div class="model">{{ $vehicle->model }} {{ $vehicle->year }}</div>
            <div class="badges">
                @if($vehicle->status === 'aktif')<span class="badge badge-ok">Aktif</span>
                @elseif($vehicle->status === 'servis')<span class="badge badge-warn">Dalam Servis</span>
                @else<span class="badge badge-danger">{{ ucfirst($vehicle->status) }}</span>@endif
                <span class="badge" style="background:#e8f0fb;color:#1a4fa0">{{ $vehicle->department }}</span>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="stats">
        <div class="stat">
            <div class="val">{{ number_format($vehicle->odometer_km) }}</div>
            <div class="lbl">KM</div>
        </div>
        <div class="stat">
            <div class="val" style="color:{{ $vehicle->roadtax_days <= 30 ? 'var(--c-danger)' : 'var(--c-ok)' }}">{{ $vehicle->roadtax_days }}h</div>
            <div class="lbl">Road Tax</div>
        </div>
        <div class="stat">
            <div class="val" style="color:{{ $vehicle->insurance_days <= 30 ? 'var(--c-danger)' : 'var(--c-ok)' }}">{{ $vehicle->insurance_days }}h</div>
            <div class="lbl">Insuran</div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-ok">✅ {{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">🔴 @foreach($errors->all() as $e){{ $e }} @endforeach</div>
    @endif

    @auth
        <div class="section-label">Pilih tindakan</div>
        <div class="actions">
            <div class="action-item" onclick="toggleForm('checkoutForm')">
                <div class="action-icon" style="background:#e8f0fb">📋</div>
                <div class="action-text">
                    <div class="name">Log Keluar</div>
                    <div class="desc">Rekod perjalanan keluar kenderaan</div>
                </div>
                <div class="action-arrow">›</div>
            </div>
            <div class="action-item" onclick="toggleForm('fuelForm')">
                <div class="action-icon" style="background:#fff8e0">⛽</div>
                <div class="action-text">
                    <div class="name">Rekod Bahan Api</div>
                    <div class="desc">Log pengisian minyak</div>
                </div>
                <div class="action-arrow">›</div>
            </div>
            <div class="action-item" onclick="toggleForm('checkinForm')">
                <div class="action-icon" style="background:#e8fff6">🔑</div>
                <div class="action-text">
                    <div class="name">Log Masuk / Pulang</div>
                    <div class="desc">Rekod kenderaan telah dipulangkan</div>
                </div>
                <div class="action-arrow">›</div>
            </div>
        </div>

        <!-- Checkout Form -->
        <div class="form-panel" id="checkoutForm">
            <h3>📋 Log Keluar Kenderaan</h3>
            <form method="POST" action="{{ route('qr.scan.action', $vehicle->qr_code_token) }}">
                @csrf <input type="hidden" name="action" value="checkout">
                <div class="form-group"><label class="form-label">Tujuan *</label><input name="purpose" class="form-control" placeholder="Lawatan klien, urusan rasmi..." required></div>
                <div class="form-group"><label class="form-label">Destinasi</label><input name="destination" class="form-control" placeholder="Nama tempat"></div>
                <div class="form-group"><label class="form-label">Odometer (km)</label><input name="km_out" class="form-control" type="number" placeholder="{{ number_format($vehicle->odometer_km) }}"></div>
                <button type="submit" class="submit-btn">✅ Simpan Log Keluar</button>
            </form>
        </div>

        <!-- Fuel Form -->
        <div class="form-panel" id="fuelForm">
            <h3>⛽ Rekod Bahan Api</h3>
            <form method="POST" action="{{ route('qr.scan.action', $vehicle->qr_code_token) }}">
                @csrf <input type="hidden" name="action" value="fuel">
                <div class="form-group"><label class="form-label">Jenis</label>
                    <select name="fuel_type" class="form-control"><option>RON95</option><option>RON97</option><option>Diesel</option></select>
                </div>
                <div class="form-group"><label class="form-label">Liter *</label><input name="liters" class="form-control" type="number" step="0.01" required></div>
                <div class="form-group"><label class="form-label">Odometer (km) *</label><input name="odometer_km" class="form-control" type="number" required placeholder="{{ number_format($vehicle->odometer_km) }}"></div>
                <div class="form-group"><label class="form-label">Stesen</label><input name="station" class="form-control" placeholder="Petronas / Shell..."></div>
                <button type="submit" class="submit-btn">✅ Simpan Rekod Minyak</button>
            </form>
        </div>

        <!-- Checkin Form -->
        <div class="form-panel" id="checkinForm">
            <h3>🔑 Log Masuk / Pulang</h3>
            <form method="POST" action="{{ route('qr.scan.action', $vehicle->qr_code_token) }}">
                @csrf <input type="hidden" name="action" value="checkin">
                <div class="form-group"><label class="form-label">Odometer Masuk (km)</label><input name="km_in" class="form-control" type="number"></div>
                <button type="submit" class="submit-btn">✅ Simpan Log Masuk</button>
            </form>
        </div>

        <div class="user-bar">
            <div class="user-avatar">{{ auth()->user()->avatar_initials }}</div>
            <div>
                <div style="font-size:12px;font-weight:600">{{ auth()->user()->name }}</div>
                <div style="font-size:11px;color:var(--c-muted)">{{ auth()->user()->position }}</div>
            </div>
        </div>
    @else
        <div class="alert alert-danger" style="margin-top:12px">🔒 Sila <a href="{{ route('login') }}" style="color:#8b0000;font-weight:600">log masuk</a> untuk mengakses tindakan kenderaan.</div>
    @endauth

    <script>
    function toggleForm(id) {
        document.querySelectorAll('.form-panel').forEach(f => {
            f.classList.toggle('show', f.id === id && !f.classList.contains('show'));
        });
    }
    </script>
</body>
</html>

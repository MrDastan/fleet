<x-fleet-layout title="QR Kenderaan">
    <div class="page-header">
        <h2>Pengurusan QR Kenderaan</h2>
        <p>Jana, cetak dan urus QR code untuk setiap kenderaan — staff scan untuk akses pantas</p>
    </div>

    <!-- How it works -->
    <div class="card mb20">
        <div class="card-header"><span class="card-title">📱 Cara Penggunaan QR</span></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;text-align:center">
                @foreach([
                    ['icon' => '📋', 'bg' => '#fff0e8', 'title' => 'QR Dilekat di Kenderaan', 'sub' => 'Satu QR unik per kenderaan, dicetak dan dilekat'],
                    ['icon' => '📱', 'bg' => '#e8f0fb', 'title' => 'Staff / Penjaga Scan', 'sub' => 'Guna kamera telefon — akses terus'],
                    ['icon' => '📝', 'bg' => '#e8fff6', 'title' => 'Form Automatik', 'sub' => 'Log keluar, minyak, atau check-in'],
                    ['icon' => '✅', 'bg' => '#f0eefd', 'title' => 'Data Terus Tersimpan', 'sub' => 'Rekod masuk ke sistem secara automatik'],
                ] as $step)
                <div>
                    <div style="width:48px;height:48px;border-radius:50%;background:{{ $step['bg'] }};display:flex;align-items:center;justify-content:center;font-size:22px;margin:0 auto 8px">{{ $step['icon'] }}</div>
                    <div style="font-size:12px;font-weight:600;margin-bottom:4px">{{ $step['title'] }}</div>
                    <div style="font-size:11px;color:var(--c-muted)">{{ $step['sub'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- QR Grid -->
    <div style="font-size:13px;font-weight:600;margin-bottom:14px">QR Kenderaan — Klik untuk pratonton & cetak</div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;margin-bottom:20px">
        @foreach($vehicles as $v)
        <div class="card" style="cursor:pointer" onclick="showQR({{ $v->id }}, '{{ $v->plat }}', '{{ $v->model }}', '{{ $v->emoji }}', '{{ $v->qr_code_token }}')">
            <div class="card-body" style="display:flex;align-items:center;gap:14px;padding:14px 16px">
                <div style="width:50px;height:50px;border-radius:10px;background:linear-gradient(135deg,#e8f0fb,#d0def8);display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0">{{ $v->emoji }}</div>
                <div style="flex:1">
                    <div style="font-size:16px;font-weight:700;letter-spacing:1px">{{ $v->plat }}</div>
                    <div style="font-size:12px;color:var(--c-muted)">{{ $v->model }} {{ $v->year }}</div>
                    <div style="font-size:11px;color:var(--c-muted);margin-top:2px">📁 {{ $v->department }}</div>
                </div>
                <div style="text-align:center">
                    <div style="font-size:28px">📲</div>
                    <div style="font-size:10px;color:var(--c-muted)">QR Code</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- QR Preview Modal -->
    <div class="modal-overlay" id="qrModal">
        <div class="modal" style="max-width:420px;text-align:center">
            <div class="modal-header">
                <div class="modal-title" id="qrModalTitle">QR Kenderaan</div>
                <div class="modal-close" onclick="closeModal('qrModal')">✕</div>
            </div>
            <div id="qrModalBody" style="padding:20px 0">
                <div style="font-size:48px;margin-bottom:12px" id="qrEmoji">🚗</div>
                <div id="qrDisplay" style="display:inline-block;padding:16px;background:#fff;border:2px solid var(--c-border);border-radius:12px"></div>
                <div style="margin-top:12px">
                    <div style="font-size:20px;font-weight:800;letter-spacing:2px" id="qrPlat"></div>
                    <div style="font-size:13px;color:var(--c-muted)" id="qrModel"></div>
                </div>
                <div style="margin-top:12px;font-size:11px;color:var(--c-muted)">
                    Scan QR ini menggunakan kamera telefon untuk akses pantas
                </div>
                <div style="margin-top:8px">
                    <input type="text" id="qrUrl" class="form-control" readonly style="text-align:center;font-size:11px;color:var(--c-muted)">
                </div>
            </div>
            <div class="modal-footer" style="justify-content:center;gap:10px">
                <button class="btn btn-secondary" onclick="closeModal('qrModal')">Tutup</button>
                <button class="btn btn-primary" onclick="printQR()">🖨️ Cetak QR</button>
                <button class="btn btn-secondary" id="btnSimulate">📱 Buka Scan Page</button>
            </div>
        </div>
    </div>

    <script>
    function showQR(id, plat, model, emoji, token) {
        document.getElementById('qrModalTitle').textContent = 'QR — ' + plat;
        document.getElementById('qrPlat').textContent = plat;
        document.getElementById('qrModel').textContent = model;
        document.getElementById('qrEmoji').textContent = emoji;

        const url = '/qr/scan/' + token;
        const fullUrl = window.location.origin + url;
        document.getElementById('qrUrl').value = fullUrl;
        document.getElementById('btnSimulate').onclick = () => window.open(url, '_blank');

        fetch('/qr/generate/' + id)
            .then(r => r.json())
            .then(data => { document.getElementById('qrDisplay').innerHTML = data.qr; })
            .catch(() => {
                document.getElementById('qrDisplay').innerHTML = '<div style="width:250px;height:250px;background:#f4f6fb;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:14px;color:var(--c-muted)">QR Code<br>' + plat + '</div>';
            });

        document.getElementById('qrModal').classList.add('open');
    }

    function printQR() {
        const content = document.getElementById('qrModalBody').innerHTML;
        const w = window.open('', '_blank');
        w.document.write('<html><head><title>QR Print</title><style>body{font-family:Segoe UI,sans-serif;text-align:center;padding:40px}svg{max-width:300px}</style></head><body>' + content + '</body></html>');
        w.document.close();
        w.print();
    }

    function closeModal(id) { document.getElementById(id).classList.remove('open'); }
    document.querySelectorAll('.modal-overlay').forEach(o => { o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); }); });
    </script>
</x-fleet-layout>

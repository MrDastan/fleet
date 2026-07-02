# Handoff: MSD Fleet — Redesign UI

## Overview
Redesign penuh untuk **MSD Fleet Management System** (fleet.msd.net.my) — sistem pengurusan kenderaan syarikat dalam Bahasa Melayu. Design ini menggantikan tema sedia ada (navy/oren, ikon emoji) dengan sistem visual baharu: kanvas warm-neutral, sidebar hampir-hitam, oren yang lebih halus, tipografi Space Grotesk + IBM Plex, dan set ikon garisan (Lucide) menggantikan semua emoji.

## About the Design Files
Fail dalam bundle ini adalah **rujukan design yang dibina dalam HTML** — prototaip yang menunjukkan rupa dan tingkah laku yang dimaksudkan, BUKAN kod production untuk disalin terus. Tugas anda: **recreate design ini dalam codebase sedia ada** — Laravel 12 + Blade + Tailwind CSS (repo FLEET), menggunakan corak yang sudah ada:

- Layout utama: `resources/views/components/fleet-layout.blade.php`
- Stylesheet: `public/css/fleet.css` (CSS variables dalam `:root`)
- Konfigurasi nav (per-role): `config/fleet.php`
- Views per modul: `resources/views/{dashboard,vehicles,saman,anomalies,...}/index.blade.php`

Kekalkan struktur route, controller, dan data sedia ada — ini adalah perubahan lapisan persembahan (Blade + CSS) sahaja.

## Fidelity
**High-fidelity (hifi).** Recreate UI secara pixel-perfect: semua warna hex, saiz fon, spacing, radius dan shadow dinyatakan di bawah adalah muktamad. Rujuk `MSD Fleet.dc.html` (buka dalam browser) untuk rupa sebenar setiap skrin.

## Design Tokens

### Warna
| Token | Nilai | Kegunaan |
|---|---|---|
| `--bg` | `#F6F4EF` | Latar halaman (warm off-white) |
| `--surface` | `#FFFFFF` | Kad & panel |
| `--surface-2` | `#FAF8F4` | Header table, latar sekunder |
| `--surface-3` | `#F6F4EF` | Stat tiles dalam kad |
| `--border` | `#E7E2D9` | Semua border kad/input |
| `--border-soft` | `#F2EEE6` | Divider dalam senarai |
| `--ink` | `#1B1712` | Teks utama |
| `--ink-2` | `#6B6459` | Teks sekunder |
| `--muted` | `#938B7D` | Teks tertier / placeholder |
| `--accent` | `#E8580C` | Oren utama (butang primary, active nav) |
| `--accent-dark` | `#C0480A` | Oren gelap (teks atas latar oren muda) |
| `--accent-light` | `#F6863E` | Oren terang (atas latar gelap) |
| `--sidebar` | `#16130E` | Latar sidebar (hampir hitam, warm) |
| `--danger` | `#C93A32` / teks `#C0362E` | Merah |
| `--warn` | `#C0851A` / teks `#986410` | Kuning keemasan |
| `--ok` | `#137049` | Hijau |
| `--info` | `#2C5AC0` | Biru |

Pasangan latar/teks untuk pill & ikon lembut (bg / fg):
- ok: `#E3F2EA` / `#137049` · warn: `#FBEFD6` / `#986410` · danger: `#FBE6E4` / `#C0362E`
- info: `#E7EDFB` / `#2C5AC0` · neutral: `#EEEBE4` / `#6B6459` · accent: `#FCEBE0` / `#C0480A`

### Tipografi
Google Fonts (tambah dalam `<head>` fleet-layout):
```
https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap
```
- **Space Grotesk** — heading & angka besar (KPI). H1 27px/600 letter-spacing -0.6px; tajuk kad 15px/600; KPI 29px/600.
- **IBM Plex Sans** — body. 13–13.5px kandungan, 12.5px label, 11–11.5px meta.
- **IBM Plex Mono** — nombor plat, no. saman, tarikh pendek, nilai kecil berangka.

### Spacing / Radius / Shadow
- Radius: kad `16px`, panel kecil `14px`, butang & input `10px`, tile dalam kad `9px`, pill `999px`
- Shadow kad: `0 1px 2px rgba(20,15,8,.04), 0 14px 30px -22px rgba(20,15,8,.28)`
- Shadow butang primary: `0 6px 16px -6px rgba(232,88,12,.6)`
- Padding kad: `17–18px`; gap grid: `16–18px`; padding main: `26px 28px 40px`

## Ikon
Ganti SEMUA emoji dengan **Lucide icons** (stroke 1.8, saiz 17–20px, `stroke-linecap/linejoin: round`). Cadangan: pakej `mallardduck/blade-lucide-icons` (composer) atau salin SVG. Pemetaan nav (`config/fleet.php` — tukar field emoji kepada nama ikon):

- Dashboard → `gauge` · Peringatan → `bell` · Senarai Kenderaan → `car`
- Permohonan → `clipboard-check` · Servis → `wrench` · Road Tax & Insuran → `file-text`
- Bahan Api → `fuel` · Saman → `triangle-alert` · Log Pergerakan → `route`
- Laporan → `bar-chart-3` · Anomali AI → `sparkles` · QR Kenderaan → `qr-code`
- Pengguna → `users` · Tetapan → `settings` · Log Keluar → `log-out`
- Lain: `search`, `plus`, `chevron-right`, `arrow-up-right`, `map-pin`, `clock`, `shield`, `calendar`, `receipt`, `activity`, `eye`, `truck`, `trending-up`

## Screens / Views

### 1. Shell (fleet-layout.blade.php)
**Sidebar** — kekal, 252px, sticky, `#16130E`, scroll sendiri:
- Logo: kotak 34px radius 9px oren `#E8580C` dengan huruf "M" putih (Space Grotesk 700), di sebelah "MSD.Fleet" (titik oren) + subtitle "Pengurusan Kenderaan" 10px `rgba(255,255,255,.38)`
- Kad user: `rgba(255,255,255,.05)` radius 11px — avatar bulat 32px gradient oren dengan inisial, nama 12.5px/600 putih, peranan 10.5px `rgba(255,255,255,.4)`
- Label seksyen (UTAMA / PENGURUSAN / ANALISIS / SISTEM): 9.5px/600, letter-spacing 1.4px, uppercase, `rgba(255,255,255,.28)`, padding `16px 10px 5px`
- Item nav: padding `9px 11px`, radius 10px, gap 11px; ikon 18px + label 13px/500
  - Default: `rgba(255,255,255,.56)`
  - Aktif: teks `#fff`, bg `rgba(232,88,12,.16)`, bar kiri `inset 2px 0 0 #E8580C`
  - **PENTING: jangan letak `transition` pada background item nav** (menyebabkan isu repaint)
- Badge kiraan: IBM Plex Mono 10px/600, radius 9px, bg `#D8443C` (danger) atau `rgba(224,162,59,.9)` (warn), teks putih
- Bawah: divider `rgba(255,255,255,.07)` + "Log Keluar"

**Topbar** — 62px sticky, `rgba(246,244,239,.85)` + `backdrop-filter: blur(12px)`, border bawah `#E7E2D9`:
- Search: max 340px, bg putih, border `#E7E2D9`, radius 10px, ikon `search`, placeholder "Cari plat, pemandu, saman..."
- Kanan: 2 butang ikon 38px (bell dengan dot merah `#C93A32` bergaris putih, calendar)

### 2. Dashboard
- Header: tarikh penuh BM 12.5px `#938B7D` + salam "Selamat pagi, {nama}" (H1). Kanan: butang "Laporan" (secondary putih) + "Kenderaan" (primary oren, ikon plus)
- **4 kad KPI** (grid auto-fit minmax 210px): ikon lembut 38px kiri-atas, sparkline SVG 64×26 kanan-atas, nilai Space Grotesk 29px, label 12.5px `#6B6459`, sub-status dot + teks berwarna. KPI: Jumlah Kenderaan (42), Perlu Perhatian (7), Dalam Servis (3), Saman Belum Bayar (RM 3,240 — klik pergi ke Saman)
- **Banner urgent**: gradient `#FBE6E4→#FCEFE7`, border `#F3C9C4`, radius 14px; ikon segitiga dalam kotak merah 36px; teks 13px `#7A2A24` dengan plat di-bold; link "Lihat ›" merah
- **Peringatan Segera** (kolum kiri, 2/3): senarai baris — ikon lembut 36px, tajuk 13.5px/600 ("Road Tax — WPK 7734"), sub 11.5px muted, dan **ring countdown SVG 44px** kanan: bulatan progress (r=17, stroke 3.5, warna ikut keterukan: ≤7 hari merah, ≤30 warn, >30 hijau) dengan angka hari di tengah + "hari" 7.5px
- **Rekod Bahan Api**: 3 stat besar (RM jumlah, liter, L/100km) + bar chart 6 bulan — bar radius `7px 7px 3px 3px`, bulan semasa gradient oren `#F6863E→#E8580C`, lain `#ECE7DE`, nilai atas bar IBM Plex Mono 10px
- **Kad Anomali AI** (kolum kanan, gelap): bg gradient `#1F1B14→#16130E`, badge "Live" hijau dengan dot pulse, angka besar 32px, kad dalaman `rgba(255,255,255,.06)` menunjukkan anomali teratas dengan progress bar keyakinan 92%, link "Siasat semua anomali ↗" oren terang. Klik → skrin Anomali
- **Status Hari Ini**: senarai plat (IBM Plex Mono 13px/600) + sub + pill status (Dalam Servis/warn, Dalam Perjalanan/ok, Di Pejabat/neutral)

### 3. Senarai Kenderaan
- Header + kiraan "42 kenderaan syarikat · N dipaparkan"
- Filter chips: Semua / Aktif / Dalam Servis / Rosak — aktif: bg `#16130E` teks putih; lain: putih border `#E7E2D9`. Kanan: butang primary "Tambah Kenderaan"
- **Grid kad kenderaan** (auto-fill minmax 260px): tile atas 96px berwarna (kereta: bg `#FCEBE0` ikon `#C0480A`; trak: bg `#EDEAE3` ikon `#5A544B`) dengan ikon kenderaan 42px stroke 1.5 + pill status penjuru; bawah: plat (Mono 16px) + km, model · jabatan 12.5px, dan **3 stat tile** (Road Tax / Insuran / Servis) — nilai "Nh" (hari) Space Grotesk 14px berwarna ikut ambang (≤7 merah, ≤30 warn, >30 hijau), label 9.5px uppercase

### 4. Pengurusan Saman
- 3 kad stat: Belum Bayar (RM merah), Jumlah Saman, Dirayu (warn)
- Table dalam kad: header uppercase 10.5px `#938B7D` bg `#FAF8F4`; kolum: No. Saman/Plat (Mono, no. kecil muted atas plat), Kesalahan, Lokasi, Tarikh (Mono), Amaun (Space Grotesk 14px/600), Status (pill: Belum Bayar/danger, Dirayu/warn, Dibayar/ok)

### 5. Pengesanan Anomali AI
- Header dengan ikon `sparkles` dalam kotak gelap 40px
- 4 kad stat kecil (Anomali aktif, Disiasat, Purata keyakinan, Kenderaan dipantau)
- **Senarai kad anomali**: border-left 3px warna keterukan (tinggi `#C93A32`, sederhana `#C0851A`); ikon lembut 42px; tajuk + pill keterukan; keterangan 13px; chip plat (Mono, bg `#F6F4EF`) + masa; kanan: label "Keyakinan AI", progress bar 6px + peratus Mono berwarna, butang "Siasat" (gelap) + "Abai" (secondary)

### 6. Modul lain (Peringatan, Permohonan, Servis, Road Tax, Bahan Api, Log Pergerakan, Laporan, QR, Pengguna, Tetapan)
Belum direka penuh — guna sistem yang sama: header halaman (H1 Space Grotesk 24px + subtitle muted), kad putih radius 16px, pill status, ikon Lucide. Ikut corak table Saman untuk senarai, corak kad Kenderaan untuk grid.

## Interactions & Behavior
- Nav sidebar: SPA-feel tidak perlu — navigasi Laravel biasa; active state dari route semasa (corak `request()->routeIs()` sedia ada)
- Hover kad kenderaan/KPI klikabel: `cursor:pointer` (boleh tambah lift halus `translateY(-1px)` + shadow, transition pada `transform/box-shadow` SAHAJA)
- Filter kenderaan: filter client-side atau query param
- Ring countdown & sparkline: SVG inline dalam Blade — rujuk markup dalam `MSD Fleet.dc.html` (fungsi `ring()` dan `spark()` dalam logic class boleh diterjemah ke Blade component/partial)
- Butang "Siasat"/"Abai" anomali: wire ke controller anomalies sedia ada

## State Management
Tiada state client-side baharu diperlukan — semua data dari controller Laravel sedia ada. Ambang warna countdown (hari): `<=7` danger, `<=30` warn, `>30` ok — jadikan helper PHP.

## Assets
- Google Fonts sahaja (link di atas). Tiada imej.
- Ikon: Lucide (MIT) — via pakej Blade atau SVG inline.

## Files
- `MSD Fleet.dc.html` — prototaip interaktif penuh (buka dalam browser; klik sidebar untuk tukar skrin: Dashboard, Senarai Kenderaan, Saman, Anomali AI)
- `support.js` — runtime prototaip (abaikan; bukan sebahagian design)

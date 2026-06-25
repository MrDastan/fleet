<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Masuk — MSD Fleet</title>
    <style>
        :root { --c-navy: #1a1a1a; --c-sky: #e85d00; --c-border: #dde3ee; --c-bg: #f4f6fb; --c-muted: #636e72; --c-text: #1e2a3a; --c-light: #fff0e8; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: var(--c-navy); display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; padding: 24px; }
        .login-card { background: #fff; border-radius: 20px; padding: 32px 28px; width: 100%; max-width: 380px; box-shadow: 0 24px 64px rgba(0,0,0,0.3); }
        .login-logo { text-align: center; margin-bottom: 24px; }
        .login-logo .brand { font-size: 22px; font-weight: 800; letter-spacing: -1px; color: var(--c-navy); }
        .login-logo .brand span { color: var(--c-sky); }
        .system-name { font-size: 13px; color: var(--c-muted); margin-top: 6px; }
        .login-title { font-size: 20px; font-weight: 700; color: var(--c-navy); margin-bottom: 4px; text-align: center; }
        .login-sub { font-size: 12px; color: var(--c-muted); text-align: center; margin-bottom: 22px; }
        .login-input { width: 100%; padding: 11px 14px; border: 1.5px solid var(--c-border); border-radius: 10px; font-size: 14px; font-family: inherit; outline: none; transition: border-color .15s, box-shadow .15s; margin-bottom: 12px; background: var(--c-bg); }
        .login-input:focus { border-color: var(--c-sky); box-shadow: 0 0 0 3px rgba(232,93,0,.12); background: #fff; }
        .login-btn { width: 100%; padding: 13px; background: var(--c-sky); color: #fff; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; font-family: inherit; transition: background .15s; margin-top: 4px; }
        .login-btn:hover { background: #c94b00; }
        .login-divider { display: flex; align-items: center; gap: 10px; margin: 16px 0; color: var(--c-muted); font-size: 12px; }
        .login-divider::before, .login-divider::after { content: ''; flex: 1; height: 1px; background: var(--c-border); }
        .login-roles { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .role-quick-btn { padding: 10px 8px; border: 1.5px solid var(--c-border); border-radius: 10px; background: #fff; cursor: pointer; text-align: center; font-family: inherit; transition: all .15s; }
        .role-quick-btn:hover { border-color: var(--c-sky); background: var(--c-light); }
        .role-icon { font-size: 20px; display: block; margin-bottom: 4px; }
        .role-name { font-size: 11px; font-weight: 600; color: var(--c-text); }
        .login-error { background: #ffe8e8; border: 1px solid #ffb3b3; color: #8b0000; border-radius: 8px; padding: 10px 12px; font-size: 12px; margin-bottom: 12px; }
        .footer-text { color: rgba(255,255,255,.3); font-size: 11px; margin-top: 20px; text-align: center; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <div class="brand">MSD<span>.</span></div>
            <div class="system-name">Sistem Pengurusan Kenderaan</div>
        </div>

        <div class="login-title">Log Masuk</div>
        <div class="login-sub">Masukkan kelayakan anda untuk meneruskan</div>

        @if($errors->any())
            <div class="login-error">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="email" name="email" class="login-input" placeholder="Alamat E-mel" value="{{ old('email') }}" autocomplete="username" required autofocus>
            <input type="password" name="password" class="login-input" placeholder="Kata Laluan" autocomplete="current-password" required>
            <button type="submit" class="login-btn">Log Masuk</button>
        </form>

        <div class="login-divider">atau log masuk pantas sebagai</div>

        <div class="login-roles">
            <form method="POST" action="{{ route('login') }}" style="display:contents">
                @csrf
                <input type="hidden" name="email" value="admin@msd.com.my">
                <input type="hidden" name="password" value="password">
                <button type="submit" class="role-quick-btn">
                    <span class="role-icon">👤</span>
                    <span class="role-name">Admin</span>
                </button>
            </form>
            <form method="POST" action="{{ route('login') }}" style="display:contents">
                @csrf
                <input type="hidden" name="email" value="fleet@msd.com.my">
                <input type="hidden" name="password" value="password">
                <button type="submit" class="role-quick-btn">
                    <span class="role-icon">🚗</span>
                    <span class="role-name">Fleet</span>
                </button>
            </form>
            <form method="POST" action="{{ route('login') }}" style="display:contents">
                @csrf
                <input type="hidden" name="email" value="staff@msd.com.my">
                <input type="hidden" name="password" value="password">
                <button type="submit" class="role-quick-btn">
                    <span class="role-icon">👷</span>
                    <span class="role-name">Staff</span>
                </button>
            </form>
            <form method="POST" action="{{ route('login') }}" style="display:contents">
                @csrf
                <input type="hidden" name="email" value="guard@msd.com.my">
                <input type="hidden" name="password" value="password">
                <button type="submit" class="role-quick-btn">
                    <span class="role-icon">🔑</span>
                    <span class="role-name">Penjaga</span>
                </button>
            </form>
        </div>
    </div>
    <div class="footer-text">MSD Sdn Bhd &bull; FMS v1.0 &bull; {{ now()->format('M Y') }}</div>
</body>
</html>

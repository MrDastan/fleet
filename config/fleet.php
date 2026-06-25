<?php

return [
    'nav' => [
        'admin' => [
            ['section' => 'UTAMA', 'items' => [
                ['route' => 'dashboard', 'icon' => '🏠', 'label' => 'Dashboard'],
                ['route' => 'reminders.index', 'icon' => '🔔', 'label' => 'Peringatan', 'badge' => '5', 'badgeType' => 'danger'],
            ]],
            ['section' => 'PENGURUSAN', 'items' => [
                ['route' => 'vehicles.index', 'icon' => '🚗', 'label' => 'Senarai Kenderaan'],
                ['route' => 'approvals.index', 'icon' => '📋', 'label' => 'Permohonan', 'badge' => '4', 'badgeType' => 'danger'],
                ['route' => 'services.index', 'icon' => '🔧', 'label' => 'Servis & Penyelenggaraan'],
                ['route' => 'roadtax.index', 'icon' => '📄', 'label' => 'Road Tax & Insuran', 'badge' => '3', 'badgeType' => 'warn'],
                ['route' => 'fuel.index', 'icon' => '⛽', 'label' => 'Bahan Api'],
                ['route' => 'saman.index', 'icon' => '🚨', 'label' => 'Pengurusan Saman', 'badge' => '4', 'badgeType' => 'danger'],
                ['route' => 'movements.index', 'icon' => '📋', 'label' => 'Log Pergerakan'],
            ]],
            ['section' => 'ADMIN', 'items' => [
                ['route' => 'reports.index', 'icon' => '📊', 'label' => 'Laporan'],
                ['route' => 'anomalies.index', 'icon' => '🧠', 'label' => 'Pengesanan Anomali', 'badge' => '3', 'badgeType' => 'danger'],
                ['route' => 'qr.index', 'icon' => '📲', 'label' => 'QR Kenderaan'],
                ['route' => 'users.index', 'icon' => '👥', 'label' => 'Pengguna'],
                ['route' => 'settings.index', 'icon' => '⚙️', 'label' => 'Tetapan'],
            ]],
        ],
        'fleet' => [
            ['section' => 'UTAMA', 'items' => [
                ['route' => 'dashboard', 'icon' => '🏠', 'label' => 'Dashboard'],
                ['route' => 'reminders.index', 'icon' => '🔔', 'label' => 'Peringatan', 'badge' => '5', 'badgeType' => 'danger'],
            ]],
            ['section' => 'PENGURUSAN', 'items' => [
                ['route' => 'vehicles.index', 'icon' => '🚗', 'label' => 'Senarai Kenderaan'],
                ['route' => 'approvals.index', 'icon' => '📋', 'label' => 'Permohonan', 'badge' => '2', 'badgeType' => 'warn'],
                ['route' => 'services.index', 'icon' => '🔧', 'label' => 'Servis & Penyelenggaraan'],
                ['route' => 'roadtax.index', 'icon' => '📄', 'label' => 'Road Tax & Insuran', 'badge' => '3', 'badgeType' => 'warn'],
                ['route' => 'fuel.index', 'icon' => '⛽', 'label' => 'Bahan Api'],
                ['route' => 'saman.index', 'icon' => '🚨', 'label' => 'Pengurusan Saman', 'badge' => '4', 'badgeType' => 'danger'],
                ['route' => 'movements.index', 'icon' => '📋', 'label' => 'Log Pergerakan'],
                ['route' => 'reports.index', 'icon' => '📊', 'label' => 'Laporan'],
                ['route' => 'anomalies.index', 'icon' => '🧠', 'label' => 'Pengesanan Anomali', 'badge' => '3', 'badgeType' => 'danger'],
            ]],
        ],
        'staff' => [
            ['section' => 'UTAMA', 'items' => [
                ['route' => 'dashboard', 'icon' => '🏠', 'label' => 'Dashboard'],
                ['route' => 'reminders.index', 'icon' => '🔔', 'label' => 'Peringatan'],
            ]],
            ['section' => 'KENDERAAN', 'items' => [
                ['route' => 'approvals.index', 'icon' => '📋', 'label' => 'Mohon Kenderaan'],
                ['route' => 'vehicles.index', 'icon' => '🚗', 'label' => 'Kenderaan Saya'],
                ['route' => 'fuel.index', 'icon' => '⛽', 'label' => 'Log Bahan Api'],
                ['route' => 'movements.index', 'icon' => '📋', 'label' => 'Log Perjalanan'],
                ['route' => 'services.index', 'icon' => '🔧', 'label' => 'Permintaan Servis'],
                ['route' => 'saman.index', 'icon' => '🚨', 'label' => 'Saman Saya'],
            ]],
        ],
        'guard' => [
            ['section' => 'UTAMA', 'items' => [
                ['route' => 'dashboard', 'icon' => '🏠', 'label' => 'Dashboard'],
            ]],
            ['section' => 'TUGAS', 'items' => [
                ['route' => 'approvals.index', 'icon' => '📋', 'label' => 'Permohonan Masuk', 'badge' => '2', 'badgeType' => 'danger'],
                ['route' => 'movements.index', 'icon' => '📍', 'label' => 'Log Keluar/Masuk'],
                ['route' => 'vehicles.index', 'icon' => '🚗', 'label' => 'Senarai Kenderaan'],
                ['route' => 'saman.index', 'icon' => '🚨', 'label' => 'Lapor Saman'],
            ]],
        ],
    ],

    'bottom_nav' => [
        'admin' => [
            ['route' => 'dashboard', 'icon' => '🏠', 'label' => 'Home'],
            ['route' => 'approvals.index', 'icon' => '📋', 'label' => 'Mohon', 'badge' => true],
            ['route' => 'vehicles.index', 'icon' => '🚗', 'label' => 'Kenderaan'],
            ['route' => 'saman.index', 'icon' => '🚨', 'label' => 'Saman', 'badge' => true],
            ['route' => 'reminders.index', 'icon' => '🔔', 'label' => 'Alert', 'badge' => true],
        ],
        'fleet' => [
            ['route' => 'dashboard', 'icon' => '🏠', 'label' => 'Home'],
            ['route' => 'approvals.index', 'icon' => '📋', 'label' => 'Permohonan', 'badge' => true],
            ['route' => 'vehicles.index', 'icon' => '🚗', 'label' => 'Kenderaan'],
            ['route' => 'services.index', 'icon' => '🔧', 'label' => 'Servis'],
            ['route' => 'saman.index', 'icon' => '🚨', 'label' => 'Saman'],
        ],
        'staff' => [
            ['route' => 'dashboard', 'icon' => '🏠', 'label' => 'Home'],
            ['route' => 'approvals.index', 'icon' => '📋', 'label' => 'Mohon'],
            ['route' => 'vehicles.index', 'icon' => '🚗', 'label' => 'Kenderaan'],
            ['route' => 'fuel.index', 'icon' => '⛽', 'label' => 'Bahan Api'],
            ['route' => 'movements.index', 'icon' => '📋', 'label' => 'Log'],
        ],
        'guard' => [
            ['route' => 'dashboard', 'icon' => '🏠', 'label' => 'Home'],
            ['route' => 'approvals.index', 'icon' => '📋', 'label' => 'Permohonan', 'badge' => true],
            ['route' => 'movements.index', 'icon' => '📍', 'label' => 'Log Keluar'],
            ['route' => 'vehicles.index', 'icon' => '🚗', 'label' => 'Kenderaan'],
        ],
    ],
];

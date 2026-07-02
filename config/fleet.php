<?php

return [
    'nav' => [
        'admin' => [
            ['section' => 'UTAMA', 'items' => [
                ['route' => 'dashboard', 'icon' => 'gauge', 'label' => 'Dashboard'],
                ['route' => 'reminders.index', 'icon' => 'bell', 'label' => 'Peringatan', 'badge' => '5', 'badgeType' => 'danger'],
            ]],
            ['section' => 'PENGURUSAN', 'items' => [
                ['route' => 'vehicles.index', 'icon' => 'car', 'label' => 'Senarai Kenderaan'],
                ['route' => 'approvals.index', 'icon' => 'clipboard-check', 'label' => 'Permohonan', 'badge' => '4', 'badgeType' => 'danger'],
                ['route' => 'services.index', 'icon' => 'wrench', 'label' => 'Servis & Penyelenggaraan'],
                ['route' => 'roadtax.index', 'icon' => 'file-text', 'label' => 'Road Tax & Insuran', 'badge' => '3', 'badgeType' => 'warn'],
                ['route' => 'fuel.index', 'icon' => 'fuel', 'label' => 'Bahan Api'],
                ['route' => 'saman.index', 'icon' => 'triangle-alert', 'label' => 'Pengurusan Saman', 'badge' => '4', 'badgeType' => 'danger'],
                ['route' => 'movements.index', 'icon' => 'route', 'label' => 'Log Pergerakan'],
            ]],
            ['section' => 'ADMIN', 'items' => [
                ['route' => 'reports.index', 'icon' => 'bar-chart-3', 'label' => 'Laporan'],
                ['route' => 'anomalies.index', 'icon' => 'sparkles', 'label' => 'Pengesanan Anomali', 'badge' => '3', 'badgeType' => 'danger'],
                ['route' => 'qr.index', 'icon' => 'qr-code', 'label' => 'QR Kenderaan'],
                ['route' => 'users.index', 'icon' => 'users', 'label' => 'Pengguna'],
                ['route' => 'settings.index', 'icon' => 'settings', 'label' => 'Tetapan'],
            ]],
        ],
        'fleet' => [
            ['section' => 'UTAMA', 'items' => [
                ['route' => 'dashboard', 'icon' => 'gauge', 'label' => 'Dashboard'],
                ['route' => 'reminders.index', 'icon' => 'bell', 'label' => 'Peringatan', 'badge' => '5', 'badgeType' => 'danger'],
            ]],
            ['section' => 'PENGURUSAN', 'items' => [
                ['route' => 'vehicles.index', 'icon' => 'car', 'label' => 'Senarai Kenderaan'],
                ['route' => 'approvals.index', 'icon' => 'clipboard-check', 'label' => 'Permohonan', 'badge' => '2', 'badgeType' => 'warn'],
                ['route' => 'services.index', 'icon' => 'wrench', 'label' => 'Servis & Penyelenggaraan'],
                ['route' => 'roadtax.index', 'icon' => 'file-text', 'label' => 'Road Tax & Insuran', 'badge' => '3', 'badgeType' => 'warn'],
                ['route' => 'fuel.index', 'icon' => 'fuel', 'label' => 'Bahan Api'],
                ['route' => 'saman.index', 'icon' => 'triangle-alert', 'label' => 'Pengurusan Saman', 'badge' => '4', 'badgeType' => 'danger'],
                ['route' => 'movements.index', 'icon' => 'route', 'label' => 'Log Pergerakan'],
                ['route' => 'reports.index', 'icon' => 'bar-chart-3', 'label' => 'Laporan'],
                ['route' => 'anomalies.index', 'icon' => 'sparkles', 'label' => 'Pengesanan Anomali', 'badge' => '3', 'badgeType' => 'danger'],
            ]],
        ],
        'staff' => [
            ['section' => 'UTAMA', 'items' => [
                ['route' => 'dashboard', 'icon' => 'gauge', 'label' => 'Dashboard'],
                ['route' => 'reminders.index', 'icon' => 'bell', 'label' => 'Peringatan'],
            ]],
            ['section' => 'KENDERAAN', 'items' => [
                ['route' => 'approvals.index', 'icon' => 'clipboard-check', 'label' => 'Mohon Kenderaan'],
                ['route' => 'vehicles.index', 'icon' => 'car', 'label' => 'Kenderaan Saya'],
                ['route' => 'fuel.index', 'icon' => 'fuel', 'label' => 'Log Bahan Api'],
                ['route' => 'movements.index', 'icon' => 'route', 'label' => 'Log Perjalanan'],
                ['route' => 'services.index', 'icon' => 'wrench', 'label' => 'Permintaan Servis'],
                ['route' => 'saman.index', 'icon' => 'triangle-alert', 'label' => 'Saman Saya'],
            ]],
        ],
        'guard' => [
            ['section' => 'UTAMA', 'items' => [
                ['route' => 'dashboard', 'icon' => 'gauge', 'label' => 'Dashboard'],
            ]],
            ['section' => 'TUGAS', 'items' => [
                ['route' => 'approvals.index', 'icon' => 'clipboard-check', 'label' => 'Permohonan Masuk', 'badge' => '2', 'badgeType' => 'danger'],
                ['route' => 'movements.index', 'icon' => 'route', 'label' => 'Log Keluar/Masuk'],
                ['route' => 'vehicles.index', 'icon' => 'car', 'label' => 'Senarai Kenderaan'],
                ['route' => 'saman.index', 'icon' => 'triangle-alert', 'label' => 'Lapor Saman'],
            ]],
        ],
    ],

    'bottom_nav' => [
        'admin' => [
            ['route' => 'dashboard', 'icon' => 'gauge', 'label' => 'Home'],
            ['route' => 'approvals.index', 'icon' => 'clipboard-check', 'label' => 'Mohon', 'badge' => true],
            ['route' => 'vehicles.index', 'icon' => 'car', 'label' => 'Kenderaan'],
            ['route' => 'saman.index', 'icon' => 'triangle-alert', 'label' => 'Saman', 'badge' => true],
            ['route' => 'reminders.index', 'icon' => 'bell', 'label' => 'Alert', 'badge' => true],
        ],
        'fleet' => [
            ['route' => 'dashboard', 'icon' => 'gauge', 'label' => 'Home'],
            ['route' => 'approvals.index', 'icon' => 'clipboard-check', 'label' => 'Permohonan', 'badge' => true],
            ['route' => 'vehicles.index', 'icon' => 'car', 'label' => 'Kenderaan'],
            ['route' => 'services.index', 'icon' => 'wrench', 'label' => 'Servis'],
            ['route' => 'saman.index', 'icon' => 'triangle-alert', 'label' => 'Saman'],
        ],
        'staff' => [
            ['route' => 'dashboard', 'icon' => 'gauge', 'label' => 'Home'],
            ['route' => 'approvals.index', 'icon' => 'clipboard-check', 'label' => 'Mohon'],
            ['route' => 'vehicles.index', 'icon' => 'car', 'label' => 'Kenderaan'],
            ['route' => 'fuel.index', 'icon' => 'fuel', 'label' => 'Bahan Api'],
            ['route' => 'movements.index', 'icon' => 'route', 'label' => 'Log'],
        ],
        'guard' => [
            ['route' => 'dashboard', 'icon' => 'gauge', 'label' => 'Home'],
            ['route' => 'approvals.index', 'icon' => 'clipboard-check', 'label' => 'Permohonan', 'badge' => true],
            ['route' => 'movements.index', 'icon' => 'route', 'label' => 'Log Keluar'],
            ['route' => 'vehicles.index', 'icon' => 'car', 'label' => 'Kenderaan'],
        ],
    ],
];

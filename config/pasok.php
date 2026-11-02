<?php

return [
    'ai' => [
        'driver' => env('PASOK_AI_DRIVER') ?: 'openai',
        'api_key' => env('PASOK_AI_KEY', ''),
        'base_url' => env('PASOK_AI_BASE_URL') ?: 'https://generativelanguage.googleapis.com/v1beta/openai',
        'model' => env('PASOK_AI_MODEL'),
        'models' => ['openai' => 'gemini-flash-latest', 'anthropic' => 'claude-haiku-5-5'],
        'timeout' => (int) env('PASOK_AI_TIMEOUT', 60),
        'daily_per_business' => (int) env('PASOK_AI_DAILY_PER_BUSINESS', 80),
        'daily_global' => (int) env('PASOK_AI_DAILY_GLOBAL', 1500),
    ],

    'whatsapp' => [
        'driver' => env('PASOK_WA_DRIVER', 'log'),
        'fonnte_token' => env('FONNTE_TOKEN'),
        'fonnte_url' => env('FONNTE_URL', 'https://api.fonnte.com/send'),
        'device' => env('FONNTE_DEVICE'),
        'webhook_secret' => env('PASOK_WEBHOOK_SECRET'),
        'daily_outbound_limit' => (int) env('PASOK_DAILY_OUTBOUND_LIMIT', 500),
        'per_minute' => 12,
    ],

    'demo' => [
        'enabled' => (bool) env('PASOK_DEMO', true),
        'ttl_hours' => 48,
        'per_ip_daily' => 15,
    ],

    'receipt' => [
        'max_kb' => 8192,
        'max_items' => 60,
        'keep_image_days' => 30,
    ],

    'regencies' => ['Badung', 'Bangli', 'Buleleng', 'Denpasar', 'Gianyar', 'Jembrana', 'Karangasem', 'Klungkung', 'Tabanan'],

    'business_types' => [
        'restoran' => 'Restoran / warung makan',
        'kafe' => 'Kafe',
        'hotel' => 'Hotel',
        'villa' => 'Villa / penginapan',
        'katering' => 'Katering',
    ],

    'categories' => [
        'tanaman_pangan' => 'Tanaman pangan',
        'hortikultura' => 'Hortikultura',
        'perkebunan' => 'Perkebunan',
        'peternakan' => 'Peternakan',
        'perikanan' => 'Perikanan',
        'industri' => 'Industri lokal',
    ],

    'quota_groups' => [
        'pertanian' => ['label' => 'Pertanian (pangan, hortikultura, perkebunan)', 'categories' => ['tanaman_pangan', 'hortikultura', 'perkebunan'], 'percent' => 30],
        'peternakan' => ['label' => 'Peternakan', 'categories' => ['peternakan'], 'percent' => 30],
        'perikanan' => ['label' => 'Perikanan', 'categories' => ['perikanan'], 'percent' => 30],
        'industri' => ['label' => 'Industri lokal Bali', 'categories' => ['industri'], 'percent' => 20],
    ],
];

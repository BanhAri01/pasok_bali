<?php

return [
    'ca_bundle' => env('PASOK_CA_BUNDLE'),

    'ai' => [
        'driver' => env('PASOK_AI_DRIVER') ?: 'openai',
        'api_key' => env('PASOK_AI_KEY', ''),
        'base_url' => env('PASOK_AI_BASE_URL') ?: 'https://generativelanguage.googleapis.com/v1beta/openai',
        'model' => env('PASOK_AI_MODEL'),
        'models' => ['openai' => 'gemini-3.5-flash-lite', 'anthropic' => 'claude-haiku-5-5'],
        'fallback_models' => array_values(array_filter(array_map('trim', explode(',', (string) (env('PASOK_AI_FALLBACK_MODELS') ?: 'gemini-flash-lite-latest,gemini-3.6-flash'))))),
        'timeout' => (int) env('PASOK_AI_TIMEOUT', 30),
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
        'session_minutes' => (int) env('PASOK_WA_SESSION_MINUTES', 30),
        'ai_per_phone' => (int) env('PASOK_WA_AI_PER_PHONE', 20),
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

    'substitutes' => [
        'Minyak goreng sawit' => ['Minyak kelapa'],
        'Gula pasir' => ['Gula aren'],
        'Tepung terigu' => ['Tepung beras', 'Tepung tapioka'],
        'Telur ayam ras' => ['Telur ayam kampung', 'Telur bebek'],
        'Daging ayam' => ['Ayam kampung'],
        'Ayam kampung' => ['Daging ayam'],
        'Cabai merah keriting' => ['Cabai merah besar'],
        'Cabai merah besar' => ['Cabai merah keriting'],
        'Beras' => ['Beras merah'],
        'Ikan tuna' => ['Ikan cakalang', 'Ikan tongkol'],
        'Ikan cakalang' => ['Ikan tongkol', 'Ikan tuna'],
        'Ikan tongkol' => ['Ikan cakalang', 'Ikan lemuru'],
        'Ikan kakap' => ['Ikan kerapu'],
        'Ikan kerapu' => ['Ikan kakap'],
        'Ikan nila' => ['Ikan lele', 'Ikan gurame'],
        'Jeruk' => ['Jeruk limau'],
        'Jeruk nipis' => ['Jeruk limau'],
        'Sawi hijau' => ['Sawi putih'],
        'Sawi putih' => ['Sawi hijau'],
    ],

    'quota_groups' => [
        'pertanian' => ['label' => 'Pertanian (pangan, hortikultura, perkebunan)', 'categories' => ['tanaman_pangan', 'hortikultura', 'perkebunan'], 'percent' => 30],
        'peternakan' => ['label' => 'Peternakan', 'categories' => ['peternakan'], 'percent' => 30],
        'perikanan' => ['label' => 'Perikanan', 'categories' => ['perikanan'], 'percent' => 30],
        'industri' => ['label' => 'Industri lokal Bali', 'categories' => ['industri'], 'percent' => 20],
    ],
];

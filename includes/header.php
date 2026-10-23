<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/koneksi.php';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($judul) ? htmlspecialchars($judul) : 'Serenéa CAFE'; ?></title>
    <meta name="description" content="Serenéa CAFE — Sistem pemesanan cafe berbasis web.">

    <link rel="icon" type="image/png" href="<?= BASE_URL; ?>assets/logo-brand.png">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e331e',
                        secondary: '#4a6b46',
                        accent: '#8ea87d',
                        cream: '#f9f8f3',
                        ink: '#142214',
                        muted: '#5c6b5b',
                        line: '#e2e8df'
                    },
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] }
                }
            }
        }
    </script>

    <script src="https://unpkg.com/lucide@latest"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <script src="<?= BASE_URL; ?>assets/js/app.js" defer></script>
</head>
<body class="bg-cream text-ink min-h-screen flex flex-col antialiased">
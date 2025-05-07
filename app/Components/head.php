<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Bengkel Kita - Perbaikan Kendaraan Terbaik' ?></title>
    <link rel="canonical" href="https://bengkel.kita.blue">
    <link rel="icon" href="https://cdn.kita.blue/kita/logo.png">

    <!-- SEO Meta Tags -->
    <meta name="description" content="Bengkel Kita adalah tempat perbaikan kendaraan terbaik di Indonesia">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?= $pageTitle ?? 'Bengkel Kita - Perbaikan Kendaraan Terbaik' ?>">
    <meta property="og:description" content="Bengkel Kita adalah tempat perbaikan kendaraan terbaik di Indonesia">
    <meta property="og:image" content="https://cdn.kita.blue/kita/thumbnail.png">
    <meta property="og:type" content="website">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $pageTitle ?? 'Bengkel Kita - Perbaikan Kendaraan Terbaik' ?>">
    <meta name="twitter:description" content="Bengkel Kita adalah tempat perbaikan kendaraan terbaik di Indonesia">
    <meta name="twitter:image" content="https://cdn.kita.blue/kita/thumbnail.png">

    <!-- Styles and Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    },
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e'
                        }
                    }
                }
            }
        }
        lucide.createIcons();
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

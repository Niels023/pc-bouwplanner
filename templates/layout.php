<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->e($title) ?> | PC-bouwplanner</title>
    <link rel="icon" href="data:,">
    <link rel="stylesheet" href="/css/style.css">
    <script src="/js/live.js" defer></script>
</head>
<body>
    <a class="skip-link" href="#inhoud">Ga naar de inhoud</a>
    <header class="site-header">
        <div class="wrap site-header-inner">
            <a class="logo" href="/">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="5" y="5" width="14" height="14" rx="3" fill="currentColor"/>
                    <rect x="9" y="9" width="6" height="6" rx="1" fill="#fff"/>
                    <path d="M9 2v3M15 2v3M9 19v3M15 19v3M2 9h3M2 15h3M19 9h3M19 15h3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                PC-bouwplanner
            </a>
            <nav aria-label="Hoofdmenu">
                <ul class="site-nav">
                    <li><a href="/bouwen/cpu">Zelf bouwen</a></li>
                    <li><a href="/aanbeveling">Aanbeveling</a></li>
                    <li><a href="/#build-openen">Build openen</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main id="inhoud" class="wrap">
<?= $content ?>
    </main>
    <footer class="site-footer">
        <div class="wrap">
            <p>Prijzen zijn richtprijzen met de peildatum bij elk onderdeel. Controleer ze voordat je iets koopt.</p>
        </div>
    </footer>
</body>
</html>

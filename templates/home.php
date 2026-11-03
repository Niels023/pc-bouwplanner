<section class="hero">
    <h1>Stel je eigen PC samen</h1>
    <p>Kies je onderdelen stap voor stap. De site controleert of alles bij elkaar past en telt de prijs en het stroomverbruik voor je op.</p>
</section>

<section class="choices" aria-label="Waar wil je beginnen?">
    <a class="choice" href="/bouwen/cpu">
        <span class="choice-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                <path d="M17.5 14v7M14 17.5h7"/>
            </svg>
        </span>
        <h2>Zelf samenstellen</h2>
        <p>Kies zelf elk onderdeel, van processor tot voeding. Past iets niet, dan zie je waarom.</p>
        <span class="choice-link">Begin bij de processor <span aria-hidden="true">→</span></span>
    </a>
    <a class="choice" href="/aanbeveling">
        <span class="choice-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 6l2 2 3-3M4 13l2 2 3-3M12 7h8M12 14h8M5 20h1M12 20h8"/>
            </svg>
        </span>
        <h2>Laat een build voorstellen</h2>
        <p>Vertel waarvoor je de PC gebruikt, dan krijg je een complete build met uitleg bij elk onderdeel.</p>
        <span class="choice-link">Vraag een voorstel <span aria-hidden="true">→</span></span>
    </a>
</section>

<section class="how" aria-labelledby="how-title">
    <h2 id="how-title">Zo werkt het</h2>
    <ol class="how-steps">
        <li><strong>Kies of laat kiezen</strong> Stel zelf je PC samen, of vertel wat je ermee wilt doen en krijg een voorstel.</li>
        <li><strong>Alles wordt gecontroleerd</strong> Past een onderdeel niet bij de rest, dan zie je meteen waarom.</li>
        <li><strong>Bewaar en deel</strong> Sla je build op en open hem later weer met een code van acht tekens.</li>
    </ol>
</section>

<section class="open-build card" id="build-openen">
    <h2>Build openen</h2>
    <form action="/build/open" method="get" class="inline-form">
        <div class="field">
            <label for="code">Deelcode</label>
            <input id="code" name="code" type="text" maxlength="8" autocomplete="off" required>
        </div>
        <button type="submit" class="button">Openen</button>
    </form>
</section>

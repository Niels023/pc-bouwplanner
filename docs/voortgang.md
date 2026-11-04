# Voortgang

Logboek per werksessie. Per sprint staan hier ook het sprintdoel, de review en de retrospective.

## Sprint 1: projectstart en ontwerp

Start: woensdag 28 oktober 2026.

**Sprintdoel:** de repository staat klaar en de opdracht, eisen en het ontwerp zijn af en goedgekeurd, zodat ik daarna kan beginnen met bouwen.

**Geselecteerde issues:** #1 (F-01, database met onderdelen). Voor de projectopzet is ook gewerkt aan #29, #30, #31 en #34. Op 30 oktober toegevoegd omdat het sprintdoel al gehaald was: #5 t/m #11 (compatibiliteitsregels) en #13 (totalen).

### Sessie woensdag 28 oktober 2026

**Gedaan:**

- Techniek gekozen. Het eerste voorstel was Node.js met Express. Ik heb gekozen voor PHP 8.2 en MySQL (MariaDB in XAMPP), met HTML en CSS en zo min mogelijk JavaScript.
- Afgesproken dat er geen urenplanning komt. Er is geen deadline. Oplevering is een demo aan de docent en het verslag doorsturen.
- Eigen git-repository gemaakt in de projectmap met `main` als hoofdbranch.
- Naam en e-mail voor commits ingesteld voor dit project.
- `.gitignore`, `.gitattributes` en een eerste README toegevoegd.
- Op branch `docs/opdracht-en-ontwerp`: opdrachtomschrijving, eisen volgens MoSCoW (F-01 t/m F-30, NF-01 t/m NF-09), technisch ontwerp met acht diagrammen (ook als PNG in `docs/img/`) en de onderbouwing van de keuzes.

- Opdracht, eisen en ontwerp goedgekeurd en naar `main` gemerged.
- GitHub CLI geïnstalleerd en ingelogd. Repository `Niels023/pc-bouwplanner` aangemaakt en `main` gepusht.
- 34 issues aangemaakt: F-01 t/m F-25 en NF-01 t/m NF-09, met labels voor de prioriteit. F-01 t/m F-25 hebben hetzelfde nummer als hun issue.
- Composer geïnstalleerd. Projectopzet gemaakt met phpdotenv, PHPUnit 11 en PHP_CodeSniffer 4 (PSR-12). De databasegegevens staan in `.env`.
- Op branch `feature/database` (#1): databaseschema, 57 bestaande onderdelen als seed-data en een seed-script. 4 tests controleren de data en slagen. De linter geeft 0 fouten.
- Ontwerp aangepast: `m2_slots` is uit het ERD en het klassendiagram gehaald.
- Op branch `fix/onderdelendata` (#1): alle onderdelen gecontroleerd. Prijzen opgezocht in de Tweakers Pricewatch, maten opgezocht bij fabrikanten en winkels. Onderdelen die niet meer te koop zijn vervangen. De data bevat nu 51 onderdelen. Alle bronnen en keuzes staan in `docs/data-controle.md`.

**Open:**

- De budgetverdeling van de aanbeveling (ontwerp §7.1) opnieuw bekijken als ik aan F-16 begin. Die gaat uit van 8% voor geheugen, maar met de huidige prijzen kost geheugen in een goedkope build bijna een kwart van het budget.
- Volgende stap: de compatibiliteitsregels (F-05 t/m F-11).

**Afwijkingen en wat ik eraan gedaan heb:**

- Mijn hele gebruikersmap (`C:\Users\Memeg`) bleek een git-repository te zijn. Daardoor zou git ook al mijn andere bestanden zien. Oplossing: een aparte repository in de projectmap gemaakt.
- In de algemene git-instellingen stonden invulwaarden in plaats van mijn naam en e-mail. Oplossing: mijn eigen naam en e-mail ingesteld voor deze repository.
- Composer installeren met `winget install Composer.Composer` lukte niet ("No package found matching input criteria"). Composer staat niet in winget. Oplossing: Composer geïnstalleerd met de installer van getcomposer.org.
- Na het installeren van de GitHub CLI vond de terminal `gh` niet, omdat de terminal al open stond voor de installatie. Oplossing: `gh` gestart met het volledige pad.
- `composer test` faalde omdat in `phpunit.xml` testmappen stonden die nog niet bestaan. Oplossing: alleen de bestaande testmap in de configuratie gezet. De andere mappen komen erbij als er tests in staan.
- Bij het maken van de seed-data bleek dat ik het aantal M.2-sloten per moederbord niet zeker wist en dat geen enkele regel het gebruikt. Oplossing: het veld uit het ontwerp en de database gehaald.
- Bij het controleren van de data bleken veel onderdelen niet meer te koop, alleen nog bij een of twee winkels te krijgen of veel te duur. Voorbeelden: de oudere videokaarten, twee B650-moederborden en de Crucial-SSD's. Oplossing: 24 onderdelen vervangen of weggehaald (lijst in `docs/data-controle.md`).
- Geheugen, opslag en videokaarten bleken veel duurder dan ik had geschat, door een tekort aan geheugenchips. Zo kost 16 GB DDR5 € 314,10 in plaats van de geschatte € 65. Oplossing: de echte prijzen gebruikt. De gevolgen voor de aanbeveling staan bij "Open".
- Tweakers was niet automatisch uit te lezen. Oplossing: de Pricewatch met de hand doorzocht in de browser.

**Komt de planning in gevaar?** Nee. Er is geen deadline en er is nog niets uitgelopen.

### Sessie vrijdag 30 oktober 2026

**Gedaan:**

- Op branch `feature/compatibiliteit`: modelklassen voor alle onderdelen en de `Build`, die prijs en verbruik optelt (#13).
- De zes compatibiliteitsregels gebouwd (#5 t/m #10), elk in een eigen klasse met unit tests. Ook de `CompatibilityChecker` en de totaalstatus (#11). De tests heb ik eerst geschreven en zien falen.
- Alle 42 tests slagen en de linter geeft 0 fouten.
- Mutatietest gedaan: 12 fouten expres in de code gezet. De tests vingen ze allemaal.
- `testplan.md`, `testrapport.md` en `uitleg.md` gemaakt. Ontwerp en keuzes bijgewerkt.
- F-11 aangepast: een build is pas "in orde" als alle acht onderdelen gekozen zijn.
- Op branch `feature/onderdelen-ophalen`: view `part_details`, `PartRepository` en `PartMapper`.
- Op branch `feature/rest-api`: router, `Request`, `Response` en foutafhandeling, plus de endpoints voor onderdelen, build opslaan, build ophalen en build controleren (#3, #14, #15, #18).
- Op branch `feature/aanbeveling`: de `Recommender` met budgetverdeling per doel, vooruitkijken en het verdelen van restgeld, plus het endpoint `/api/recommendation` (#16, #17, #18). Budgetverdeling aangepast aan de echte prijzen.
- Alle 144 tests slagen en de linter geeft 0 fouten. De API ook met de hand getest op een echte server. Mutatietest gedaan op de aanbeveling.

- De website gebouwd op branches `feature/paginas-basis`, `feature/bouwplanner` en `feature/aanbeveling-pagina`: startpagina, acht stappen met filters en kaartjes, grijze onderdelen met uitleg, totalen, overzicht, opslaan, openen met deelcode en de aanbevelingspagina. Eén klein JavaScript-bestand om te kiezen zonder herladen.
- Alle 200 tests slagen en de linter geeft 0 fouten. Handmatig getest: breedtes van 360 tot 1920 pixels, Lighthouse (100 op alle gemeten pagina's), contrast, toetsenbord, zonder JavaScript, snelheid en een schone installatie. Alle Must haves zijn nu gebouwd.
- Schermafbeeldingen van de site in `docs/img/` gezet voor het verslag.
- Should haves gebouwd en gemerged: sorteren op prijs en naam (#20, `feature/sorteren`), controle op beeld zonder videokaart (#21, `feature/zonder-videokaart`) en de aanbeveling overnemen in de bouwplanner (#19, `feature/aanbeveling-overnemen`). Werken krijgt nu geen losse videokaart meer, daardoor kost de goedkoopste build voor werken € 766,21 in plaats van € 1.126. Alle 228 tests slaagden.
- Ik vond dat alleen een budget weinig zegt voor iemand die niets van PC's weet. Daarom twee nieuwe Should haves toegevoegd: F-31 (#35, scherm en soort games kiezen) en F-32 (#36, budget mag leeg). FPS per videokaart opgezocht bij Tom's Hardware. Op branch `feature/aanbeveling-scherm-en-games` gebouwd en getest, 258 tests.

**Open:**

- Gebruikerstest met 2 of 3 echte mensen (`docs/testplan.md`, hoofdstuk 6). Dit moet ik zelf doen.
- De site laten zien aan de docent en de feedback opschrijven.
- `verbetervoorstellen.md` maken, op basis van de gebruikerstest en de feedback. Eerste punten: de aanbeveling kijkt alleen naar prijs en niet naar koelvermogen, en de site is nog niet getest in Firefox, Safari en op een echte telefoon.
- Documentatie van F-19 t/m F-21, F-31 en F-32 bijwerken en `feature/aanbeveling-scherm-en-games` mergen.
- Eén persoon laten installeren met de README voor NF-09 (#34).

**Afwijkingen en wat ik eraan gedaan heb:**

- `Build` heeft geen methode `part(Category)` gekregen zoals in het ontwerp stond, maar een methode per onderdeel. Reden: dan weet de editor welke velden er zijn. Het ontwerp en `keuzes.md` zijn aangepast.
- `category` is geen veld van `Part` geworden, maar een methode die elke subklasse zelf invult. Het ontwerp is aangepast.
- Mijn hulpfunctie `result()` in een test gaf een fatale fout, omdat PHPUnit al een methode met die naam heeft. Oplossing: hernoemd naar `resultFor()`.
- Een build zonder opslag kreeg de totaalstatus "in orde", omdat geen enkele regel opslag nodig heeft. Oplossing: F-11 aangepast. Een build is nu pas "in orde" als alle acht onderdelen gekozen zijn.
- De API gaf op de echte server een 500, terwijl alle tests slaagden. Mijn eigen database was gevuld voordat de view bestond. Oplossing: `composer seed` gedraaid en in de README gezet dat dit nodig is na een wijziging in het schema.
- `sort` staat in het ontwerp bij de filters, maar is nog niet gebouwd. Dat hoort bij F-20 (Should) en komt later.
- De aanbeveling uit het ontwerp bleek te simpel. Reserveren voor het goedkoopste onderdeel per categorie werkt niet, omdat onderdelen van elkaar afhangen: een AM5-processor dwingt je tot duur DDR5. Oplossing: bij elke keuze vooruitkijken of de build nog af kan.
- De eerste versie slaagde voor de tests, maar gaf vreemde uitkomsten op de echte data, zoals een koeler van € 139 op een zuinige processor en € 865 over bij € 4.000. Oplossing: geld schuift niet meer door, en restgeld gaat aan het eind naar de belangrijkste onderdelen voor het doel. Het ontwerp en `keuzes.md` zijn aangepast.
- De budgetverdeling uit het ontwerp klopte niet met de echte prijzen. Oplossing: nieuwe percentages bepaald door de aanbeveling op de echte onderdelen te draaien bij budgetten van € 1.200 tot € 6.000.
- De mutatietest vond twee zwakke tests. Oplossing: tests verbeterd, zie het testrapport.
- De website gebruikt de API niet, anders dan in het ontwerp stond. Na een keuze moeten ook de kaartjes veranderen, en dan had ik die logica in JavaScript moeten schrijven. Oplossing: het script haalt de HTML van de pagina op en vervangt alleen de stukken die veranderen. Het ontwerp en `keuzes.md` zijn aangepast.
- Bij het bekijken in de browser vielen dingen op die de tests niet zagen: onderdelen die niet passen stonden bovenaan, de filters duwden op een telefoon de kaartjes weg en een kolom was te smal. Oplossing: allemaal aangepast, zie het testrapport.
- Bij het committen van het sorteren heb ik `git add -A` gebruikt. Daardoor kwamen twee bestanden van F-21 (`IntegratedGraphicsRule` en de test ervan) in de commit voor sorteren, en die ging naar mijn lokale `main`. De geschiedenis van `main` herschrijf ik niet. Oplossing: F-21 eerst afgemaakt en gemerged, en pas daarna gepusht. Zo heeft `main` op GitHub nooit halve code gehad. Sindsdien noem ik bij elke commit de bestanden zelf.
- F-32 verandert het acceptatiecriterium van F-16: een leeg budget gaf eerst een melding en geeft nu de goedkoopste build. F-16 is daarop aangepast.
- De mutatietest op F-31 vond een dubbele controle voor werken met scherm en games. De overbodige controle is weggehaald, zie het testrapport.

**Komt de planning in gevaar?** Nee.

### Sessie dinsdag 3 november 2026

**Gedaan:**

- Documentatie van F-19, F-20, F-21, F-31 en F-32 bijgewerkt: ontwerp met nieuwe PNG's, keuzes, bron van de FPS-gegevens, testplan, testrapport, uitleg en README.
- Testrun: 258 tests slagen, de linter geeft 0 fouten.
- Ik vond de vormgeving van de site lelijk. Drie stijlen bekeken: licht en modern, donker in gaming-stijl, of meegaan met de instelling van het apparaat. Gekozen voor licht en modern.
- `feature/aanbeveling-scherm-en-games` gemerged en gepusht. Issues #19, #20, #21, #35 en #36 gesloten.
- Nieuwe eis NF-10 (verzorgde vormgeving, #37) toegevoegd. Op branch `feature/vormgeving` de hele site in de nieuwe stijl gezet: kleuren als variabelen in de CSS, een startpagina met grote keuzes en uitleg hoe het werkt, kaartjes met labels voor de specificaties, een totaalbalk die onderaan in beeld blijft en grote keuzevlakken in het aanbevelingsformulier.
- Getest: 258 tests, linter schoon, Lighthouse 100 op vijf pagina's, contrast minimaal 5,02:1, geen horizontaal scrollen van 360 tot 1920 pixels. Schermafbeeldingen in `docs/img/` vernieuwd.
- Refactor op branch `refactor/minder-bestanden` (#30), omdat ik alles moet kunnen uitleggen en er niet meer code moet zijn dan nodig. Vier mogelijkheden bekeken. Gekozen: ongebruikte code weg en de zeven regelklassen samen in de `CompatibilityChecker`. De onderdeelklassen zijn gebleven, omdat de regels dan met `$build->cpu()->socket` kunnen werken. Van 55 naar 45 PHP-bestanden in `src/`. Alle 258 tests slagen en de mutatietest vangt alle 16 ingebouwde fouten.
- In `docs/uitleg.md` uitgelegd wat `index.php`, `bootstrap.php` en `layout.php` doen, omdat ik dat zelf nog niet goed kon uitleggen.
- De Could haves gebouwd: builds vergelijken (#22), afdrukken op één A4 (#23), controle of de koeler op de socket past (#24) en waterkoeling (#25). Daarvoor de sockets van alle koelers, twee waterkoelers en de radiatorruimte van alle kasten opgezocht bij de fabrikanten. De data heeft nu 53 onderdelen. 278 tests slagen, Lighthouse 100 op de nieuwe pagina's.

- `docs/verbetervoorstellen.md` gemaakt, met zeven voorstellen, prioriteit en een planning in sprints. In `docs/opdracht.md` opgeschreven hoe de scope tijdens het project veranderd is.
- Het verslag deels ingevuld: alle blokken van B1-K1 en de projectmethodiek van B1-K2, op basis van de documentatie, de git-geschiedenis en de issues. Daarna ook samenwerken, feedback en evaluatie, met mijn eigen antwoorden. Alleen de presentatie staat nog open.

**Feedback van klasgenoten:** er mochten meer keuzes komen als de site een build voor je maakt. Verwerkt in F-31 en F-32: je kiest nu ook je scherm en het soort games, het budget mag leeg, en je ziet in gewone taal wat de build haalt.

**Afspraak met mezelf:** het project is in november af. Op 3 november zijn alle eisen behalve NF-09 gebouwd en getest, dus dat ligt op schema.

**Open:**

- De drie blokken over de presentatie in het verslag (B1-K2-W2). Die kan ik pas invullen na de demo.
- Een sprintreview en retrospective van sprint 1 schrijven.
- Gebruikerstest met 2 of 3 mensen (testplan hoofdstuk 6), nu ook met TC-G-09 voor scherm en games. Daarin ook vragen wat ze van de nieuwe vormgeving vinden.
- De site laten zien aan de docent en de feedback opschrijven.
- `verbetervoorstellen.md` aanvullen met wat de gebruikerstest en de docent opleveren.
- Eén persoon laten installeren met de README (#34).

**Afwijkingen en wat ik eraan gedaan heb:**

- DeepCool noemt LGA1851 niet bij de AK620. Ik heb de veilige kant gekozen: in de data past hij er niet op, dus de site geeft een fout met de Core Ultra 5 245K.
- Bij de Fractal North past een radiator van 360 mm alleen voorin, en dan is er minder ruimte voor de videokaart. Omdat de site die combinatie niet controleert, staat in de data 240 mm.
- De mutatietest vond een te zwakke test voor de waterkoeler. De test kijkt nu ook naar de melding.
- NF-10 hoort bij de eisen, maar het issuenummer volgt niet de vaste regel. NF-01 t/m NF-09 zijn #26 t/m #34, maar #35 en #36 waren al gebruikt voor F-31 en F-32. NF-10 is daarom #37.

**Komt de planning in gevaar?** Nee.

### Sessie woensdag 4 november 2026

**Gedaan:**

- Feedback opgeschreven. Ik heb de site voor de nieuwe vormgeving gepresenteerd aan twee familieleden die weinig van PC's weten. Ik vroeg wat ze vonden van hoe de website eruitzag, en hun antwoord was dat die niet echt modern was. Dat heb ik op 3 november aangepakt (NF-10, #37).
- De feedback van klasgenoten preciezer opgeschreven: er mochten meer keuzes komen als de site een build voor je maakt.
- Het verslag bijgewerkt: de presentatie en de feedback staan erin. Nu zijn 27 van de 28 blokken ingevuld.

**Open:**

- In het verslag nog "Beantwoorden vragen" bij de presentatie: welke vragen de familieleden stelden en wat ik antwoordde.
- Gebruikerstest met 2 of 3 mensen (testplan hoofdstuk 6).
- Een sprintreview en retrospective van sprint 1 schrijven.
- Eén persoon laten installeren met de README (#34).

**Komt de planning in gevaar?** Nee. De afspraak om in november klaar te zijn ligt op schema.

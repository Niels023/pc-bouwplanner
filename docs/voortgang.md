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
- Alle 105 tests slagen en de linter geeft 0 fouten. De API ook met de hand getest met curl op een echte server.

**Open:**

- De budgetverdeling van de aanbeveling opnieuw bekijken bij F-16 (zie sessie 28 oktober).
- Volgende stap: de aanbeveling (F-16, F-17) met het laatste endpoint van de API.

**Afwijkingen en wat ik eraan gedaan heb:**

- `Build` heeft geen methode `part(Category)` gekregen zoals in het ontwerp stond, maar een methode per onderdeel. Reden: dan weet de editor welke velden er zijn. Het ontwerp en `keuzes.md` zijn aangepast.
- `category` is geen veld van `Part` geworden, maar een methode die elke subklasse zelf invult. Het ontwerp is aangepast.
- Mijn hulpfunctie `result()` in een test gaf een fatale fout, omdat PHPUnit al een methode met die naam heeft. Oplossing: hernoemd naar `resultFor()`.
- Een build zonder opslag kreeg de totaalstatus "in orde", omdat geen enkele regel opslag nodig heeft. Oplossing: F-11 aangepast. Een build is nu pas "in orde" als alle acht onderdelen gekozen zijn.
- De API gaf op de echte server een 500, terwijl alle tests slaagden. Mijn eigen database was gevuld voordat de view bestond. Oplossing: `composer seed` gedraaid en in de README gezet dat dit nodig is na een wijziging in het schema.
- `sort` staat in het ontwerp bij de filters, maar is nog niet gebouwd. Dat hoort bij F-20 (Should) en komt later.

**Komt de planning in gevaar?** Nee.

# Voortgang

Logboek per werksessie. Per sprint staan hier ook het sprintdoel, de review en de retrospective.

## Sprint 1: projectstart en ontwerp

Start: woensdag 28 oktober 2026.

**Sprintdoel:** de repository staat klaar en de opdracht, eisen en het ontwerp zijn af en goedgekeurd, zodat ik daarna kan beginnen met bouwen.

**Geselecteerde issues:** #1 (F-01, database met onderdelen). Voor de projectopzet is ook gewerkt aan #29, #30, #31 en #34.

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

**Open:**

- Prijzen en twijfelgevallen in `docs/data-controle.md` nakijken. Pas daarna kan #1 dicht.
- Volgende stap: de compatibiliteitsregels (F-05 t/m F-11).

**Afwijkingen en wat ik eraan gedaan heb:**

- Mijn hele gebruikersmap (`C:\Users\Memeg`) bleek een git-repository te zijn. Daardoor zou git ook al mijn andere bestanden zien. Oplossing: een aparte repository in de projectmap gemaakt.
- In de algemene git-instellingen stonden invulwaarden in plaats van mijn naam en e-mail. Oplossing: mijn eigen naam en e-mail ingesteld voor deze repository.
- Composer installeren met `winget install Composer.Composer` lukte niet ("No package found matching input criteria"). Composer staat niet in winget. Oplossing: Composer geïnstalleerd met de installer van getcomposer.org.
- Na het installeren van de GitHub CLI vond de terminal `gh` niet, omdat de terminal al open stond voor de installatie. Oplossing: `gh` gestart met het volledige pad.
- `composer test` faalde omdat in `phpunit.xml` testmappen stonden die nog niet bestaan. Oplossing: alleen de bestaande testmap in de configuratie gezet. De andere mappen komen erbij als er tests in staan.
- Bij het maken van de seed-data bleek dat ik het aantal M.2-sloten per moederbord niet zeker wist en dat geen enkele regel het gebruikt. Oplossing: het veld uit het ontwerp en de database gehaald.

**Komt de planning in gevaar?** Nee. Er is geen deadline en er is nog niets uitgelopen.

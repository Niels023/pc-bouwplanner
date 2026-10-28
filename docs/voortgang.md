# Voortgang

Logboek per werksessie. Per sprint staan hier ook het sprintdoel, de review en de retrospective.

## Sprint 1: projectstart en ontwerp

Start: woensdag 28 oktober 2026.

**Sprintdoel:** de repository staat klaar en de opdracht, eisen en het ontwerp zijn af en goedgekeurd, zodat ik daarna kan beginnen met bouwen.

**Geselecteerde issues:** nog geen, de GitHub-repository bestaat nog niet.

### Sessie woensdag 28 oktober 2026

**Gedaan:**

- Techniek gekozen. Het eerste voorstel was Node.js met Express. Ik heb gekozen voor PHP 8.2 en MySQL (MariaDB in XAMPP), met HTML en CSS en zo min mogelijk JavaScript.
- Afgesproken dat er geen urenplanning komt. Er is geen deadline. Oplevering is een demo aan de docent en het verslag doorsturen.
- Eigen git-repository gemaakt in de projectmap met `main` als hoofdbranch.
- Naam en e-mail voor commits ingesteld voor dit project.
- `.gitignore`, `.gitattributes` en een eerste README toegevoegd.
- Op branch `docs/opdracht-en-ontwerp`: opdrachtomschrijving, eisen volgens MoSCoW (F-01 t/m F-30, NF-01 t/m NF-09), technisch ontwerp met acht diagrammen (ook als PNG in `docs/img/`) en de onderbouwing van de keuzes.

**Open:**

- Akkoord op opdracht, eisen en ontwerp. Daarna gaat de branch naar `main`.
- GitHub-repository aanmaken en de eisen als issues invoeren.
- Composer installeren, dat is nodig voor PHPUnit en PHP_CodeSniffer.

**Afwijkingen en wat ik eraan gedaan heb:**

- Mijn hele gebruikersmap (`C:\Users\Memeg`) bleek een git-repository te zijn. Daardoor zou git ook al mijn andere bestanden zien. Oplossing: een aparte repository in de projectmap gemaakt.
- In de algemene git-instellingen stonden invulwaarden in plaats van mijn naam en e-mail. Oplossing: mijn eigen naam en e-mail ingesteld voor deze repository.
- Composer installeren met `winget install Composer.Composer` lukte niet ("No package found matching input criteria"). Composer staat niet in winget. Oplossing: Composer installeren met de installer van getcomposer.org. Dit staat nog open.

**Komt de planning in gevaar?** Nee. Er is geen deadline en er is nog niets uitgelopen.

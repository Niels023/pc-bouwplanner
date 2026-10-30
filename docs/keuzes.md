# Onderbouwing van de keuzes

Per keuze staat hier wat ik gekozen heb, welk alternatief ik heb overwogen en waarom dat het niet werd.

## 1. Programmeertaal en framework: PHP 8.2 zonder framework

**Gekozen:** PHP 8.2 zonder framework, met een eigen kleine router en PDO voor de database.

**Waarom:**

- PHP ken ik al van school, en ik moet elke regel code kunnen uitleggen.
- PHP 8.2 heeft wat ik nodig heb: enums (voor categorieën en statussen), `readonly`-eigenschappen en strikte types.
- Zonder framework is er geen code die "vanzelf" iets doet. Alles wat de applicatie doet, staat in mijn eigen bestanden.
- PHP en MySQL draaien al op mijn laptop via XAMPP.

**Alternatieven:**

| Alternatief | Waarom niet |
|---|---|
| Laravel | Laravel regelt veel automatisch (routing, ORM, validatie). Dat scheelt werk, maar dan moet ik bij de presentatie ook uitleggen wat Laravel achter de schermen doet. Voor een project van deze grootte is gewone PHP met een duidelijke mappenstructuur overzichtelijker. |
| Node.js met Express | Eén taal voor frontend en backend, maar ik ken JavaScript minder goed dan PHP en ik wil zo min mogelijk JavaScript. |

## 2. Database: MySQL

**Gekozen:** MySQL (in XAMPP is dat MariaDB, die werkt hetzelfde).

**Waarom:** de gegevens hebben een vaste structuur met relaties: een moederbord hoort bij een socket, een build bevat onderdelen. Een relationele database past daar goed bij. Met foreign keys en unieke sleutels bewaakt de database zelf dat de gegevens kloppen. MySQL ken ik van school en het staat al in XAMPP.

**Alternatieven:**

| Alternatief | Waarom niet |
|---|---|
| SQLite | Eén bestand en geen server nodig, dat is handig. Maar op school werk ik met MySQL en phpMyAdmin, en MySQL is gebruikelijker voor een website. |
| MongoDB | Een documentdatabase is flexibel, maar ik heb juist relaties en vaste velden nodig voor de controles. |

## 3. Datamodel: één basistabel plus een tabel per categorie

**Gekozen:** de tabel `parts` met de velden die elk onderdeel heeft, en per categorie een tabel met de specificaties (`cpus`, `motherboards`, enzovoort).

**Waarom:** elk veld heeft een vast type en kan verplicht zijn. Een moederbord moet een socket hebben, een kast een maximale GPU-lengte. Zoeken op alle onderdelen (bijvoorbeeld op merk of prijs) gaat via één tabel.

**Alternatieven:**

| Alternatief | Waarom niet |
|---|---|
| Eén grote tabel met alle velden | Veel lege velden (een voeding heeft geen socket) en de database kan niet afdwingen welk veld verplicht is voor welke categorie. |
| Sleutel-waardetabel (`part_id`, `naam`, `waarde`) | Heel flexibel, maar alles wordt tekst. Vergelijken op getallen zoals lengte in mm wordt dan omslachtig en foutgevoelig. |

Sockets, RAM-types en formaten staan in aparte tabellen. Daardoor kan een socket maar op één manier geschreven worden en vergelijken de regels altijd dezelfde waarden.

**View `part_details`:** omdat een onderdeel over meerdere tabellen verdeeld is, heb je veel joins nodig om het op te halen. Die staan op één plek in een view. Het alternatief was per categorie een eigen query in PHP schrijven. Dat zijn acht query's die bijna hetzelfde doen, en filteren over alle categorieën tegelijk wordt dan lastig.

## 4. Prijzen in centen

**Gekozen:** in de database `DECIMAL(7,2)`, in PHP hele centen (`int`).

**Waarom:** kommagetallen (`float`) kunnen afrondingsfouten geven, bijvoorbeeld 0,1 + 0,2 is in PHP niet precies 0,3. Bij de aanbeveling vergelijk ik het totaal met het budget. Een afrondingsfout zou daar een verkeerd antwoord kunnen geven. Met hele centen kan dat niet.

**Alternatief:** overal `float` en afronden bij het tonen. Dat werkt meestal, maar niet altijd. Daarom niet gekozen.

## 5. REST API naast server-side pagina's

**Gekozen:** PHP maakt de HTML-pagina's op de server. Daarnaast is er een REST API met JSON. Beide gebruiken dezelfde logica en repositories.

**Waarom:**

- De site werkt zonder JavaScript en ik hoef weinig JavaScript te schrijven.
- De API maakt de controle en de aanbeveling los te testen met API-tests, zonder browser.
- Het kleine JavaScript-bestand gebruikt de API om de totalen bij te werken zonder de pagina te herladen.

**Opzet van de API:** de paden gaan over dingen (`/api/parts`, `/api/builds`), de HTTP-methode zegt wat er gebeurt (GET ophalen, POST aanmaken). De controle is `POST /api/builds/check`, omdat je daar een lijst met onderdelen meestuurt in de body. De aanbeveling is `GET /api/recommendation`, omdat die niets opslaat en met dezelfde invoer altijd hetzelfde antwoord geeft. Fouten hebben altijd dezelfde vorm (`error.code` en `error.message`), zodat de frontend ze op één manier kan tonen.

**Alternatieven:**

| Alternatief | Waarom niet |
|---|---|
| Alleen een API en een frontend die alles met JavaScript ophaalt | Dan moet de hele frontend in JavaScript en werkt de site niet zonder JavaScript. Ik wil juist zo min mogelijk JavaScript. |
| Alleen pagina's, geen API | De API is een eis van de opdracht, en zonder API kan ik de logica minder goed los testen. |

## 6. Inloggen: nee, opslaan met een deelcode

**Gekozen:** een opgeslagen build krijgt een willekeurige code van 8 tekens. Met die code of de link kan iedereen de build openen.

**Waarom:** het doel is een build bewaren en delen. Daarvoor is geen account nodig. De code is willekeurig, zodat je niet de builds van anderen kunt vinden door een nummer op te hogen (zoals bij `/build/1`, `/build/2`).

**Alternatief:** accounts met e-mail en wachtwoord. Dat vraagt om wachtwoorden veilig opslaan, sessies beveiligen en wachtwoord vergeten. Te veel werk voor iets wat de gebruiker niet nodig heeft.

## 7. Compatibiliteit: elke regel een eigen klasse

**Gekozen:** een interface `CompatibilityRule` met per regel een eigen klasse, en een `CompatibilityChecker` die alle regels uitvoert.

**Waarom:**

- Elke regel is klein en apart te testen.
- Een nieuwe regel toevoegen betekent één nieuwe klasse, zonder bestaande code te veranderen.
- De controle op ontbrekende onderdelen zit op één plek, in de checker, en niet zes keer in elke regel.
- De logica staat los van de API en de pagina's. De aanbeveling gebruikt dezelfde checker, zodat een aanbeveling nooit iets voorstelt wat de bouwplanner afkeurt.

**Alternatief:** één functie met een lange reeks `if`-statements. Dat is in het begin sneller, maar wordt lang, lastig te testen en bij elke nieuwe regel moet je in dezelfde functie zitten.

**Build met een methode per onderdeel:** `Build` heeft methodes als `cpu()` en `psu()`. Het alternatief was één methode `part(Category)`. Die geeft alleen een `Part` terug, dus PHP en PhpStorm weten dan niet dat er een veld `socket` is. Dat kost zeven korte methodes, maar de regels worden er duidelijker en veiliger van.

**Statussen:** naast in orde en fout heb ik "waarschuwing" (alleen voor de voeding, als die net genoeg is maar weinig marge heeft) en "nog niet gecontroleerd" (als een onderdeel nog ontbreekt). Zonder die laatste status zou een halve build als fout of juist als goed worden gezien, en dat klopt allebei niet.

**Formaat moederbord en kast:** ik geef elk formaat een rangnummer (Mini-ITX 1, Micro-ATX 2, ATX 3, E-ATX 4). Een kast slaat het grootste formaat op dat past. In de praktijk past in een kast ook elk kleiner formaat. Het alternatief, per kast een lijst met alle formaten, kost meer tabellen en invoer zonder dat de uitkomst verandert.

## 8. Verbruik en voedingsmarge

**Gekozen:** elk onderdeel heeft een verbruik in watt. Het geschatte verbruik van de build is de som daarvan. De voeding moet dat verbruik plus 30% marge kunnen leveren.

- **Processor:** het maximale vermogen volgens de fabrikant (bij AMD de PPT, bij Intel de Maximum Turbo Power), omdat een processor onder zware belasting meer kan verbruiken dan zijn TDP.
- **Videokaart:** het totale vermogen van de kaart volgens de fabrikant (total board power).
- **Overige onderdelen:** een vaste schatting per categorie (bijvoorbeeld moederbord, geheugen, opslag en ventilatoren). Die schattingen zet ik in de lijst met twijfelgevallen.
- **Voeding:** verbruikt zelf niets in deze berekening, die levert het vermogen.

**Waarom 30% marge:** een voeding werkt het efficiëntst en stilst als hij niet op zijn maximum draait, en videokaarten hebben korte pieken boven hun opgegeven vermogen. 30% is een gangbare marge in bouwadviezen. De marge staat in de configuratie, dus aanpassen kan zonder de code te veranderen.

**Alternatief:** alleen de TDP optellen zonder marge. Dat geeft een te lage schatting en dus het advies om een te zwakke voeding te kopen.

## 9. Aanbeveling met vaste regels

**Gekozen:** het budget wordt per doel volgens vaste percentages over de categorieën verdeeld. Per categorie kiest de module het duurste compatibele onderdeel binnen het deelbudget. Wat over is, schuift door naar de volgende categorie.

**Waarom:**

- Elke keuze is uit te leggen met één zin: "het beste onderdeel binnen € X".
- Dezelfde invoer geeft altijd dezelfde uitkomst, dus het is goed te testen.
- De percentages staan in de configuratie en zijn makkelijk aan te passen als testgebruikers vinden dat een verdeling niet klopt.

**Prijs als maat voor kwaliteit:** binnen een categorie is een duurder onderdeel meestal sneller of beter. Dat klopt niet altijd, maar het is een eerlijke en uitlegbare regel. Ik noem dit ook als beperking in de verbetervoorstellen.

**Alternatieven:**

| Alternatief | Waarom niet |
|---|---|
| Prestatiescores uit benchmarks | Beter dan prijs, maar ik kan de scores niet zelf controleren en ze verschillen per bron. Verzonnen of onzekere cijfers wil ik niet in de database. |
| Alle combinaties uitproberen en de beste kiezen | Met 50 onderdelen zijn dat honderdduizenden combinaties. Dat is traag en lastig uit te leggen. |
| AI | Niet uitlegbaar, niet voorspelbaar en valt buiten de opdracht. |

## 10. Weinig JavaScript

**Gekozen:** de site werkt met gewone HTML-formulieren. Na een POST stuurt de server door naar een normale pagina, zodat vernieuwen geen dubbele keuze geeft. Eén klein JavaScript-bestand werkt de totalen bij zonder herladen.

**Waarom:** minder JavaScript is minder code die fout kan gaan. De site werkt ook als JavaScript uit staat of niet laadt. Dat helpt ook voor de toegankelijkheid, want gewone formulieren en knoppen werken vanzelf met het toetsenbord en met schermlezers.

**Alternatief:** een frontendframework zoals React of Vue. Dat is veel extra code en kennis voor een site met vijf pagina's.

## 11. Testen: PHPUnit

**Gekozen:** PHPUnit voor unit tests (regels, checker, aanbeveling) en voor API-tests (verzoeken naar de router met een testdatabase).

**Waarom:** PHPUnit is de standaard voor PHP en werkt met Composer. De regels en de aanbeveling kennen geen database, dus die test ik met onderdelen die ik in de test zelf aanmaak. Zo kan ik precies de randgevallen maken die ik wil, zoals een voeding met precies genoeg vermogen.

**Alternatief:** Pest. Dat is een laagje bovenop PHPUnit met een kortere schrijfwijze, maar het voegt voor dit project niets toe.

## 12. Codestijl: PHP_CodeSniffer met PSR-12

**Gekozen:** PHP_CodeSniffer met de standaard PSR-12. `phpcs` controleert de code, `phpcbf` verbetert de opmaak automatisch.

**Waarom:** PSR-12 is de gangbare stijl voor PHP. Eén tool doet zowel de controle als de automatische opmaak.

**Alternatief:** PHP-CS-Fixer. Die doet ongeveer hetzelfde, maar heeft meer instellingen nodig om tot dezelfde regels te komen.

## 13. Taal in code en documentatie

**Gekozen:** namen in de code (klassen, functies, variabelen) zijn Engels. Teksten voor de gebruiker, de documentatie en de commit messages zijn Nederlands.

**Waarom:** Engels is gebruikelijk in code en past bij PHP zelf en bij de documentatie die ik online vind. De gebruikers en de beoordelaars zijn Nederlands, dus alles wat zij lezen is Nederlands. Commit messages zijn altijd Nederlands, zodat de geschiedenis één taal heeft.

## 14. Versiebeheer

**Gekozen:** git met een branch per onderdeel. Een branch gaat pas naar `main` als de feature werkt en alle tests slagen. Ik merge met `--no-ff`, zodat in de geschiedenis te zien blijft welke commits bij welke feature horen. Commit messages volgen Conventional Commits (`feat:`, `fix:`, `test:`, `docs:`, `refactor:`, `chore:`) en verwijzen naar het issue.

**Waarom:** `main` werkt altijd. In de geschiedenis is per feature terug te zien wat ik gedaan heb, en via het issuenummer bij welke eis het hoort.

**Alternatief:** alles direct op `main` committen. Sneller, maar dan staat er soms halve code op `main` en is per feature minder goed te zien wat erbij hoort.

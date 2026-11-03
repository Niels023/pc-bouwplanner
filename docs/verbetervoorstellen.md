# Verbetervoorstellen

In dit document staat wat er aan de PC-bouwplanner beter kan, waar dat uit blijkt, hoe belangrijk het is en in welke volgorde ik het zou doen.

## 1. Bronnen

Ik heb deze bronnen doorgelopen op 3 november 2026:

| Bron | Wat ik eruit gehaald heb |
|---|---|
| [testrapport.md](testrapport.md), de bevindingen per testrun | Plekken waar de site werkt volgens de tests, maar de uitkomst niet goed is |
| [testrapport.md](testrapport.md), "Nog niet getest" | Wat nog niet bewezen is |
| [data-controle.md](data-controle.md) | Waar de data een aanname of een vereenvoudiging is |
| [keuzes.md](keuzes.md) | Bewuste vereenvoudigingen met een nadeel |
| De mutatietests | Of de tests sterk genoeg zijn. Alle ingebouwde fouten worden nu gevangen, dus daar komt geen voorstel uit |
| Gebruikerstest en feedback van de docent | Nog niet gedaan. Wat daaruit komt, voeg ik hier toe |

## 2. Voorstellen

Prioriteit: **hoog** betekent dat de site nu een verkeerd advies kan geven of iets niet bewezen is, **middel** dat het advies beter kan, **laag** dat het vooral gemak is. Omvang: **klein** is een paar uur werk, **middel** een werkdag en **groot** meerdere werkdagen.

| ID | Voorstel | Waar blijkt het uit | Prioriteit | Omvang |
|---|---|---|---|---|
| V-01 | Testen in Firefox, Safari en op een echte telefoon | Testrapport, "Nog niet getest": alleen Chromium-browsers en de telefoonweergave van de browser zijn gebruikt | Hoog | Klein |
| V-02 | Koeler kiezen op koelvermogen en niet alleen op prijs | Testrapport aanbeveling: bij € 2.000 krijgt de Core i5-14600K (181 W) de low-profile NH-L12S, omdat die het duurst is binnen het deelbudget | Hoog | Middel |
| V-03 | Radiator voorin laten meetellen bij de ruimte voor de videokaart | Data-controle §9: een radiator voorin kost ruimte. Alleen bij de North weet de site dat, via een veilige maat | Middel | Middel |
| V-04 | De grens van 30 FPS voor lichte games toetsen | Keuzes §9: dit is een aanname van mij, niet gemeten | Middel | Klein |
| V-05 | Opgeslagen builds bewaren bij `composer seed` | Testrapport could haves: na een wijziging in het schema waren alle opgeslagen builds weg | Middel | Klein |
| V-06 | FPS laten afhangen van de processor | Data-controle §7: de FPS komt uit één test met een snelle testcomputer. Met een goedkope processor kan het minder zijn, vooral op Full HD | Middel | Groot |
| V-07 | Prijzen makkelijker bijwerken | Data-controle §1: prijzen veranderen snel en worden nu met de hand in een PHP-bestand aangepast | Laag | Middel |

### V-01 Testen in Firefox, Safari en op een echte telefoon

**Werkzaamheden:** de handmatige tests TC-H-01 t/m TC-H-06 en TC-H-12 doen in Firefox, in Safari op een iPhone of Mac, en op een Android-telefoon. Fouten opschrijven in het testrapport en oplossen.

**Waarom eerst:** het is weinig werk en tot die tijd weet ik niet zeker of NF-01 en NF-02 overal gehaald worden. De site gebruikt `:has()` in de CSS. Werkt dat in een browser niet, dan valt alleen de paarse rand om een keuze weg, maar dat wil ik zien.

### V-02 Koeler kiezen op koelvermogen

**Werkzaamheden:** per koeler opzoeken tot hoeveel watt de fabrikant hem geschikt vindt, een kolom toevoegen aan `coolers`, en een regel maken die een waarschuwing geeft als het verbruik van de processor hoger is. De aanbeveling neemt die regel dan vanzelf mee, omdat ze dezelfde checker gebruikt.

**Let op:** fabrikanten gebruiken verschillende maten. Noctua geeft een NSPR-getal, ARCTIC een eigen index. Waar geen wattage staat, moet ik de veilige kant kiezen.

### V-03 Radiator voorin en de videokaart

**Werkzaamheden:** per kast opzoeken hoeveel ruimte voor de videokaart overblijft met een radiator voorin, en per kast opslaan of een radiator voorin of bovenin komt. De lengteregel van de videokaart houdt daar dan rekening mee.

### V-04 De grens voor lichte games toetsen

**Werkzaamheden:** in de gebruikerstest vragen aan iemand die Fortnite of Valorant speelt hoe vloeiend dat loopt op een RTX 5060 op 1440p, of benchmarks van één lichte game opzoeken. Daarna de waarde `min_fps` in `config/app.php` aanpassen als dat nodig is. Code hoeft niet te veranderen.

### V-05 Opgeslagen builds bewaren bij seed

**Werkzaamheden:** `schema.sql` splitsen in de tabellen met onderdelen, die opnieuw gemaakt mogen worden, en de tabellen `builds` en `build_parts`, die blijven staan. Een opgeslagen build met een onderdeel dat niet meer bestaat, krijgt dan een melding.

### V-06 FPS die afhangt van de processor

**Werkzaamheden:** een bron zoeken die dezelfde videokaarten met verschillende processors test, en per processor een factor vastleggen. Dit is veel data-onderzoek en het is lastig om een betrouwbare bron te vinden. Daarom staat het later in de planning.

### V-07 Prijzen makkelijker bijwerken

**Werkzaamheden:** een beheerpagina waar je per onderdeel de prijs en de peildatum aanpast, afgeschermd met een wachtwoord. Automatisch prijzen ophalen van websites doe ik bewust niet, omdat dat scraping is.

## 3. Planning

Er is geen deadline, dus de planning is een volgorde in twee sprints. Elke sprint eindigt met alle tests groen, een merge naar `main` en een bijgewerkt testrapport.

| Sprint | Voorstellen | Waarom deze volgorde |
|---|---|---|
| Sprint 2 | V-01, V-04, V-05 | Klein werk dat laat zien of de site echt werkt zoals de tests zeggen. V-04 hoort bij de gebruikerstest, die ik toch nog moet doen |
| Sprint 3 | V-02, V-03 | De twee voorstellen die een verkeerd advies van de aanbeveling of de controle voorkomen. Eerst data opzoeken, dan de regel bouwen met tests |
| Later | V-06, V-07 | Groot of vooral gemak. Pas als de rest af is |

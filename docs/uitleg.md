# Uitleg van de code

Hier leg ik in gewone taal uit hoe de PC-bouwplanner in elkaar zit. Dit document groeit mee met de code. Het is mijn voorbereiding op vragen bij de demo.

## Mappen

| Map | Wat staat erin |
|---|---|
| `config/` | Instellingen, zoals de databasegegevens en de marge van de voeding |
| `database/` | `schema.sql` (de tabellen), `seed.php` (vult de database) en `data/` (de onderdelen zelf) |
| `docs/` | Alle documentatie |
| `src/Compatibility/` | De controle of onderdelen bij elkaar passen |
| `src/Database/` | Verbinding met de database en de `Seeder` |
| `src/Model/` | Klassen voor de onderdelen en de build |
| `tests/` | Alle tests. `Unit` draait zonder database, `Database` met een aparte testdatabase |
| `vendor/` | Packages van Composer. Staat niet in git, je maakt het met `composer install` |

`bootstrap.php` laadt de autoloader van Composer en de instellingen uit `.env`. Elk script begint daarmee.

## De database vullen

`composer seed` start `database/seed.php`. Dat doet dit:

1. De database aanmaken als die nog niet bestaat.
2. `schema.sql` uitvoeren. Dat gooit alle tabellen weg en maakt ze opnieuw.
3. De `Seeder` zet eerst de sockets, RAM-types en formaten in hun tabellen en onthoudt de id's.
4. Daarna zet de `Seeder` elk onderdeel in twee tabellen: de algemene gegevens in `parts` en de specificaties in de tabel van de categorie, bijvoorbeeld `cpus`. Een waarde als `'AM5'` wordt daarbij het id uit de tabel `sockets`.

## Het model

Elk onderdeel is een object. De klasse `Part` heeft wat elk onderdeel heeft: merk, naam, prijs en verbruik. `Cpu`, `Gpu`, `Psu` en de andere klassen erven daarvan en voegen hun eigen velden toe.

- **`abstract`:** van `Part` zelf kun je geen object maken. Dat klopt, want er bestaat geen "onderdeel" dat geen processor, moederbord of iets anders is.
- **`readonly`:** de waarden kunnen na het maken niet meer veranderen. Een processor krijgt nooit ineens een andere socket, en dat voorkomt fouten.
- **`enum Category`:** een vaste lijst met de acht categorieën. Zo kan ik geen tikfout maken in een categorie.
- **Prijs in centen:** `priceCents` is een `int`. Met kommagetallen kun je afrondingsfouten krijgen, met hele centen niet.

Een `Build` is een verzameling onderdelen, maximaal één per categorie. Hij kan de totaalprijs en het verbruik uitrekenen. Met `$build->cpu()` haal je de processor op. Dat mag alleen als er een processor gekozen is.

## De compatibiliteitscontrole

Dit is het belangrijkste deel van de app. Het staat in `src/Compatibility/` en weet niets van de database of de website. Daardoor is het makkelijk te testen.

Elke regel is een eigen klasse in `Rules/` en implementeert de interface `CompatibilityRule`. Een interface is een afspraak: elke regel moet deze drie methodes hebben.

| Methode | Wat doet het | Voorbeeld bij `SocketRule` |
|---|---|---|
| `name()` | De naam van de regel | `'socket'` |
| `requiredCategories()` | Welke onderdelen de regel nodig heeft | processor en moederbord |
| `evaluate($build)` | De controle zelf, geeft een `RuleResult` terug | vergelijkt de twee sockets |

Een `RuleResult` bevat de naam van de regel, een status (`Ok`, `Warning`, `Error` of `NotChecked`) en een melding in het Nederlands.

### Wat gebeurt er bij een controle?

De `CompatibilityChecker` heeft een lijst met alle regels. Bij `check($build)` doet hij dit voor elke regel:

1. Kijken of de onderdelen uit `requiredCategories()` gekozen zijn.
2. Ontbreekt er iets, dan maakt de checker zelf een resultaat `NotChecked` met de melding "Kies eerst een moederbord."
3. Is alles er, dan roept hij `evaluate()` van de regel aan.

Alle resultaten komen samen met de totaalprijs en het verbruik in een `CheckReport`. `overallStatus()` bepaalt daaruit de totaalstatus: eerst kijken of er een fout is, dan een waarschuwing, dan of de build nog niet alle acht onderdelen heeft, en anders is alles in orde. Ook zonder opslag is een build dus nog niet compleet, al heeft geen enkele regel opslag nodig.

### De voedingsregel

Dit is de enige regel met drie uitkomsten. Stel de onderdelen verbruiken samen 450 W en de marge is 30%:

```text
nodig = 450 x 130 / 100 = 585 W
```

| Voeding | Uitkomst | Waarom |
|---|---|---|
| 449 W of minder | Fout | Minder dan het verbruik |
| 450 tot en met 584 W | Waarschuwing | Genoeg, maar te weinig marge |
| 585 W of meer | In orde | Genoeg met marge |

Het resultaat rond ik met `ceil` naar boven af. Bij 583 W is 757,9 W nodig, en een voeding van 757 W is dan net te weinig.

De regel heeft ook een processor nodig. Anders is het verbruik bijna 0 en lijkt elke voeding goed.

## Vragen die ik kan verwachten

**Waarom is elke regel een eigen klasse?** Elke regel is klein en apart te testen. Een nieuwe regel toevoegen is één nieuwe klasse maken en die aan de lijst in `withAllRules()` toevoegen. De rest van de code hoeft niet te veranderen.

**Waarom controleert de checker of onderdelen ontbreken, en niet de regels zelf?** Dan zou die controle zes keer in de code staan. Nu staat hij op één plek.

**Waarom staat de marge niet in de code van de regel?** De marge is een instelling. Hij staat in `config/app.php` en wordt via de constructor meegegeven aan `PsuWattageRule`. In de test kan ik zo ook een andere marge gebruiken.

**Hoe weet je dat je tests goed zijn?** Ik test precies op de grenzen, bijvoorbeeld een kaart van 355 mm en 356 mm in een kast van 355 mm. Ik heb ook expres 12 fouten in de code gezet, en de tests vingen ze allemaal.

**Waarom heet de klasse `PcCase` en niet `Case`?** `case` is een gereserveerd woord in PHP.

**Waarom is `Build::cpu()` niet nullable?** De checker controleert eerst met `hasPart()` of het onderdeel er is. Roept iemand `cpu()` aan zonder processor, dan is dat een fout in de code en is het goed dat PHP een foutmelding geeft.

# Testrapport

In dit rapport staan alleen resultaten van tests die echt gedraaid zijn. De testcases staan in [testplan.md](testplan.md).

Eisen die nog niet gebouwd zijn, staan hier nog niet in.

## Testrun 30 oktober 2026

**Omgeving:** Windows 11, PHP 8.2.12, MariaDB 10.4.32 (XAMPP), PHPUnit 11.5.57, PHP_CodeSniffer 4.0.4. Branch `feature/compatibiliteit`.

**Resultaat `composer test`:**

```text
OK (42 tests, 97 assertions)
```

**Resultaat `composer lint`:** 0 fouten en 0 waarschuwingen.

| Testsuite | Tests | Geslaagd | Mislukt |
|---|---|---|---|
| unit | 38 | 38 | 0 |
| database | 4 | 4 | 0 |

### Resultaten per eis

| Eis | Testcases | Geslaagd | Conclusie |
|---|---|---|---|
| F-01 Database met onderdelen | 4 | 4 | Voldoet. De database bevat 51 onderdelen in 8 categorieën, met minimaal 5 per categorie. |
| F-05 Controle socket | 2 | 2 | Voldoet. |
| F-06 Controle RAM-type | 2 | 2 | Voldoet. |
| F-07 Controle formaat | 3 | 3 | Voldoet, ook bij hetzelfde formaat. |
| F-08 Controle voeding | 8 | 8 | Voldoet, ook precies op de grenzen. |
| F-09 Controle lengte videokaart | 3 | 3 | Voldoet, ook bij precies even lang. |
| F-10 Controle hoogte koeler | 3 | 3 | Voldoet, ook bij precies even hoog. |
| F-11 Resultaat van de controle | 11 | 11 | Voldoet op het niveau van de module. De melding is nog niet op een pagina te zien. |
| F-13 Totaalprijs en verbruik | 6 | 6 | De berekening klopt. De eis is nog niet af: de totalen moeten nog op de pagina's komen. |

De tests voor F-01 tot en met F-13 zijn unit tests en databasetests. De regels zijn dus nog niet via de website of de API getest.

### Mutatietest

Op 30 oktober 2026 heb ik 12 fouten met opzet in de code gezet en steeds de unit tests gedraaid. Daarna is de code teruggezet.

| Ingebouwde fout | Gevangen door de tests |
|---|---|
| Voeding: `$wattage < $needed` wordt `<=` | Ja |
| Voeding: `$wattage < $usage` wordt `<=` | Ja |
| Voeding: `ceil` wordt `floor` | Ja |
| Voeding: regel heeft geen processor meer nodig | Ja |
| Videokaart: `>` wordt `>=` | Ja |
| Videokaart: 1 mm extra speling | Ja |
| Koeler: `>` wordt `>=` | Ja |
| Koeler: 1 mm extra speling | Ja |
| Formaat: `<=` wordt `<` | Ja |
| Socket: regel geeft nooit een fout | Ja |
| RAM-type: regel geeft nooit een fout | Ja |
| Totaalstatus: waarschuwing wordt fout | Ja |

**Conclusie:** alle 12 fouten zijn gevangen. De tests controleren de grenzen dus echt.

### Bevindingen

- Een build zonder opslag kon de totaalstatus "in orde" krijgen, omdat geen enkele regel opslag nodig heeft. Dit klopte met het toenmalige acceptatiecriterium van F-11, maar is niet wat een gebruiker verwacht. Opgelost op 30 oktober: F-11 is aangepast en een build is nu pas "in orde" met alle acht onderdelen.

## Testrun 30 oktober 2026, REST API

**Omgeving:** gelijk aan de vorige run. Branch `feature/rest-api`.

**Resultaat `composer test`:**

```text
OK (105 tests, 341 assertions)
```

**Resultaat `composer lint`:** 0 fouten en 0 waarschuwingen.

| Testsuite | Tests | Geslaagd | Mislukt |
|---|---|---|---|
| unit | 44 | 44 | 0 |
| database | 15 | 15 | 0 |
| api | 46 | 46 | 0 |

### Resultaten per eis

| Eis | Conclusie |
|---|---|
| F-02 Onderdelen bekijken | De API en de database voldoen. De kaartjes op de pagina moeten nog. |
| F-03 Onderdelen filteren | De API voldoet, ook bij combinaties, grenswaarden en foute invoer. De filters op de pagina moeten nog. |
| F-11 Resultaat van de controle | Voldoet, ook via de API. Een build is pas "in orde" met alle acht onderdelen. |
| F-14 Build opslaan | De API voldoet, ook bij namen van precies 60 en 61 tekens. Het formulier moet nog. |
| F-15 Build ophalen | De API voldoet. De pagina moet nog. |
| F-18 REST API | Voldoet voor onderdelen en builds. Het endpoint voor de aanbeveling moet nog. |
| NF-03 Veilig | Query's en invoercontrole voldoen. SQL-injectie via een filter heeft geen effect. Escapen op de pagina's moet nog getest worden. |

### Handmatige test met curl

Op 30 oktober 2026 heb ik de server gestart en de API getest met curl. Getest: filteren, onbekende categorie (422), onbekend pad (404), verkeerde methode (405), kapotte JSON (400), controleren, opslaan (201) en ophalen met de code (200). Na het vullen van de database met `composer seed` gaf alles het verwachte antwoord. Ook `composer start` is getest en geeft een 200 op `/api/parts`.

### Bevindingen

- Bij de eerste handmatige test gaf `/api/parts` een 500, terwijl alle PHPUnit-tests slaagden. Oorzaak: mijn eigen database was gevuld voor de view `part_details` bestond. De testdatabase wordt bij elke testrun opnieuw gemaakt, dus daar viel het niet op. Opgelost met `composer seed`, en de README zegt nu dat je dit moet doen als het schema verandert. Dit laat zien dat handmatig testen naast de automatische tests nuttig is.
- De foutafhandeling werkte daarbij goed: de gebruiker kreeg een algemene melding en de echte fout stond in de log.
- `composer seed` maakt alle tabellen opnieuw, dus opgeslagen builds zijn daarna weg. Dat is geen probleem zolang de site lokaal draait, maar het staat nu wel in de README.

## Nog niet getest

- De aanbeveling (F-16, F-17): die bestaat nog niet.
- Handmatige tests en gebruikerstests: de pagina's bestaan nog niet.

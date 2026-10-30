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

- Een build zonder opslag kan de totaalstatus "in orde" krijgen, omdat geen enkele regel opslag nodig heeft. Dit klopt met het acceptatiecriterium van F-11, maar is misschien niet wat een gebruiker verwacht. Dit staat als open punt in [voortgang.md](voortgang.md).

## Nog niet getest

- API-tests: de API bestaat nog niet.
- Handmatige tests en gebruikerstests: de pagina's bestaan nog niet.

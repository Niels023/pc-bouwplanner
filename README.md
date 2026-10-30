# PC-bouwplanner

Een website waarop je zelf een PC samenstelt. Je kiest per stap een onderdeel (processor, moederbord, geheugen, videokaart, koeler, kast, opslag en voeding). De site controleert of de onderdelen bij elkaar passen, telt de prijs en het geschatte stroomverbruik op en kan op basis van je budget en doel (gamen, werken of streamen) zelf een build voorstellen.

De basisversie is af: je kunt een PC samenstellen, je build opslaan en een aanbeveling vragen. De site werkt op telefoon en desktop, ook zonder JavaScript.

## Techniek

- PHP 8.2 met PDO
- MySQL (MariaDB via XAMPP)
- HTML, CSS en een klein beetje JavaScript
- PHPUnit voor tests, PHP_CodeSniffer (PSR-12) voor codestijl

## Installeren

Je hebt nodig: PHP 8.2 of hoger, MySQL of MariaDB en [Composer](https://getcomposer.org/). Met XAMPP heb je PHP en MariaDB al.

1. Clone de repository en installeer de packages:

   ```bash
   git clone https://github.com/Niels023/pc-bouwplanner.git
   cd pc-bouwplanner
   composer install
   ```

2. Kopieer `.env.example` naar `.env` en vul je databasegegevens in. Bij een standaard XAMPP-installatie is de gebruiker `root` zonder wachtwoord en hoef je niets te veranderen.

3. Start MySQL (in XAMPP: Start bij MySQL) en vul de database:

   ```bash
   composer seed
   ```

   Dit maakt de database `pc_bouwplanner` aan als die nog niet bestaat, maakt alle tabellen opnieuw en vult ze met de onderdelen uit `database/data/`. Draai dit ook als `database/schema.sql` veranderd is. Let op: opgeslagen builds zijn daarna weg.

## Starten

```bash
composer start
```

De site draait dan op http://localhost:8000. Naast de website is er een REST API:

| Methode | Pad | Wat |
|---|---|---|
| GET | `/api/parts` | Onderdelen ophalen. Filters: `category`, `brand`, `max_price`, `socket`, `ram_type`, `form_factor` |
| GET | `/api/parts/{id}` | Eén onderdeel ophalen |
| POST | `/api/builds/check` | Onderdelen controleren, body: `{"part_ids": [1, 2]}` |
| POST | `/api/builds` | Build opslaan, body: `{"name": "Mijn pc", "part_ids": [1, 2]}` |
| GET | `/api/builds/{code}` | Opgeslagen build ophalen |
| GET | `/api/recommendation` | Aanbeveling, bijvoorbeeld `?budget=2000&goal=gaming`. Doelen: `gaming`, `work`, `streaming` |

## Tests en codestijl

```bash
composer test
composer lint
```

`composer test` draait alle PHPUnit-tests: unit tests, databasetests, API-tests en paginatests. De tests met een database gebruiken een aparte database, `pc_bouwplanner_test`, zodat je eigen data blijft staan. MySQL moet daarvoor wel draaien. Alleen de unit tests draaien kan zonder MySQL:

```bash
vendor/bin/phpunit --testsuite unit
```

`composer lint` controleert de code op PSR-12. Met `composer fix` worden de meeste stijlfouten automatisch verbeterd.

## Documentatie

De projectdocumentatie staat in de map [docs](docs/): opdracht, eisen, ontwerp, keuzes, testplan, testrapport, voortgang en een uitleg van de code. In [docs/data-controle.md](docs/data-controle.md) staan de bronnen van de onderdelendata.

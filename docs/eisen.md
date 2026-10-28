# Eisen en wensen

De eisen zijn ingedeeld volgens MoSCoW:

- **Must have**: zonder deze eisen werkt de basisversie niet.
- **Should have**: belangrijk, maar de basisversie werkt ook zonder.
- **Could have**: leuk als er tijd over is.
- **Won't have (deze keer)**: bewust niet in dit project, zie ook [opdracht.md](opdracht.md).

Functionele eisen hebben een ID die begint met F, niet-functionele eisen een ID die begint met NF. Elke eis heeft een acceptatiecriterium: als dat klopt, is de eis af. In het [testplan](testplan.md) hoort bij elk ID minstens één testcase.

## Functionele eisen

### Must have

| ID | Eis | Acceptatiecriterium |
|---|---|---|
| F-01 | Database met bestaande onderdelen | Na het draaien van het seed-script staan er 40 tot 60 bestaande onderdelen in de database, met minimaal 4 per categorie. Elk onderdeel heeft een merk, naam, richtprijs (hoger dan 0) en peildatum. Specificaties waar ik niet zeker van ben, staan in een lijst om na te kijken. |
| F-02 | Onderdelen per categorie bekijken | Per categorie zie ik alle onderdelen als kaartjes met merk, naam, de belangrijkste specificaties, richtprijs en peildatum. |
| F-03 | Onderdelen filteren | Ik kan filteren op merk, maximale prijs en een specificatie die bij de categorie hoort (socket, RAM-type of formaat). Filters zijn te combineren. Ik zie alleen onderdelen die aan alle filters voldoen. Zijn er geen resultaten, dan zie ik de melding "Geen onderdelen gevonden met deze filters". |
| F-04 | Build stap voor stap samenstellen | Ik doorloop acht stappen in een vaste volgorde: processor, moederbord, geheugen, videokaart, koeler, kast, opslag, voeding. Per stap kies ik één onderdeel, of ik wijzig of verwijder mijn keuze. Ik kan terug naar een eerdere stap zonder dat ik mijn andere keuzes kwijtraak. |
| F-05 | Controle socket processor en moederbord | Hebben de processor en het moederbord een verschillende socket, dan geeft de controle een fout met beide sockets in de melding, bijvoorbeeld "De processor heeft socket AM5, maar het moederbord heeft socket AM4". Bij dezelfde socket is de regel in orde. |
| F-06 | Controle RAM-type | Past het geheugentype (DDR4 of DDR5) niet bij het moederbord, dan geeft de controle een fout met beide types in de melding. Bij hetzelfde type is de regel in orde. |
| F-07 | Controle formaat moederbord en kast | Is het moederbord groter dan het grootste formaat dat de kast ondersteunt, dan geeft de controle een fout. Voorbeeld: een ATX-moederbord in een Mini-ITX-kast geeft een fout, een Micro-ATX-moederbord in een ATX-kast is in orde. |
| F-08 | Controle voeding tegenover geschat verbruik | Het geschatte verbruik is de som van het verbruik van alle gekozen onderdelen. Is het vermogen van de voeding minstens het verbruik plus 30% marge, dan is de regel in orde. Is het vermogen wel genoeg voor het verbruik maar niet voor de marge, dan volgt een waarschuwing. Is het vermogen lager dan het verbruik, dan volgt een fout. Precies verbruik plus 30% is in orde. De marge staat in de configuratie. |
| F-09 | Controle lengte videokaart in kast | Is de videokaart langer dan de maximale lengte die in de kast past, dan geeft de controle een fout met beide maten in millimeters. Precies even lang is in orde. |
| F-10 | Controle hoogte koeler in kast | Is de koeler hoger dan de maximale koelerhoogte van de kast, dan geeft de controle een fout met beide maten in millimeters. Precies even hoog is in orde. |
| F-11 | Duidelijk resultaat van de controle | Elke regel geeft een status (in orde, waarschuwing, fout of nog niet gecontroleerd) en een melding in begrijpelijk Nederlands. Ontbreekt een onderdeel dat een regel nodig heeft, dan is de status "nog niet gecontroleerd" en noemt de melding welk onderdeel ik nog moet kiezen. De build krijgt een totaalstatus: fout als één regel fout is, anders waarschuwing als één regel een waarschuwing geeft, anders nog niet compleet als een regel nog niet gecontroleerd is, anders in orde. |
| F-12 | Onverenigbare onderdelen herkennen bij het kiezen | Onderdelen die een fout geven met mijn eerdere keuzes zijn grijs en tonen in tekst waarom ze niet passen. Ze blijven zichtbaar en te kiezen, zodat ik daarna een eerdere keuze kan aanpassen. |
| F-13 | Totaalprijs en geschat verbruik | Bij elke stap zie ik de totaalprijs (som van de richtprijzen) en het geschatte verbruik in watt. Beide kloppen direct na het kiezen of verwijderen van een onderdeel. |
| F-14 | Build opslaan | Ik geef de build een naam van 1 tot 60 tekens en sla hem op. Ik krijg een unieke deelcode van 8 tekens en een link. Een build zonder onderdelen of zonder naam kan ik niet opslaan en ik zie waarom. |
| F-15 | Opgeslagen build ophalen | Via de link of door de deelcode in te vullen zie ik de build met onderdelen, totaalprijs, verbruik en controleresultaat. Bij een onbekende code zie ik "Geen build gevonden met deze code". |
| F-16 | Aanbeveling op basis van budget en doel | Ik vul een budget in hele euro's in en kies gamen, werken of streamen. Ik krijg een complete build die volledig compatibel is en waarvan de totaalprijs niet hoger is dan mijn budget. Is het budget leeg, 0, negatief of geen getal, dan zie ik een melding. Is het budget lager dan de goedkoopste complete build, dan zie ik het minimale bedrag dat nodig is. |
| F-17 | Uitleg bij de aanbeveling | Bij elk aanbevolen onderdeel staat in één zin waarom het gekozen is, met het deelbudget voor die categorie. Ik zie ook hoeveel van mijn budget over is. |
| F-18 | REST API | De API geeft JSON terug en heeft deze endpoints: onderdelen ophalen en filteren, één onderdeel ophalen, build opslaan, build ophalen, build controleren en aanbeveling opvragen. Bij ongeldige invoer geeft de API de juiste statuscode (400, 404 of 422) en een foutmelding met een code en een uitleg in het Nederlands. |

### Should have

| ID | Eis | Acceptatiecriterium |
|---|---|---|
| F-19 | Aanbeveling overnemen en aanpassen | Met één knop zet ik de aanbevolen build in de bouwplanner. Daarna kan ik elk onderdeel wijzigen zoals bij F-04. |
| F-20 | Onderdelen sorteren | Ik kan de onderdelen in een stap sorteren op prijs (laag naar hoog en hoog naar laag) en op naam. |
| F-21 | Controle beeld zonder videokaart | Kies ik geen videokaart en heeft de processor geen ingebouwde graphics, dan geeft de controle een fout: de PC geeft dan geen beeld. Met ingebouwde graphics is een build zonder videokaart in orde. |

### Could have

| ID | Eis | Acceptatiecriterium |
|---|---|---|
| F-22 | Builds vergelijken | Ik kan twee opgeslagen builds naast elkaar zien, met per categorie het onderdeel en het verschil in prijs en verbruik. |
| F-23 | Printvriendelijk overzicht | Een build heeft een overzichtspagina die netjes op één A4 afdrukt, zonder knoppen en filters. |
| F-24 | Controle koeler op socket | Ondersteunt de koeler de socket van de processor niet, dan geeft de controle een fout. |
| F-25 | Waterkoeling (AIO) | Ik kan ook een AIO-waterkoeler kiezen. De controle kijkt dan of de radiatormaat in de kast past in plaats van naar de koelerhoogte. |

### Won't have (deze keer)

| ID | Eis | Waarom niet |
|---|---|---|
| F-26 | Inloggen en accounts | Niet nodig om builds te bewaren, de deelcode is genoeg. Accounts brengen veel extra werk en beveiliging mee. |
| F-27 | Actuele prijzen van webshops | Scrapen mag niet en echte koppelingen met webshops vallen buiten dit project. |
| F-28 | Bestellen en afrekenen | De site is een planner, geen webshop. |
| F-29 | Aanbeveling met AI | De aanbeveling moet uitlegbaar zijn. Vaste regels kan ik uitleggen en testen. |
| F-30 | Beheerscherm voor onderdelen | De onderdelen komen uit een seed-script. Een beheerscherm met inloggen is te veel voor dit project. |

## Niet-functionele eisen

| ID | Prioriteit | Eis | Acceptatiecriterium |
|---|---|---|---|
| NF-01 | Must | Werkt op telefoon en desktop | Alle pagina's zijn te gebruiken op schermen van 360 tot 1920 pixels breed, zonder horizontaal scrollen. Op een telefoon staan de kaartjes onder elkaar, op een desktop naast elkaar. |
| NF-02 | Must | Toegankelijk | Tekst heeft een contrast van minimaal 4,5:1 (WCAG 2.1 niveau AA). Elk invoerveld heeft een label. Alles werkt met alleen een toetsenbord en de focus is altijd zichtbaar. Een status is nooit alleen met kleur aangegeven, er staat ook tekst bij. Lighthouse geeft voor toegankelijkheid een score van 90 of hoger. |
| NF-03 | Must | Veilig | Alle databasequery's gebruiken prepared statements. Alle tekst die op een pagina komt, wordt ge-escaped. Alle invoer wordt op de server gecontroleerd. Er staan geen wachtwoorden of sleutels in de repository. |
| NF-04 | Must | Instelbaar zonder code aan te passen | De databasegegevens staan in een `.env`-bestand. De voedingsmarge en de budgetverdeling per doel staan in een configuratiebestand. Er staat een `.env.example` zonder echte wachtwoorden in de repository. |
| NF-05 | Must | Nette code | PHP_CodeSniffer geeft 0 fouten volgens PSR-12. Functies zijn kort en hebben duidelijke namen. Er is geen dubbele of ongebruikte code. |
| NF-06 | Must | Getest | Elke compatibiliteitsregel en de aanbeveling hebben unit tests, ook voor randgevallen zoals precies genoeg vermogen, een ontbrekend onderdeel en een leeg budget. Alle tests slagen voordat een branch naar main gaat. |
| NF-07 | Should | Werkt zonder JavaScript | Met JavaScript uit werken alle Must-functies nog. JavaScript zorgt er alleen voor dat de totalen bijwerken zonder dat de pagina herlaadt. |
| NF-08 | Should | Snel | Elke pagina en elk API-verzoek geeft lokaal binnen 1 seconde antwoord. |
| NF-09 | Should | Makkelijk te installeren | Iemand anders kan de site installeren en starten met alleen de stappen uit de README. |

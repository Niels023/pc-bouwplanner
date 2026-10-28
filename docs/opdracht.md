# Opdrachtomschrijving

## Aanleiding

Wie voor het eerst zelf een PC bouwt, moet uit honderden onderdelen kiezen. Niet alles past bij elkaar. Een processor past alleen op een moederbord met dezelfde socket, een groot moederbord past niet in een kleine kast en een te zwakke voeding kan de PC niet van stroom voorzien. Die fouten merk je vaak pas als de onderdelen al gekocht zijn. Daarnaast is het voor een beginner lastig om in te schatten hoe je een budget het beste verdeelt.

## Opdracht

Ik maak een website, de PC-bouwplanner, waarmee een gebruiker stap voor stap een PC samenstelt. De website:

1. laat per categorie bestaande onderdelen zien, met filters;
2. controleert of de gekozen onderdelen compatibel zijn en legt uit waarom iets niet past;
3. telt de totaalprijs en het geschatte stroomverbruik op;
4. slaat een build op zodat de gebruiker hem later terug kan halen;
5. stelt zelf een build voor op basis van budget en doel (gamen, werken of streamen) en legt elke keuze uit.

De logica zit in een REST API, zodat de controle en de aanbeveling ook los van de website te gebruiken en te testen zijn.

## Doelgroep

- Mensen die voor het eerst zelf een PC willen samenstellen en weinig weten van de techniek, zoals scholieren en studenten.
- Mensen die al wat weten, maar snel willen controleren of hun lijstje onderdelen klopt.

De doelgroep gebruikt de site zowel op een laptop als op een telefoon. De uitleg bij meldingen moet daarom kort en begrijpelijk zijn, zonder vakjargon waar dat kan.

## Doelen

1. Een gebruiker kan zonder hulp een complete build van acht onderdelen samenstellen.
2. De site vindt elke onverenigbaarheid uit de zes controleregels en geeft per regel een begrijpelijke melding.
3. De totaalprijs en het geschatte verbruik kloppen altijd met de gekozen onderdelen.
4. Een aanbevolen build valt binnen het opgegeven budget en is volledig compatibel.
5. De site is goed te gebruiken op telefoon en desktop en met alleen een toetsenbord.

## Scope

Binnen de scope:

- Een database met 40 tot 60 bestaande onderdelen in acht categorieën: processor (CPU), moederbord, geheugen (RAM), videokaart (GPU), koeler, kast, opslag en voeding. Elk onderdeel heeft een richtprijs met peildatum.
- Zes compatibiliteitsregels: socket van CPU en moederbord, RAM-type, formaat moederbord tegenover kast, voeding tegenover geschat verbruik met marge, lengte videokaart in kast en hoogte koeler in kast.
- Een build opslaan en ophalen met een deelcode.
- Een aanbeveling op basis van vaste regels voor drie doelen.
- Een REST API met JSON en een website die door PHP op de server wordt opgebouwd.

## Buiten de scope

- Inloggen en gebruikersaccounts. Een opgeslagen build is terug te vinden met de deelcode.
- Actuele prijzen van webshops en het automatisch ophalen (scrapen) van gegevens. De prijzen zijn richtprijzen die ik zelf invoer.
- Bestellen of afrekenen.
- Een aanbeveling met AI. De aanbeveling werkt met vaste, uitlegbare regels.
- Een beheerscherm om onderdelen toe te voegen of te wijzigen. De onderdelen komen uit een seed-script.
- Voorspellingen van prestaties, zoals FPS in games.
- Meerdere onderdelen in dezelfde categorie, zoals twee SSD's of twee videokaarten.
- Waterkoeling (AIO). De eerste versie gebruikt alleen luchtkoelers.

## Randvoorwaarden

- De website draait lokaal met PHP 8.2 en MySQL (MariaDB via XAMPP).
- De website werkt in de nieuwste versies van Chrome, Firefox, Edge en Safari.
- De broncode staat in een git-repository op GitHub.

## Oplevering

- De werkende website, gedemonstreerd aan de docent.
- De repository met code, tests en documentatie.
- Het verslag op de werkprocessen B1-K1 en B1-K2.

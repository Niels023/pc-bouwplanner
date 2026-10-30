# Bronnen en controle van de onderdelendata

Op 28 oktober 2026 zijn alle onderdelen in `database/data/parts.php` gecontroleerd: of ze nog te koop zijn, wat ze kosten en of de specificaties kloppen. In dit document staat waar de gegevens vandaan komen en welke keuzes ik heb gemaakt als bronnen elkaar tegenspraken.

## 1. Prijzen

Bron: de laagste prijs in de [Tweakers Pricewatch](https://tweakers.net/pricewatch/) op 28 oktober 2026. Bij processors is dat de Boxed-versie. Alle onderdelen hebben in de Pricewatch prijzen van meerdere winkels.

Geheugen, opslag en videokaarten zijn op dit moment veel duurder dan een jaar eerder, door een tekort aan geheugenchips. Een kit van 16 GB DDR5 kost bijvoorbeeld € 314,10. Dat is geen fout in de data.

Prijzen veranderen snel. Werkwijze om ze bij te werken:

1. De prijs opzoeken in de Tweakers Pricewatch.
2. `price_eur` aanpassen in `database/data/parts.php` en `price_date` op de datum van de controle zetten.
3. `composer seed` draaien. Let op: dit maakt alle tabellen opnieuw, dus opgeslagen builds zijn daarna weg.

### Processors

| Onderdeel | Prijs |
|---|---|
| AMD Ryzen 5 5600 Boxed | € 124,96 |
| AMD Ryzen 7 5700X Boxed | € 194,90 |
| AMD Ryzen 5 7600 Boxed | € 176,82 |
| AMD Ryzen 5 9600X Boxed | € 181,00 |
| AMD Ryzen 7 7800X3D Boxed | € 356,84 |
| AMD Ryzen 7 9800X3D Boxed | € 429,00 |
| Intel Core i5-12400F Boxed | € 145,04 |
| Intel Core i5-14600K Boxed | € 260,84 |
| Intel Core Ultra 5 245K Boxed | € 183,00 |

### Moederborden

| Onderdeel | Prijs |
|---|---|
| ASUS TUF Gaming B550-PLUS | € 120,12 |
| ASRock B550M Pro4 | € 95,48 |
| Gigabyte B850M DS3H | € 134,00 |
| MSI B850 GAMING PLUS WIFI | € 169,00 |
| ASUS ROG Strix B850-I GAMING WIFI | € 279,00 |
| Gigabyte B760M DS3H DDR4 | € 99,00 |
| Gigabyte Z790 AORUS ELITE AX | € 206,99 |
| MSI MAG B860 TOMAHAWK WIFI | € 218,51 |

### Geheugen

| Onderdeel | Prijs |
|---|---|
| Kingston FURY Beast 16GB (2x8GB) DDR4-3200 | € 169,00 |
| Kingston FURY Beast 32GB (2x16GB) DDR4-3200 | € 299,00 |
| Kingston FURY Beast 16GB (2x8GB) DDR5-5600 | € 314,10 |
| Corsair Vengeance 32GB (2x16GB) DDR5-6000 CL30 | € 599,00 |
| G.Skill Flare X5 32GB (2x16GB) DDR5-6000 CL30 | € 619,00 |

### Videokaarten

| Onderdeel | Prijs |
|---|---|
| MSI GeForce RTX 5060 8G VENTUS 2X OC | € 499,00 |
| PowerColor Reaper Radeon RX 9060 XT 16GB | € 570,01 |
| MSI GeForce RTX 5060 Ti 16G VENTUS 2X OC PLUS | € 843,90 |
| MSI GeForce RTX 5070 12G VENTUS 2X OC | € 949,00 |
| Sapphire Pulse Radeon RX 9070 XT | € 999,00 |
| MSI GeForce RTX 5080 16G VENTUS 3X OC | € 1.999,00 |
| ASUS ROG Astral GeForce RTX 5080 16GB GDDR7 OC Edition | € 2.229,00 |

### Koelers

| Onderdeel | Prijs |
|---|---|
| Noctua NH-L12S | € 57,95 |
| DeepCool AK400 | € 32,99 |
| Thermalright Peerless Assassin 120 SE | € 43,00 |
| ARCTIC Freezer 36 | € 21,20 |
| DeepCool AK620 | € 64,95 |
| Noctua NH-D15 G2 | € 138,94 |

### Kasten

| Onderdeel | Prijs |
|---|---|
| Cooler Master MasterBox Q300L V2 | € 62,00 |
| Corsair 3000D Airflow Tempered Glass | € 76,00 |
| Lian Li Lancool 216 | € 89,90 |
| Fractal Design North TG Clear Charcoal Black | € 144,90 |
| Cooler Master MasterBox NR200P V3 | € 103,05 |

### Opslag

| Onderdeel | Prijs |
|---|---|
| Kingston NV3 M.2 2280 500GB | € 104,22 |
| Samsung 870 EVO 500GB | € 164,95 |
| Kingston NV3 M.2 2280 1TB | € 149,90 |
| Samsung 870 EVO 1TB | € 249,00 |
| Western Digital WD_BLACK SN850X 1TB | € 199,00 |
| Samsung 990 PRO 2TB | € 339,00 |

### Voedingen

| Onderdeel | Prijs |
|---|---|
| Corsair CX550 (2023) | € 49,95 |
| Corsair RM650e (2025) | € 82,84 |
| Seasonic FOCUS GX ATX 3 (2024) 750W | € 123,75 |
| be quiet! Pure Power 13 M 850W | € 125,10 |
| Corsair RM1000x (2024) | € 186,45 |

## 2. Bronnen van de maten

Voor de controleregels zijn vooral de maten belangrijk. Die heb ik opgezocht bij de fabrikant of, als die niet bereikbaar was, bij meerdere winkels die dezelfde waarde gaven.

| Onderdeel | Waarde in de data | Bron |
|---|---|---|
| MSI GeForce RTX 5060 8G VENTUS 2X OC | 197 mm | Specificaties MSI: 197 x 120 x 41 mm |
| PowerColor Reaper Radeon RX 9060 XT 16GB | 220 mm | PowerColor: 220 mm met bracket, 200 mm zonder |
| MSI GeForce RTX 5060 Ti 16G VENTUS 2X OC PLUS | 227 mm | Winkels (Memory Express, Newegg): 227 x 127 x 41 mm |
| MSI GeForce RTX 5070 12G VENTUS 2X OC | 236 mm | Winkels en distributeurs: 236 x 126 x 50 mm |
| Sapphire Pulse Radeon RX 9070 XT | 320 mm | Sapphire via review PCPer: 320 x 120,25 x 61,6 mm |
| MSI GeForce RTX 5080 16G VENTUS 3X OC | 303 mm | MSI-store: 303 x 121 x 49 mm |
| ASUS ROG Astral GeForce RTX 5080 OC | 358 mm | ASUS: 357,6 x 149,3 x 76 mm |
| Cooler Master MasterBox Q300L V2 | GPU 360 mm, koeler 159 mm | Cooler Master |
| Corsair 3000D Airflow | GPU 360 mm, koeler 170 mm | Corsair-handleiding en winkels |
| Lian Li Lancool 216 | GPU 392 mm, koeler 180 mm | Lian Li via winkels: 392 mm en 180,5 mm |
| Fractal Design North (TG) | GPU 355 mm, koeler 170 mm | Fractal Design via winkels |
| Cooler Master MasterBox NR200P V3 | GPU 361 mm, koeler 70 mm | Winkels: 361,5 mm en 70 mm |
| Noctua NH-L12S | 70 mm | Noctua: 70 mm met de ventilator onder de koelvinnen |
| DeepCool AK400 | 155 mm | DeepCool |
| Thermalright Peerless Assassin 120 SE | 155 mm | Winkels (Galaxus en anderen) |
| ARCTIC Freezer 36 | 159 mm | ARCTIC via winkels: 159 x 126 x 104 mm |
| DeepCool AK620 | 160 mm | DeepCool via winkels: 129 x 138 x 160 mm |
| Noctua NH-D15 G2 | 168 mm | Noctua: 168 mm met ventilatoren |

Sockets, RAM-types, formaten, kernen en kloksnelheden komen uit de productinformatie in de Tweakers Pricewatch en zijn vergeleken met de sites van AMD, Intel en de fabrikanten van de moederborden. Het aantal geheugensloten van de Gigabyte B850M DS3H en B760M DS3H DDR4 (allebei 4) heb ik nagekeken bij Gigabyte en winkels.

## 3. Keuzes als bronnen verschillen

Als een maat niet precies vaststaat, kies ik de veilige kant. Dan zegt de site eerder "past niet" dan "past" terwijl het niet past.

- **Afronden.** De database slaat hele millimeters op. Een videokaart rond ik naar boven af (ROG Astral: 357,6 wordt 358 mm). De ruimte in een kast rond ik naar beneden af (Lancool 216: 180,5 wordt 180 mm, NR200P V3: 361,5 wordt 361 mm).
- **Videokaart met of zonder bracket.** De Reaper RX 9060 XT is 200 mm zonder en 220 mm met bracket. In de kast telt de lengte met bracket, dus 220 mm.
- **Fractal Design North.** De versie met glazen zijpaneel (TG) past koelers tot 170 mm. De Mesh-versie met ventilatorbeugel maar tot 145 mm. In de data staat de TG-versie.
- **Noctua NH-L12S.** 70 mm met de ventilator onder de koelvinnen (zo wordt hij geleverd), 86 mm met de ventilator erboven. In de data staat 70 mm.
- **Cooler Master Q300L V2.** Cooler Master geeft 159 mm voor de koeler, sommige winkels 157 mm. Ik volg de fabrikant.

## 4. Vervangen onderdelen

De eerste versie van de data bevatte onderdelen die niet meer te koop zijn, maar bij een of twee winkels liggen of veel te duur zijn. Die zijn vervangen door vergelijkbare onderdelen die wel goed te koop zijn, of weggehaald.

| Oud | Nieuw | Reden |
|---|---|---|
| AMD Ryzen 7 5700X3D | AMD Ryzen 7 5700X | Geen prijzen meer in de Pricewatch |
| MSI MAG B550 TOMAHAWK | ASUS TUF Gaming B550-PLUS | Geen prijzen meer |
| MSI B650 GAMING PLUS WIFI | MSI B850 GAMING PLUS WIFI | Geen prijzen meer, B850 is de opvolger |
| Gigabyte B650M DS3H | Gigabyte B850M DS3H | Nog maar één winkel, B850 is de opvolger |
| ASUS ROG STRIX B650E-I GAMING WIFI | ASUS ROG Strix B850-I GAMING WIFI | Opvolger, zodat alle AM5-borden dezelfde generatie (B850) zijn |
| MSI PRO B760M-A WIFI DDR4 | Gigabyte B760M DS3H DDR4 | Geen prijzen meer |
| Corsair Vengeance LPX 32GB DDR4-3600 | Kingston FURY Beast 32GB DDR4-3200 | Maar drie winkels en € 100 duurder |
| G.Skill Ripjaws V 64GB DDR4 en Corsair Vengeance 64GB DDR5 | geen | Rond € 1.500 bij één of enkele winkels |
| Intel Arc B580 Limited Edition | MSI GeForce RTX 5060 Ti 16G VENTUS 2X OC PLUS | Arc B580 bij maar twee winkels |
| AMD Radeon RX 7600 en RX 7800 XT (referentie) | PowerColor Reaper RX 9060 XT 16GB | Referentiekaarten niet meer te koop |
| NVIDIA RTX 5070 en RTX 5080 Founders Edition | MSI RTX 5070 VENTUS 2X OC en RTX 5080 VENTUS 3X OC | Founders Edition niet in Nederlandse winkels |
| ASUS ROG Strix RTX 4090 OC | ASUS ROG Astral RTX 5080 OC | Niet meer te koop. De Astral is net zo lang (358 mm) |
| Cooler Master MasterBox NR200P | Cooler Master MasterBox NR200P V3 | Origineel niet meer te koop. De V3 heeft andere maten |
| Cooler Master MasterBox Q300L | Cooler Master MasterBox Q300L V2 | Origineel bij maar twee winkels |
| Corsair 4000D Airflow | geen | Gewone versie bijna niet meer te koop |
| SilverStone SUGO SG13 | geen | Er past alleen een koeler van maximaal 61 mm in, en die zit niet in de data |
| Crucial P3 Plus 500GB en MX500 1TB | Kingston NV3 500GB en Samsung 870 EVO 1TB | Crucial verkoopt geen consumentenproducten meer |
| be quiet! System Power 10 450W | geen | Geen prijzen meer. De CX550 is nu de goedkoopste voeding |
| be quiet! Pure Power 12 M 850W | be quiet! Pure Power 13 M 850W | Opvolger |
| Seasonic FOCUS GX-750 | Seasonic FOCUS GX ATX 3 (2024) 750W | Oude versie bij maar één winkel |

## 5. Geschat verbruik

Het verbruik (`power_w`) wordt gebruikt voor de controle van de voeding.

**Processors.** Het maximale vermogen volgens de fabrikant:

- AMD: de PPT, dat is 1,35 keer de TDP. 65 W TDP wordt 88 W en 120 W wordt 162 W.
- Intel: de Maximum Turbo Power. Core i5-12400F 117 W, Core i5-14600K 181 W en Core Ultra 5 245K 159 W.

**Videokaarten.** Het totale vermogen (total board power) van de chip volgens AMD of NVIDIA: RTX 5060 145 W, RX 9060 XT 16GB 160 W, RTX 5060 Ti 180 W, RTX 5070 250 W, RX 9070 XT 304 W en RTX 5080 360 W. Overgeklokte versies (OC) kunnen iets meer verbruiken. De marge van 30% vangt dat op.

**Overige onderdelen.** Dit zijn vaste schattingen, geen gegevens van de fabrikant:

| Categorie | Schatting |
|---|---|
| Moederbord | 40 W |
| Geheugen | 6 W per kit (3 W per module) |
| Koeler | 5 W (ventilator) |
| Kast | 5 W (meegeleverde ventilatoren) |
| Opslag | 5 W |
| Voeding | 0 W, levert zelf het vermogen |

## 6. Randgevallen in de echte data

De data is zo gekozen dat elke controleregel ook met echte onderdelen een keer afgaat:

- **Socket:** een AM4-processor op een AM5-bord.
- **RAM-type:** DDR5-geheugen op de Gigabyte B760M DS3H DDR4, een Intel-bord met DDR4.
- **Formaat:** een ATX-bord in de Q300L V2 (Micro-ATX) of de NR200P V3 (Mini-ITX).
- **Lengte videokaart:** de ROG Astral (358 mm) past niet in de Fractal North (355 mm), maar wel in de Q300L V2 (360 mm).
- **Hoogte koeler:** de NH-L12S (70 mm) past precies in de NR200P V3 (70 mm). De Freezer 36 (159 mm) past precies in de Q300L V2 (159 mm), de AK620 (160 mm) net niet.
- **Voeding:** de Ryzen 7 9800X3D met de ROG Astral en de rest van de onderdelen verbruikt ongeveer 583 W. Met 30% marge is 758 W nodig. Een voeding van 750 W geeft dan een waarschuwing, 850 W is in orde.

# Controle van de onderdelendata

De onderdelen in `database/data/parts.php` zijn bestaande producten. Niet alles is al gecontroleerd. In dit document staat wat nog nagekeken moet worden.

Werkwijze na het controleren van een onderdeel:

1. De waarde aanpassen in `database/data/parts.php`.
2. Bij een prijs: `price_date` op de datum van de controle zetten, bijvoorbeeld `'price_date' => '2026-11-02'`.
3. Het onderdeel hieronder afvinken.
4. `composer seed` draaien.

## 1. Prijzen

Alle prijzen zijn schattingen die ik op 28 oktober 2026 heb ingevoerd. Ze zijn nog niet opgezocht. Bron voor de controle: de laagste prijs bij een bekende Nederlandse winkel in de Tweakers Pricewatch.

### Processors

| Onderdeel | Richtprijs nu | Gecontroleerd |
|---|---|---|
| AMD Ryzen 5 5600 | € 99,00 | |
| AMD Ryzen 7 5700X3D | € 199,00 | |
| AMD Ryzen 5 7600 | € 179,00 | |
| AMD Ryzen 5 9600X | € 209,00 | |
| AMD Ryzen 7 7800X3D | € 349,00 | |
| AMD Ryzen 7 9800X3D | € 469,00 | |
| Intel Core i5-12400F | € 109,00 | |
| Intel Core i5-14600K | € 229,00 | |
| Intel Core Ultra 5 245K | € 249,00 | |

### Moederborden

| Onderdeel | Richtprijs nu | Gecontroleerd |
|---|---|---|
| MSI MAG B550 TOMAHAWK | € 139,00 | |
| ASRock B550M Pro4 | € 99,00 | |
| Gigabyte B650M DS3H | € 119,00 | |
| MSI B650 GAMING PLUS WIFI | € 169,00 | |
| ASUS ROG STRIX B650E-I GAMING WIFI | € 289,00 | |
| MSI PRO B760M-A WIFI DDR4 | € 139,00 | |
| Gigabyte Z790 AORUS ELITE AX | € 229,00 | |
| MSI MAG B860 TOMAHAWK WIFI | € 219,00 | |

### Geheugen

| Onderdeel | Richtprijs nu | Gecontroleerd |
|---|---|---|
| Kingston FURY Beast 16GB (2x8GB) DDR4-3200 | € 45,00 | |
| Corsair Vengeance LPX 32GB (2x16GB) DDR4-3600 | € 79,00 | |
| G.Skill Ripjaws V 64GB (2x32GB) DDR4-3600 | € 139,00 | |
| Kingston FURY Beast 16GB (2x8GB) DDR5-5600 | € 65,00 | |
| Corsair Vengeance 32GB (2x16GB) DDR5-6000 CL30 | € 119,00 | |
| G.Skill Flare X5 32GB (2x16GB) DDR5-6000 CL30 | € 125,00 | |
| Corsair Vengeance 64GB (2x32GB) DDR5-6000 CL30 | € 239,00 | |

### Videokaarten

| Onderdeel | Richtprijs nu | Gecontroleerd |
|---|---|---|
| Intel Arc B580 Limited Edition | € 279,00 | |
| MSI GeForce RTX 5060 VENTUS 2X OC 8G | € 309,00 | |
| AMD Radeon RX 7600 | € 259,00 | |
| NVIDIA GeForce RTX 5070 Founders Edition | € 619,00 | |
| AMD Radeon RX 7800 XT | € 479,00 | |
| Sapphire PULSE Radeon RX 9070 XT 16GB | € 689,00 | |
| NVIDIA GeForce RTX 5080 Founders Edition | € 1.179,00 | |
| ASUS ROG Strix GeForce RTX 4090 OC Edition | € 1.999,00 | |

### Koelers

| Onderdeel | Richtprijs nu | Gecontroleerd |
|---|---|---|
| Noctua NH-L12S | € 59,00 | |
| DeepCool AK400 | € 35,00 | |
| Thermalright Peerless Assassin 120 SE | € 39,00 | |
| ARCTIC Freezer 36 | € 32,00 | |
| DeepCool AK620 | € 59,00 | |
| Noctua NH-D15 G2 | € 149,00 | |

### Kasten

| Onderdeel | Richtprijs nu | Gecontroleerd |
|---|---|---|
| Cooler Master MasterBox Q300L | € 49,00 | |
| Corsair 3000D Airflow | € 79,00 | |
| Corsair 4000D Airflow | € 99,00 | |
| Fractal Design North | € 139,00 | |
| Lian Li Lancool 216 | € 99,00 | |
| Cooler Master MasterBox NR200P | € 109,00 | |
| SilverStone SUGO SG13 | € 59,00 | |

### Opslag

| Onderdeel | Richtprijs nu | Gecontroleerd |
|---|---|---|
| Crucial P3 Plus 500GB | € 45,00 | |
| Samsung 870 EVO 500GB | € 59,00 | |
| Kingston NV3 1TB | € 65,00 | |
| Crucial MX500 1TB | € 75,00 | |
| Western Digital WD_BLACK SN850X 1TB | € 99,00 | |
| Samsung 990 PRO 2TB | € 179,00 | |

### Voedingen

| Onderdeel | Richtprijs nu | Gecontroleerd |
|---|---|---|
| be quiet! System Power 10 450W | € 52,00 | |
| Corsair CX550 | € 62,00 | |
| Corsair RM650e | € 89,00 | |
| Seasonic FOCUS GX-750 | € 115,00 | |
| be quiet! Pure Power 12 M 850W | € 125,00 | |
| Corsair RM1000x | € 179,00 | |

## 2. Geschat verbruik

Het verbruik (`power_w`) wordt gebruikt voor de controle van de voeding.

**Processors.** Het maximale vermogen volgens de fabrikant:

- AMD: de PPT, dat is 1,35 keer de TDP. 65 W TDP wordt 88 W, 105 W wordt 142 W en 120 W wordt 162 W.
- Intel: de Maximum Turbo Power. Core i5-12400F 117 W, Core i5-14600K 181 W en Core Ultra 5 245K 159 W.

Controleren op amd.com en intel.com (ark.intel.com).

**Videokaarten.** Het totale vermogen (total board power) van het referentiemodel van AMD, NVIDIA of Intel. Overgeklokte versies van andere merken kunnen iets meer verbruiken. Dat geldt voor de MSI RTX 5060 VENTUS 2X OC, de Sapphire PULSE RX 9070 XT en de ASUS ROG Strix RTX 4090 OC.

**Overige onderdelen.** Dit zijn vaste schattingen, geen gegevens van de fabrikant:

| Categorie | Schatting |
|---|---|
| Moederbord | 40 W |
| Geheugen | 6 W per kit (3 W per module) |
| Koeler | 5 W (ventilator) |
| Kast | 5 W (meegeleverde ventilatoren) |
| Opslag | 5 W |
| Voeding | 0 W, levert zelf het vermogen |

## 3. Specificaties waar ik niet zeker van ben

| Onderdeel | Veld | Waarde nu | Wat controleren |
|---|---|---|---|
| MSI GeForce RTX 5060 VENTUS 2X OC 8G | lengte | 197 mm | Lengte op de productpagina van MSI. |
| Sapphire PULSE Radeon RX 9070 XT 16GB | lengte | 320 mm | Lengte op de productpagina van Sapphire. |
| Intel Arc B580 Limited Edition | lengte | 272 mm | Lengte op de productpagina van Intel. |
| ASUS ROG Strix GeForce RTX 4090 OC Edition | lengte | 358 mm | Fabrikant geeft 357,6 mm, afgerond naar boven. Kijken of hij nog te koop is. |
| AMD Radeon RX 7600 en RX 7800 XT | lengte | 204 en 267 mm | Dit zijn de referentiemodellen van AMD. In winkels liggen vooral versies van andere merken, met een andere lengte. Eventueel vervangen door een model dat wel te koop is. |
| Cooler Master MasterBox Q300L | max. GPU en koeler | 360 en 157 mm | Specificaties op de site van Cooler Master. |
| Corsair 3000D Airflow | max. GPU en koeler | 360 en 170 mm | Specificaties op de site van Corsair. |
| Lian Li Lancool 216 | max. GPU en koeler | 392 en 180 mm | Specificaties op de site van Lian Li. |
| SilverStone SUGO SG13 | max. GPU en koeler | 267 en 61 mm | Specificaties op de site van SilverStone. |
| Corsair 4000D Airflow en Lian Li Lancool 216 | grootste formaat | ATX | Volgens de fabrikant past ook een smal E-ATX-bord. Dat maakt nu niet uit, want er zitten geen E-ATX-borden in de data. |
| ARCTIC Freezer 36 | hoogte | 159 mm | Productpagina van ARCTIC. |
| Noctua NH-L12S | hoogte | 70 mm | Hoogte met de ventilator bovenop, productpagina van Noctua. |
| MSI PRO B760M-A WIFI DDR4 | naam | | Er bestaan meerdere versies (ook "DDR4 II"). Kijken welke te koop is. |
| Gigabyte B650M DS3H | aantal RAM-sloten | 4 | Productpagina van Gigabyte. |
| Kingston FURY Beast 16GB (2x8GB) DDR5-5600 | naam | | Bestaat in meerdere CL-varianten. Kijken welke te koop is. |

## 4. Verkrijgbaarheid

Sommige onderdelen zijn van een oudere generatie en zijn misschien niet meer nieuw te koop: Ryzen 5 5600, Ryzen 7 5700X3D, Core i5-12400F, Radeon RX 7600, Radeon RX 7800 XT en GeForce RTX 4090. Bij de prijscontrole kijken of ze nog te koop zijn. Zo niet, dan vervangen door een vergelijkbaar onderdeel en de specificaties opnieuw opzoeken.

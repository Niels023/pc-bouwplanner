# Testplan

In dit testplan staat hoe ik de PC-bouwplanner test en welke testcases er per eis zijn. De echte resultaten staan in [testrapport.md](testrapport.md).

## 1. Aanpak

| Testvorm | Wat | Waarmee | Wanneer |
|---|---|---|---|
| Unit tests | Losse klassen: de regels, de checker, de build en later de aanbeveling. Zonder database, met onderdelen die de test zelf maakt | PHPUnit, map `tests/Unit` | Bij elke wijziging en voor elke merge |
| Databasetests | Het seed-script en de data in een aparte testdatabase | PHPUnit, map `tests/Database` | Voor elke merge |
| API-tests | De endpoints: statuscodes, JSON en foutmeldingen | PHPUnit, map `tests/Api` | Voor elke merge, zodra de API er is |
| Handmatige tests | De website op telefoon en desktop, toetsenbord, contrast, zonder JavaScript | Browser, Lighthouse | Als de pagina's af zijn |
| Gebruikerstests | 2 of 3 echte mensen voeren opdrachten uit op de site | Observatie en vragen | Als de basisversie af is |
| Codestijl | PSR-12 | PHP_CodeSniffer (`composer lint`) | Voor elke merge |

Ik gebruik grenswaardenanalyse: bij elke grens test ik de waarde precies op de grens en de waarde er net naast. Bij de voeding zijn dat bijvoorbeeld 450 W, 449 W, 585 W en 584 W bij een verbruik van 450 W. Zo vind ik fouten als `>` in plaats van `>=`.

Om te controleren of de tests zelf goed zijn, bouw ik af en toe expres een fout in de code. Een goede test moet dan falen. Dit heet mutatietesten. Ik doe dit met de hand, voor de regels waar grenzen belangrijk zijn.

Alle tests moeten slagen voordat een branch naar `main` gaat.

## 2. Welke eis wordt hoe getest

| Eis | Testvorm | Status testcases |
|---|---|---|
| F-01 | Databasetest | Uitgewerkt in hoofdstuk 3 |
| F-02, F-03 | Databasetest, API-test en paginatest | Uitgewerkt in hoofdstuk 3 en 4 |
| F-04, F-12 | Paginatest, unit test en handmatig | Uitgewerkt in hoofdstuk 4 en 5 |
| F-05 t/m F-11 | Unit test en API-test | Uitgewerkt in hoofdstuk 3 |
| F-13 | Unit test, paginatest en handmatig | Uitgewerkt in hoofdstuk 3, 4 en 5 |
| F-14, F-15 | API-test, paginatest en handmatig | Uitgewerkt in hoofdstuk 3, 4 en 5 |
| F-16, F-17 | Unit test, API-test en paginatest | Uitgewerkt in hoofdstuk 3 en 4 |
| F-18 | API-test en handmatig | Uitgewerkt in hoofdstuk 3 |
| F-19, F-20, F-21 | Unit test en handmatig | Komt bij die eisen |
| NF-01, NF-02, NF-07, NF-08 | Handmatig en Lighthouse | Uitgewerkt in hoofdstuk 5 |
| NF-03 | API-test, paginatest en code nakijken | Uitgewerkt in hoofdstuk 3 en 4 |
| NF-04 | Code nakijken | Uitgewerkt in hoofdstuk 3 |
| NF-05 | PHP_CodeSniffer | Uitgewerkt in hoofdstuk 3 |
| NF-06 | Alle testvormen | Uitgewerkt in hoofdstuk 3 |
| NF-09 | Handmatig | Uitgewerkt in hoofdstuk 5 |

## 3. Testcases

De invoer bij de unit tests zijn onderdelen die de test zelf maakt met de klasse `Tests\Support\Parts`. Stappen bij een unit test: de test draaien met `composer test`. De naam van de test staat in de laatste kolom.

### F-01 Database met bestaande onderdelen

Stappen: `composer test` maakt de testdatabase, vult die met het seed-script en controleert de inhoud.

| ID | Invoer | Verwacht resultaat | Test |
|---|---|---|---|
| TC-F01-01 | Seed-data | Tussen 40 en 60 onderdelen | `testDatabaseHasBetween40And60Parts` |
| TC-F01-02 | Seed-data | 8 categorieën, elk minimaal 4 onderdelen | `testEveryCategoryHasAtLeastFourParts` |
| TC-F01-03 | Seed-data | Elk onderdeel heeft een rij in de tabel van zijn categorie | `testEveryPartHasSpecificationsForItsCategory` |
| TC-F01-04 | Seed-data | Elk onderdeel heeft een prijs boven 0 en een peildatum | `testEveryPartHasPriceAndPriceDate` |

### F-02 Onderdelen per categorie bekijken (API)

| ID | Invoer | Verwacht resultaat | Test |
|---|---|---|---|
| TC-F02-01 | `GET /api/parts` | 200, JSON, alle onderdelen en een `count` | `testListGivesAllPartsAsJson` |
| TC-F02-02 | `GET /api/parts/{id}` van de Ryzen 5 7600 | Categorie, merk, prijs 176,82, peildatum en socket AM5 | `testPartHasPriceDateAndSpecs` |
| TC-F02-03 | Data uit de testdatabase | Elke categorie wordt de juiste klasse | `testEveryCategoryBecomesTheRightClass` |
| TC-F02-04 | Ryzen 5 7600 uit de database | Alle velden kloppen, prijs is 17682 cent | `testCpuHasAllItsFields` |
| TC-F02-05 | B850M DS3H en NR200P V3 | Formaat is ingevuld en te vergelijken | `testBoardAndCaseGetTheirFormFactor` |
| TC-F02-06 | Kit van 32 GB en SSD van 2 TB | Elk onderdeel krijgt zijn eigen capaciteit | `testRamAndStorageGetTheirOwnCapacity` |
| TC-F02-07 | Filter op categorie `gpu` | Onderdelen staan op prijs, laag naar hoog | `testPartsAreSortedByCategoryAndPrice` |

### F-03 Onderdelen filteren (API)

| ID | Invoer | Verwacht resultaat | Test |
|---|---|---|---|
| TC-F03-01 | `category=gpu` | Alleen videokaarten | `testFilterByCategory` |
| TC-F03-02 | `category=toetsenbord` | 422 `invalid_category`, melding noemt de waarde | `testUnknownCategoryGivesError` |
| TC-F03-03 | `socket=AM4` | Processors en moederborden met AM4 | `testFilterBySocketFindsCpusAndBoards` |
| TC-F03-04 | `ram_type=DDR4&category=ram` | Alleen DDR4-geheugen | `testFilterByRamType` |
| TC-F03-05 | `form_factor=Mini-ITX` | Moederborden en kasten | `testFilterByFormFactorFindsBoardsAndCases` |
| TC-F03-06 | `brand=Noctua` | Alleen Noctua | `testFilterByBrand` |
| TC-F03-07 | `max_price=176.82` | De duurste is precies 176,82 (grenswaarde) | `testMaxPriceInEuros` |
| TC-F03-08 | `max_price=abc`, `0`, `-10` | 422 `invalid_max_price` | `testInvalidMaxPriceGivesError` |
| TC-F03-09 | Vier filters tegelijk | Alleen onderdelen die aan alles voldoen | `testFiltersCanBeCombined` |
| TC-F03-10 | `brand=Bestaat niet` | 200 met een lege lijst | `testNoResultsGivesEmptyList` |
| TC-F03-11 | Lege filters | Worden genegeerd | `testEmptyFilterIsIgnored` |
| TC-F03-12 | `category[]=cpu&category[]=gpu` | Geen fout | `testFilterAsArrayIsIgnored` |

De filters zijn ook getest op de `PartRepository`: `testFiltersCanBeCombined`, `testMaxPriceIsInclusive` en `testFormFactorFilterFindsBoardsAndCases` in `PartRepositoryTest`. De melding "Geen onderdelen gevonden met deze filters" komt op de pagina en wordt daar getest.

### F-14 Build opslaan (API)

| ID | Invoer | Verwacht resultaat | Test |
|---|---|---|---|
| TC-F14-01 | Naam en onderdelen | 201, code van 8 tekens en een url | `testSavedBuildGetsShareCodeAndUrl` |
| TC-F14-02 | Twee builds opslaan | Twee verschillende codes | `testEveryBuildGetsItsOwnCode` |
| TC-F14-03 | Naam leeg, alleen spaties of ontbreekt | 422 `invalid_name` | `testNameIsRequired` |
| TC-F14-04 | Naam van 60 tekens | 201 (grenswaarde) | `testNameOfSixtyCharactersIsAllowed` |
| TC-F14-05 | Naam van 61 tekens | 422 `invalid_name` | `testNameOfSixtyOneCharactersIsNotAllowed` |
| TC-F14-06 | Naam van 60 keer "é" | 201, tekens tellen en geen bytes | `testNameLengthCountsCharactersNotBytes` |
| TC-F14-07 | Naam met spaties eromheen | Spaties worden weggehaald | `testNameIsTrimmed` |
| TC-F14-08 | Naam is een getal | 422 `invalid_name` | `testNameThatIsNotTextGivesError` |
| TC-F14-09 | Geen onderdelen | 422 `empty_build` | `testBuildWithoutPartsCannotBeSaved` |
| TC-F14-10 | Onderdeel bestaat niet | 422 `unknown_part` | `testInvalidPartsCannotBeSaved` |

### F-15 Opgeslagen build ophalen (API)

| ID | Invoer | Verwacht resultaat | Test |
|---|---|---|---|
| TC-F15-01 | Code van een opgeslagen build | Naam, onderdelen, totaalprijs en controleresultaat | `testSavedBuildCanBeOpenedWithCode` |
| TC-F15-02 | Code in hoofdletters | Build wordt gevonden | `testCodeInCapitalsAlsoWorks` |
| TC-F15-03 | Onbekende code | 404 "Geen build gevonden met deze code." | `testUnknownCodeGivesNotFound` |

### F-18 REST API

Behalve de testcases bij F-02, F-03, F-14 en F-15:

| ID | Invoer | Verwacht resultaat | Test |
|---|---|---|---|
| TC-F18-01 | Lege lijst naar `/api/builds/check` | 200, totaalstatus incomplete, 6 regels | `testEmptyListGivesIncomplete` |
| TC-F18-02 | Complete build die past | Totaalstatus ok | `testCompleteBuildThatFitsIsOk` |
| TC-F18-03 | Ryzen 5 5600 en B550M Pro4 | Totaal 220,44 en 128 W | `testTotalsAreCalculated` |
| TC-F18-04 | AM4-processor op AM5-bord | Fout met de juiste melding | `testWrongSocketGivesErrorWithMessage` |
| TC-F18-05 | Alleen een processor | Socketregel `not_checked` | `testMissingPartGivesNotChecked` |
| TC-F18-06 | NH-L12S in NR200P V3, ROG Astral in North en in Q300L V2 | In orde, fout en in orde (echte onderdelen op de grens) | `testRealPartsOnTheEdgeOfFitting` |
| TC-F18-07 | 9800X3D en ROG Astral met 750 W | 583 W verbruik, waarschuwing | `testHighEndBuildOnSmallPsuGivesWarning` |
| TC-F18-08 | Kapotte JSON | 400 `invalid_json` | `testInvalidJsonGivesBadRequest` |
| TC-F18-09 | Geen `part_ids` | 422 `invalid_part_ids` | `testMissingPartIdsGivesError` |
| TC-F18-10 | `part_ids` is 5, tekst, 0, -1, 1.5 of een object | 422 `invalid_part_ids` | `testPartIdsMustBeAListOfPositiveNumbers` |
| TC-F18-11 | Onderdeel 999999 | 422 `unknown_part`, melding noemt het id | `testUnknownPartGivesErrorWithId` |
| TC-F18-12 | Twee processors | 422 `duplicate_category` | `testTwoPartsInSameCategoryGivesError` |
| TC-F18-13 | `/api/parts/999999` | 404 `part_not_found` | `testUnknownPartGivesNotFound` |
| TC-F18-14 | `/api/parts/abc` | 404 `part_not_found` | `testIdThatIsNotANumberGivesNotFound` |
| TC-F18-15 | Onbekend pad | 404 `not_found` | `testUnknownPathGivesNotFound` |
| TC-F18-16 | `DELETE /api/parts` | 405 `method_not_allowed` | `testWrongMethodGivesMethodNotAllowed` |
| TC-F18-17 | Database niet bereikbaar | 500 `server_error`, geen technische details | `testDatabaseProblemGivesGeneralMessage` |
| TC-F18-18 | Met de hand: server starten en met curl filteren, controleren, opslaan en ophalen | Dezelfde antwoorden als in de API-tests | Handmatig |

### F-16 Aanbeveling op basis van budget en doel

Bij de unit tests gebruikt de test een kleine eigen catalogus. Daarin kost de goedkoopste build voor gamen precies € 920. De API-tests gebruiken de echte onderdelen.

| ID | Invoer | Verwacht resultaat | Test |
|---|---|---|---|
| TC-F16-01 | Budget € 919 | Te laag, minimum is € 920 | `testBudgetBelowMinimumGivesMinimumAmount` |
| TC-F16-02 | Budget € 0 | Te laag | `testEmptyBudgetIsTooLow` |
| TC-F16-03 | Budget € 920 (grenswaarde) | Goedkoopste build, niets over | `testExactlyMinimumBudgetGivesCheapestBuild` |
| TC-F16-04 | Budget € 920 voor streamen | Te laag, streamen heeft 32 GB nodig | `testMinimumDependsOnGoal` |
| TC-F16-05 | Elk doel, budget € 1.020 tot € 4.000 | Altijd compleet, zonder problemen en binnen budget | `testEveryBudgetGivesCompleteCompatibleBuildWithinBudget` |
| TC-F16-06 | Budget € 3.000 | Duurste videokaart binnen het deelbudget | `testPicksBestPartWithinCategoryBudget` |
| TC-F16-07 | Onderdeel van precies het deelbudget | Wordt gekozen (grenswaarde) | `testPartThatCostsExactlyTheCategoryBudgetIsAllowed` |
| TC-F16-08 | AM5-processor past in deelbudget, DDR5 is niet te betalen | AM4-processor wordt gekozen | `testSkipsCpuWhenBuildCouldNotBeFinishedAfterwards` |
| TC-F16-09 | Gamen en streamen | 16 GB en 32 GB geheugen | `testMemoryMeetsMinimumOfGoal` |
| TC-F16-10 | Werken en gamen | 500 GB en 1000 GB opslag | `testStorageMeetsMinimumOfGoal` |
| TC-F16-11 | Bord met 2 sloten is de duurste in het deelbudget | Wordt niet gekozen | `testBoardWithTooFewRamSlotsIsNotChosen` |
| TC-F16-12 | Budget € 1.800, gamen en werken | Bij gamen gaat restgeld naar de videokaart, bij werken niet | `testLeftoverGoesToWhatMattersForTheGoal` |
| TC-F16-13 | Upgrade naar videokaart van 300 W | Voeding van 850 W wordt gekozen | `testBetterGpuGetsAStrongerPsu` |
| TC-F16-14 | Twee keer dezelfde invoer | Precies dezelfde uitkomst | `testSameInputGivesSameResult` |
| TC-F16-15 | Budget 0, -5, abc, 12.50, 1e3, 100001, leeg (API) | 422 `invalid_budget` | `testInvalidBudgetGivesError` |
| TC-F16-16 | Geen budget (API) | 422 `invalid_budget` | `testMissingBudgetGivesError` |
| TC-F16-17 | Doel `wandelen` (API) | 422 `invalid_goal`, melding noemt de opties | `testUnknownGoalGivesErrorWithOptions` |
| TC-F16-18 | Geen doel (API) | 422 `invalid_goal` | `testMissingGoalGivesError` |
| TC-F16-19 | Budget € 1 (API) | 422 `budget_too_low`, melding noemt het minimum | `testBudgetTooLowNamesMinimumAmount` |
| TC-F16-20 | Minimum uit de melding, en 1 euro minder (API) | Minimum geeft 200, 1 euro minder geeft 422 | `testMinimumAmountInMessageIsCorrect` |
| TC-F16-21 | Elk doel, van het minimum tot € 6.000 in stappen van € 100 (API) | Altijd status ok en binnen budget | `testEveryBudgetGivesCompleteBuildWithinBudget` |
| TC-F16-22 | € 2.500 voor gamen en werken (API) | Gamen besteedt meer aan de videokaart | `testGamingSpendsMoreOnGpuThanWork` |
| TC-F16-23 | Twee keer dezelfde vraag (API) | Hetzelfde antwoord | `testSameRequestGivesSameAnswer` |
| TC-F16-24 | `config/app.php` | Percentages tellen op tot 100, elke categorie heeft een deel | `GoalConfigTest` |

### F-17 Uitleg bij de aanbeveling

| ID | Invoer | Verwacht resultaat | Test |
|---|---|---|---|
| TC-F17-01 | Videokaart binnen het deelbudget | "Beste videokaart binnen het deelbudget van € 440." | `testReasonNamesCategoryAndBudget` |
| TC-F17-02 | Geheugen bij streamen | Melding noemt minimaal 32 GB voor streamen | `testReasonForMemoryNamesMinimumForGoal` |
| TC-F17-03 | Deelbudget te laag | "Het deelbudget van € 46 was te krap, daarom de goedkoopste keuze die past." | `testReasonSaysWhenCategoryBudgetWasTooLow` |
| TC-F17-04 | Betere videokaart door restgeld | Melding noemt het deelbudget en de betere videokaart | `testReasonSaysWhenLeftoverPaidForUpgrade` |
| TC-F17-05 | Budget € 1.000 | Over = budget min totaal | `testLeftoverIsBudgetMinusTotal` |
| TC-F17-06 | Budget € 1.500 | Acht keuzes, voor elke categorie één | `testEveryCategoryHasOneChoice` |
| TC-F17-07 | € 2.000 voor gamen (API) | Acht keuzes met naam, deelbudget en reden, plus bedrag over | `testResponseHasBuildReasonsAndLeftover` |

### F-05 Controle socket

| ID | Invoer | Verwacht resultaat | Test |
|---|---|---|---|
| TC-F05-01 | Processor AM5, moederbord AM5 | In orde | `testSameSocketIsOk` |
| TC-F05-02 | Processor AM5, moederbord AM4 | Fout: "De processor heeft socket AM5, maar het moederbord heeft socket AM4." | `testDifferentSocketGivesErrorWithBothSockets` |

### F-06 Controle RAM-type

| ID | Invoer | Verwacht resultaat | Test |
|---|---|---|---|
| TC-F06-01 | Moederbord DDR5, geheugen DDR5 | In orde | `testSameRamTypeIsOk` |
| TC-F06-02 | Moederbord DDR5, geheugen DDR4 | Fout: "Het geheugen is DDR4, maar het moederbord ondersteunt alleen DDR5." | `testDifferentRamTypeGivesErrorWithBothTypes` |

### F-07 Controle formaat moederbord en kast

| ID | Invoer | Verwacht resultaat | Test |
|---|---|---|---|
| TC-F07-01 | Micro-ATX-bord in ATX-kast | In orde | `testSmallerBoardFitsInBiggerCase` |
| TC-F07-02 | ATX-bord in ATX-kast | In orde (grenswaarde) | `testSameSizeFits` |
| TC-F07-03 | ATX-bord in Mini-ITX-kast | Fout: "Het moederbord is ATX, maar in deze kast past maximaal Mini-ITX." | `testBiggerBoardGivesErrorWithBothSizes` |

### F-08 Controle voeding

Verbruik 450 W en 30% marge, dus 585 W is nodig, tenzij de tabel iets anders zegt.

| ID | Invoer | Verwacht resultaat | Test |
|---|---|---|---|
| TC-F08-01 | Voeding 650 W | In orde | `testEnoughWattageIsOk` |
| TC-F08-02 | Voeding 585 W | In orde (precies verbruik plus marge) | `testExactlyUsagePlusMarginIsOk` |
| TC-F08-03 | Voeding 584 W | Waarschuwing, melding noemt 585 W | `testOneWattBelowMarginGivesWarning` |
| TC-F08-04 | Voeding 450 W | Waarschuwing (precies het verbruik) | `testExactlyUsageGivesWarning` |
| TC-F08-05 | Voeding 449 W | Fout, melding noemt 449 W en 450 W | `testOneWattBelowUsageGivesError` |
| TC-F08-06 | Verbruik 583 W, voeding 758 W en 757 W | 758 W in orde, 757 W waarschuwing (757,9 W wordt naar boven afgerond) | `testNeededWattageIsRoundedUp` |
| TC-F08-07 | Marge 0%, voeding 450 W | In orde | `testMarginComesFromConstructor` |
| TC-F08-08 | Processor 200 W, videokaart 200 W, moederbord 50 W, voeding 449 W | Fout (alle onderdelen tellen mee) | `testUsageCountsAllChosenParts` |

### F-09 Controle lengte videokaart

| ID | Invoer | Verwacht resultaat | Test |
|---|---|---|---|
| TC-F09-01 | Kaart 300 mm, kast maximaal 355 mm | In orde | `testShorterCardFits` |
| TC-F09-02 | Kaart 355 mm, kast maximaal 355 mm | In orde (grenswaarde) | `testExactlyMaxLengthFits` |
| TC-F09-03 | Kaart 356 mm, kast maximaal 355 mm | Fout met beide maten | `testOneMillimeterTooLongGivesErrorWithBothLengths` |

### F-10 Controle hoogte koeler

| ID | Invoer | Verwacht resultaat | Test |
|---|---|---|---|
| TC-F10-01 | Koeler 155 mm, kast maximaal 170 mm | In orde | `testLowerCoolerFits` |
| TC-F10-02 | Koeler 70 mm, kast maximaal 70 mm | In orde (grenswaarde) | `testExactlyMaxHeightFits` |
| TC-F10-03 | Koeler 160 mm, kast maximaal 159 mm | Fout met beide maten | `testOneMillimeterTooHighGivesErrorWithBothHeights` |

### F-11 Duidelijk resultaat van de controle

| ID | Invoer | Verwacht resultaat | Test |
|---|---|---|---|
| TC-F11-01 | Lege build | 6 regels nog niet gecontroleerd, totaalstatus nog niet compleet | `testEmptyBuildChecksNothing` |
| TC-F11-02 | Lege build | Melding noemt wat ontbreekt: "Kies eerst een processor en een moederbord." | `testMissingPartsAreNamedInMessage` |
| TC-F11-03 | Alleen een processor | Melding noemt alleen het ontbrekende onderdeel: "Kies eerst een moederbord." | `testOnlyMissingPartIsNamed` |
| TC-F11-04 | Alleen een voeding | Voedingsregel nog niet gecontroleerd | `testPsuWithoutCpuIsNotChecked` |
| TC-F11-05 | Complete build die past | Alle regels in orde, totaalstatus in orde | `testCompleteBuildThatFitsIsOk` |
| TC-F11-06 | Build met verkeerde socket | Totaalstatus fout | `testOneProblemMakesTheWholeBuildAnError` |
| TC-F11-07 | Onvolledige build, resultaten in orde en nog niet gecontroleerd | Totaalstatus nog niet compleet | `testIncompleteBuildGivesIncomplete` |
| TC-F11-08 | Onvolledige build, waarschuwing en nog niet gecontroleerd | Totaalstatus waarschuwing | `testWarningGoesBeforeIncomplete` |
| TC-F11-09 | Onvolledige build, fout, waarschuwing en nog niet gecontroleerd | Totaalstatus fout | `testErrorGoesBeforeEverythingElse` |
| TC-F11-10 | Complete build, alleen resultaten in orde | Totaalstatus in orde | `testAllOkGivesOk` |
| TC-F11-11 | Build met alleen een processor | De build weet welke onderdelen gekozen zijn | `testHasPartOnlyForChosenCategories` |
| TC-F11-12 | Onvolledige build waarvan alle regels in orde zijn | Totaalstatus nog niet compleet | `testIncompleteBuildWithAllRulesOkIsStillIncomplete` |
| TC-F11-13 | Build met alles behalve opslag | Alle regels in orde, totaalstatus nog niet compleet | `testBuildWithoutStorageIsNotComplete` |
| TC-F11-14 | Build met alle acht onderdelen | De build is compleet | `testBuildIsCompleteWithAllEightParts` |
| TC-F11-15 | Build zonder opslag | De build is niet compleet | `testBuildIsNotCompleteWithMissingPart` |

### F-13 Totaalprijs en geschat verbruik

| ID | Invoer | Verwacht resultaat | Test |
|---|---|---|---|
| TC-F13-01 | Lege build | Prijs 0 en verbruik 0 | `testEmptyBuildCostsNothingAndUsesNoPower` |
| TC-F13-02 | Onderdelen van € 124,96 en € 95,48 | Totaal € 220,44 | `testTotalPriceIsSumOfAllParts` |
| TC-F13-03 | Onderdelen van 88 W, 145 W en 6 W | Verbruik 239 W | `testEstimatedPowerIsSumOfAllParts` |
| TC-F13-04 | Opslag van 5 W en € 100 | Opslag telt mee in prijs en verbruik | `testStorageCountsTowardsTotals` |
| TC-F13-05 | Twee processors in dezelfde build | De laatste vervangt de eerste | `testNewPartReplacesPartInSameCategory` |
| TC-F13-06 | Complete build | Het rapport van de controle bevat dezelfde totalen als de build | `testReportContainsTotals` |

### NF-03 Veilig

| ID | Stappen | Verwacht resultaat |
|---|---|---|
| TC-NF03-01 | `brand=' OR '1'='1` naar `/api/parts` sturen (`testSqlInjectionInFilterDoesNothing`) | 0 resultaten, de tekst wordt niet als SQL uitgevoerd |
| TC-NF03-02 | Naam met `<script>` opslaan (`testHtmlInNameIsStoredAsPlainText`) | De tekst wordt ongewijzigd opgeslagen. Escapen gebeurt op de pagina |
| TC-NF03-03 | Alle query's in `src/Repository` nakijken | Alleen prepared statements met `?` |
| TC-NF03-04 | Repository nakijken op wachtwoorden | Geen wachtwoorden, `.env` staat in `.gitignore` |

### NF-04 Instelbaar zonder code aan te passen

| ID | Stappen | Verwacht resultaat |
|---|---|---|
| TC-NF04-01 | `.env.example` kopiëren naar `.env` en `composer seed` draaien | De database wordt gevuld met de gegevens uit `.env` |
| TC-NF04-02 | `psu_margin_percent` opzoeken in `config/app.php` | De marge staat daar en de checker gebruikt die waarde (`CompatibilityCheckerTest`) |

### NF-05 Nette code

| ID | Stappen | Verwacht resultaat |
|---|---|---|
| TC-NF05-01 | `composer lint` draaien | 0 fouten en 0 waarschuwingen volgens PSR-12 |

### NF-06 Getest

| ID | Stappen | Verwacht resultaat |
|---|---|---|
| TC-NF06-01 | `composer test` draaien voor elke merge | Alle tests slagen |
| TC-NF06-02 | Expres fouten in de regels bouwen, zoals `>` veranderen in `>=` | De tests falen bij elke ingebouwde fout |

## 4. Paginatests

Deze tests staan in `tests/Pages/`. Ze sturen een verzoek naar de applicatie, net als een browser zonder JavaScript, en controleren de HTML, de statuscode en de doorverwijzing.

### F-02 en F-03 Kaartjes en filters op de pagina

| ID | Invoer | Verwacht resultaat | Test |
|---|---|---|---|
| TC-P-01 | `/bouwen/cpu` | Stap 1 van 8, onderdeel, prijs, peildatum en socket | `testStepShowsPartsAsCards` |
| TC-P-02 | Alle acht categorieën | Stap 1 t/m 8 in de juiste volgorde | `testAllEightStepsExistInTheRightOrder` |
| TC-P-03 | Onbekende stap | 404 | `testUnknownStepGivesNotFound` |
| TC-P-04 | Alle categorieën | Elke categorie toont zijn eigen specificaties | `testEveryCategoryShowsItsOwnSpecs` |
| TC-P-05 | `brand=Intel` | Alleen Intel | `testFilterShowsOnlyMatchingParts` |
| TC-P-06 | Merk, socket en maximale prijs | Alleen onderdelen die aan alles voldoen | `testFiltersCanBeCombined` |
| TC-P-07 | Filters ingevuld | Filters blijven ingevuld staan | `testFiltersStayFilledIn` |
| TC-P-08 | `max_price=1` | "Geen onderdelen gevonden met deze filters" | `testNoResultsGivesMessage` |
| TC-P-09 | `max_price=abc` | Melding, onderdelen blijven zichtbaar | `testInvalidMaxPriceGivesMessageAndStillShowsParts` |
| TC-P-10 | Processor en koeler | Alleen filters die bij de categorie horen | `testOnlyFiltersThatFitTheCategoryAreShown` |

### F-04 Build stap voor stap samenstellen

| ID | Invoer | Verwacht resultaat | Test |
|---|---|---|---|
| TC-P-11 | Onderdeel kiezen | Doorsturen naar de stap, onderdeel staat op gekozen | `testChoosingAPartRedirectsAndShowsIt` |
| TC-P-12 | Kiezen met filters in de URL | Filters blijven, onbekende parameters vallen weg | `testRedirectKeepsFilters` |
| TC-P-13 | Tweede onderdeel in dezelfde categorie | Vervangt het eerste | `testChoosingAnotherPartReplacesTheFirst` |
| TC-P-14 | Naar een andere stap gaan | Keuze blijft bewaard | `testChoiceIsKeptOnOtherSteps` |
| TC-P-15 | Onderdeel verwijderen | Stap is weer leeg | `testRemovingAPartEmptiesItsStep` |
| TC-P-16 | Onbekend onderdeel of geen id | 404 | `testUnknownPartCannotBeChosen` |
| TC-P-17 | Onderdeel van een andere categorie | 404 | `testPartOfOtherCategoryCannotBeChosen` |
| TC-P-18 | Stap Geheugen | Links naar alle stappen, huidige stap is gemarkeerd | `testStepNavigationLinksToAllSteps` |
| TC-P-19 | Eerste en laatste stap | Geen vorige stap, laatste gaat naar het overzicht | `testFirstAndLastStepHaveTheRightLinks` |

### F-12 Onverenigbare onderdelen herkennen

| ID | Invoer | Verwacht resultaat | Test |
|---|---|---|---|
| TC-P-20 | AM5-processor gekozen, stap Moederbord | AM4-borden zijn grijs met de reden | `testPartsThatDoNotFitAreGreyWithReason` |
| TC-P-21 | Grijs bord kiezen | Kan wel, totaalstatus wordt Fout | `testPartsThatDoNotFitCanStillBeChosen` |
| TC-P-22 | AM5-processor gekozen | Borden die passen staan bovenaan | `testPartsThatFitComeFirst` |
| TC-P-23 | Niets gekozen | Niets is grijs | `testNothingIsGreyWithoutChoices` |
| TC-P-24 | 9800X3D en ROG Astral gekozen, stap Voeding | Krappe voeding krijgt een waarschuwing | `testTightPsuGivesWarningOnCard` |
| TC-P-25 | Unit: onderdeel dat past | Geen problemen | `testPartThatFitsHasNoProblems` |
| TC-P-26 | Unit: onderdeel dat niet past | De fout van de juiste regel | `testPartThatDoesNotFitGivesTheProblem` |
| TC-P-27 | Unit: krappe voeding | Waarschuwing | `testTightPsuGivesAWarning` |
| TC-P-28 | Unit: videokaart overbelast de voeding | Videokaart krijgt de fout | `testGpuThatOverloadsThePsuGetsTheProblem` |
| TC-P-29 | Unit: fout tussen andere onderdelen | Niet de schuld van dit onderdeel | `testProblemBetweenOtherPartsIsNotBlamedOnCandidate` |
| TC-P-30 | Unit: onderdeel vervangen | Kijkt naar het nieuwe onderdeel | `testReplacingAPartLooksAtTheNewPart` |
| TC-P-31 | Unit: oude fout, ander onderdeel in die regel | Fout telt nog steeds | `testOldProblemStillCountsForPartInThatRule` |

### F-13 Totalen op de pagina

| ID | Invoer | Verwacht resultaat | Test |
|---|---|---|---|
| TC-P-32 | Ryzen 5 5600 en B550M Pro4 | Totaalbalk: € 220,44 en 128 W | `testTotalsAreAddedUp` |

### F-14 en F-15 Opslaan en openen

| ID | Invoer | Verwacht resultaat | Test |
|---|---|---|---|
| TC-P-33 | Leeg overzicht | 8 keer "Nog niet gekozen" | `testEmptyOverviewShowsAllStepsAsNotChosen` |
| TC-P-34 | Twee onderdelen die niet passen | Onderdelen en meldingen staan op het overzicht | `testChosenPartsAndCheckResultsAreShown` |
| TC-P-35 | Opslaan met naam | Doorsturen naar de build met die naam | `testSavingRedirectsToSavedBuild` |
| TC-P-36 | Naam met alleen spaties | 422 en melding | `testSavingWithoutNameGivesMessage` |
| TC-P-37 | Naam van 61 tekens | 422, naam blijft ingevuld | `testTooLongNameStaysInTheField` |
| TC-P-38 | Lege build | 422 en melding | `testEmptyBuildCannotBeSaved` |
| TC-P-39 | Opgeslagen build openen | Naam, code, onderdelen, prijs en controle | `testSavedBuildShowsNameCodePartsAndCheck` |
| TC-P-40 | Onbekende code | 404 "Geen build gevonden met deze code" | `testUnknownCodeGivesNotFoundPage` |
| TC-P-41 | Code invullen op de startpagina | Doorsturen naar de build | `testCodeFormRedirectsToBuild` |
| TC-P-42 | Leeg codeveld | Terug naar de startpagina | `testEmptyCodeFormGoesBackHome` |

### F-16 en F-17 Aanbeveling op de pagina

| ID | Invoer | Verwacht resultaat | Test |
|---|---|---|---|
| TC-P-43 | Formulier openen | Geen foutmelding | `testEmptyFormGivesNoError` |
| TC-P-44 | € 2.000 voor gamen | Onderdelen, reden en bedrag over | `testResultShowsPartsWithReasonAndLeftover` |
| TC-P-45 | Formulier verstuurd | Waarden blijven ingevuld | `testFormKeepsItsValues` |
| TC-P-46 | Leeg budget | 422 en melding | `testEmptyBudgetGivesMessage` |
| TC-P-47 | 0, -5, abc, 12.50 | Melding | `testInvalidBudgetsGiveMessage` |
| TC-P-48 | € 1.000 voor gamen | "Voor gamen heb je minimaal € 1.172 nodig." | `testBudgetBelowMinimumNamesTheMinimum` |
| TC-P-49 | Onbekend doel | 422 en melding | `testUnknownGoalGivesMessage` |

### NF-03 Escaping en algemene pagina's

| ID | Invoer | Verwacht resultaat | Test |
|---|---|---|---|
| TC-P-50 | `<script>` in een filter | Staat niet als code op de pagina | `testHtmlInFilterIsEscaped` |
| TC-P-51 | `<script>` als naam van een build | Wordt `&lt;script&gt;` | `testNameIsEscaped` |
| TC-P-52 | `<script>` als budget | Staat niet als code op de pagina | `testInputIsEscaped` |
| TC-P-53 | Startpagina | Beide keuzes en het veld voor de deelcode | `testHomePageShowsBothChoicesAndCodeField` |
| TC-P-54 | Startpagina | Taal, titel en link "Ga naar de inhoud" | `testPageHasLanguageTitleAndSkipLink` |
| TC-P-55 | Onbekende pagina | 404 als HTML | `testUnknownPageGivesHtmlNotFound` |
| TC-P-56 | Onbekend API-pad | Fout blijft JSON | `testApiPathStillGivesJsonError` |

## 5. Handmatige tests

| ID | Eis | Stappen | Verwacht resultaat |
|---|---|---|---|
| TC-H-01 | NF-01 | Alle pagina's openen op 360, 375, 1000, 1200 en 1920 pixels breed | Geen horizontaal scrollen. Kaartjes onder elkaar op een telefoon, naast elkaar op een desktop |
| TC-H-02 | NF-02 | Lighthouse draaien op de startpagina, een stap, het overzicht en de aanbeveling | Score voor toegankelijkheid van 90 of hoger |
| TC-H-03 | NF-02 | Contrast van alle kleurparen uit de CSS uitrekenen | Minimaal 4,5:1 |
| TC-H-04 | NF-02 | Met alleen Tab en Enter een onderdeel kiezen en een build opslaan | Alles is bereikbaar, de focus is zichtbaar, de focus blijft op het kaartje na het kiezen |
| TC-H-05 | NF-07 | De hele route doorlopen zonder JavaScript: acht onderdelen kiezen, filteren, verwijderen, opslaan, openen | Alles werkt en de totalen kloppen na elke stap |
| TC-H-06 | F-13 | Met JavaScript aan een onderdeel kiezen | Pagina herlaadt niet, totalen en kaartjes veranderen direct, geen fouten in de console |
| TC-H-07 | NF-08 | Tijd meten van alle pagina's en API-verzoeken, 5 keer per pagina | Binnen 1 seconde |
| TC-H-08 | NF-09 | Schone clone van GitHub, stappen uit de README volgen, `composer test` draaien | Site start, alle tests slagen |
| TC-H-09 | F-14, F-15 | In de browser een build opslaan en met de code openen | De build is hetzelfde |

## 6. Gebruikerstest

De gebruikerstest doe ik met 2 of 3 echte mensen, liefst iemand die weinig van PC's weet en iemand die er wel wat van weet. Ik kijk mee en help niet, tenzij iemand echt vastloopt. Ik schrijf op waar iemand twijfelt of iets niet begrijpt.

| ID | Opdracht | Geslaagd als | Bij eis |
|---|---|---|---|
| TC-G-01 | Stel een complete PC samen waarvan alle onderdelen passen | Build heeft status "In orde" | F-04, F-12 |
| TC-G-02 | Kies een processor van AMD onder de € 200 | Gebruikt de filters | F-03 |
| TC-G-03 | Zoek uit waarom een grijs onderdeel niet past en leg het uit in eigen woorden | Kan de reden noemen | F-12 |
| TC-G-04 | Hoeveel kost je build en hoeveel stroom verbruikt hij? | Vindt beide zonder hulp | F-13 |
| TC-G-05 | Sla je build op en open hem opnieuw met de code | Build is terug | F-14, F-15 |
| TC-G-06 | Vraag een build aan voor gamen met € 1.500. Waarom is de videokaart gekozen? | Vindt de reden | F-16, F-17 |
| TC-G-07 | Vraag een build aan met € 500 | Begrijpt de melding | F-16 |
| TC-G-08 | Doe opdracht 1 op een telefoon | Lukt zonder horizontaal scrollen | NF-01 |

Vragen na afloop:

1. Wat vond je het makkelijkst en wat het lastigst?
2. Was er een melding of woord dat je niet begreep?
3. Zou je deze site gebruiken als je een PC ging kopen? Waarom wel of niet?
4. Wat zou je veranderen?

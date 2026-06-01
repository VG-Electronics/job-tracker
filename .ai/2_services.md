Create the core services and repositories. Follow the best practices and use typing. Architecture and naming must be consistent.

## Services

ScrapperService (dependency injection of ScrapperInterface):
@getOffers($url) - uses ScrapperInterface instance to get result of offers listing from given url and prepares the offers data

AiService (dependency injection of AiApiInterface):
@prepareOffers($offers) - sends a prompt with offers to AiApiInterface, it does it in chunks of 10 offers. 
AI has to return JSON data ready to save in the database. It must calculate the salary in PLN currency.
AI must extract: title, company (hiring employer), recruitment_company (agency, if different from employer), description, url, min_salary, max_salary, salary_type, status.

## Interfaces

AiApiInterface (dependency injection):
@send($prompt)

ScrapperInterface (dependency injection):
@getOffers($url)

## API

/AI/OpenAiApi (implements AiApiInterface)

## Scrappers

Use a scrapper of your choice.

## Repositories

WebsitesRepository:
@getUrls

OffersRepository:
@getOffers($filters, $sorting, $limit) – returns the offers together with joined meetings and persons
@getExistingUrls($urls)

MeetingsRepository:
@getMeetings($filters, $sorting, $limit) – returns the meetings together with joined persons and offers

PersonsRepository:
@getPersons - returns the persons together with joined meetings and offers

## Jobs

FetchNewOffersJob:
1. Get urls from WebsitesRepository@getUrls
2. for every url call ScrapperService@getOffers($url, $limit)
3. for whole collection call OffersRepository@getExistingUrls($urls)
4. filter the results that already exist in the database
5. calls AiService@prepareOffers($offers)
6. Save the results in the database
Create the controllers and routes. All requests have to be separate FormRequest with validation.

## Controllers

OffersController (JsonResponse)
@get - returns latest offers from the database, supports pagination, sorting by date and salary and filtering by status and min_salary & salary_type
@fetchNewOffers - calls FetchNewOffersJob and returns new offers count
@update

MeetingsController (JsonResponse)
@get - returns latest meetings from the database, supports filtering by status and sorting by occurs_at date
@create - offerID and other required data
@update
@delete

PersonsController (JsonResponse)
@get - returns latest persons from the database
@create - optional $offerID
@update
@delete

WebsitesController (JsonResponse)
@get - returns latest websites from the database
@create
@update
@delete
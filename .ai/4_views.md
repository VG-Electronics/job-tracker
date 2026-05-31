Create the views for the application. Always split the views into smaller components.

Three tabs: Offers, Meetings, Persons, Settings

Offers:
1. Filters panel
    - sorting: price asc, price desc, created_at asc, created_at desc
    - filters: min salary with salary type, offer status
2. Refresh offers button - calls OffersController@fetchNewOffers, if returned count is greater than 0 then refresh the page (remember filters)
3. List of offers
    - Offers loaded by ajax, dynamic pagination on scroll
    - Offers are listed one by one
    - Offer card:
        - status
        - title
        - url (clickable, new tab)
        - created_at
        - salary
        - meeting info (if there is any assigned)
4. Clicking on an offer opens a modal window
    - Editing all details
    - Adding & assigning a person
    - Adding a meeting

Meetings:
1. Filtering by meeting status and sorting by date
2. List of meetings
3. Modal window with details & editing
4. No create button (it's in the offer card)

Persons:
1. List of persons with assigned offers
2. Modal window with details & editing
3. No create button (it's in the offer card)

Settings:
1. List of set websites
2. Adding a website to scrap
3. Editing & deleting a website
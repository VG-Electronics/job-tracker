## Database & Models

websites:
- id
- name
- url
- base_url
- created_at
- updated_at

persons:
- id
- name
- email
- phone
- role (enum PersonRole)
- linkedin_url
- created_at
- updated_at

offers_persons:
- offer_id
- person_id

offers:
- id
- status (enum OfferStatus)
- title
- description
- url
- min_salary
- max_salary
- salary_type (enum SalaryType)
- note
- created_at
- updated_at

meetings:
- id
- offer_id
- person_id
- occurs_at
- url
- title
- note
- created_at
- updated_at

## Enums

SalaryType:
- hourly
- monthly
- annually

OfferStatus:
- new
- ignored
- applied
- rejected
- interview
- offer

PersonRole:
- recruiter
- employee
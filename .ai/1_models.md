Create database structure & Eloquent models with relations + the enums. 

## Database & Models

websites:
- id
- name
- url
- created_at
- updated_at

persons:
- id
- name
- email (nullable)
- phone (nullable)
- role (enum PersonRole)
- linkedin_url (nullable)
- created_at
- updated_at

offers_persons:
- offer_id
- person_id

offers:
- id
- status (enum OfferStatus, default: new)
- title
- description (nullable)
- url
- min_salary (nullable)
- max_salary (nullbable)
- salary_type (enum SalaryType, nullable)
- note (nullable)
- created_at
- updated_at

meetings:
- id
- offer_id
- person_id
- occurs_at
- url (nullable)
- title
- note (nullable)
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
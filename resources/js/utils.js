export function fmtDate(str) {
  if (!str) return '—'
  return new Date(str).toLocaleDateString('pl-PL')
}

export function fmtDateTime(str) {
  if (!str) return '—'
  return new Date(str).toLocaleString('pl-PL', { dateStyle: 'short', timeStyle: 'short' })
}

export function toDateTimeLocal(str) {
  if (!str) return ''
  return str.slice(0, 16)
}

export function fmtSalary(offer) {
  if (!offer.min_salary && !offer.max_salary) return null
  const suffixes = { hourly: '/h', monthly: '/mies.', annually: '/rok' }
  const suffix = suffixes[offer.salary_type] ?? ''
  const min = offer.min_salary ? Number(offer.min_salary).toLocaleString('pl-PL') : null
  const max = offer.max_salary ? Number(offer.max_salary).toLocaleString('pl-PL') : null
  const range = [min, max].filter(Boolean).join(' – ')
  return `${range} zł${suffix}`
}

export const STATUS_LABELS = {
  new: 'Nowa',
  ignored: 'Ignorowana',
  interested: 'Zainteresowany',
  applied: 'Aplikowano',
  rejected: 'Odrzucona',
  interview: 'Rozmowa',
  offer: 'Oferta',
}

export const STATUS_CLASSES = {
  new: 'bg-blue-100 text-blue-700',
  ignored: 'bg-gray-100 text-gray-500',
  interested: 'bg-teal-100 text-teal-700',
  applied: 'bg-amber-100 text-amber-700',
  rejected: 'bg-red-100 text-red-700',
  interview: 'bg-purple-100 text-purple-700',
  offer: 'bg-green-100 text-green-700',
}

export const ROLE_LABELS = {
  recruiter: 'Rekruter',
  employee: 'Pracownik',
}

export const SALARY_TYPES = [
  { value: 'hourly', label: 'Godzinowe' },
  { value: 'monthly', label: 'Miesięczne' },
  { value: 'annually', label: 'Roczne' },
]

export const OFFER_STATUSES = Object.entries(STATUS_LABELS).map(([value, label]) => ({ value, label }))

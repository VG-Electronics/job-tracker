const BASE = '/api'

async function req(method, path, body) {
  const opts = {
    method,
    headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
  }
  if (body !== undefined) opts.body = JSON.stringify(body)
  const res = await fetch(BASE + path, opts)
  if (res.status === 204) return null
  const data = await res.json()
  if (!res.ok) throw data
  return data
}

export const api = {
  get(path, params = {}) {
    const clean = Object.fromEntries(
      Object.entries(params).filter(([, v]) => v != null && v !== '')
    )
    const qs = new URLSearchParams(clean).toString()
    return req('GET', qs ? `${path}?${qs}` : path)
  },
  post: (path, body) => req('POST', path, body),
  patch: (path, body) => req('PATCH', path, body),
  delete: (path) => req('DELETE', path),
}

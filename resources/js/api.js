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
    const qsParams = new URLSearchParams()
    Object.entries(params).forEach(([k, v]) => {
      if (v == null || v === '' || v === false) return
      if (Array.isArray(v)) {
        v.forEach(item => qsParams.append(`${k}[]`, item))
      } else {
        qsParams.append(k, typeof v === 'boolean' ? (v ? 1 : 0) : v)
      }
    })
    const qs = qsParams.toString()
    return req('GET', qs ? `${path}?${qs}` : path)
  },
  post: (path, body) => req('POST', path, body),
  patch: (path, body) => req('PATCH', path, body),
  delete: (path) => req('DELETE', path),
}

const API_BASE_URL = (import.meta.env.VITE_API_BASE_URL ?? 'http://127.0.0.1:8000').replace(/\/$/, '')
const SAFE_METHODS = ['GET', 'HEAD', 'OPTIONS']

let csrfPrimed = false
let csrfRequest = null

export class ApiError extends Error {
  constructor(message, status = 0, data = null) {
    super(message)
    this.name = 'ApiError'
    this.status = status
    this.data = data
  }
}

function buildUrl(path) {
  if (/^https?:\/\//.test(path)) {
    return path
  }

  return `${API_BASE_URL}${path.startsWith('/') ? path : `/${path}`}`
}

function readCookie(name) {
  if (typeof document === 'undefined') {
    return null
  }

  const cookie = document.cookie
    .split('; ')
    .find((entry) => entry.startsWith(`${name}=`))

  return cookie ? decodeURIComponent(cookie.split('=').slice(1).join('=')) : null
}

async function parseResponse(response) {
  if (response.status === 204) {
    return null
  }

  const contentType = response.headers.get('content-type') ?? ''

  if (contentType.includes('application/json')) {
    return response.json()
  }

  const text = await response.text()
  return text ? { message: text } : null
}

function firstValidationMessage(errors) {
  const bucket = Object.values(errors ?? {})[0]
  return Array.isArray(bucket) ? bucket[0] : null
}

function resolveErrorMessage(payload, fallback) {
  if (payload?.message) {
    return payload.message
  }

  const validationMessage = firstValidationMessage(payload?.errors)

  return validationMessage ?? fallback
}

async function ensureCsrfCookie(force = false) {
  if (csrfPrimed && !force) {
    return
  }

  if (!csrfRequest || force) {
    csrfRequest = fetch(buildUrl('/sanctum/csrf-cookie'), {
      method: 'GET',
      credentials: 'include',
      headers: {
        Accept: 'application/json',
      },
    })
      .then(async (response) => {
        if (!response.ok) {
          const payload = await parseResponse(response)
          throw new ApiError(
            resolveErrorMessage(payload, 'Unable to initialize the CSRF cookie.'),
            response.status,
            payload,
          )
        }

        csrfPrimed = true
      })
      .finally(() => {
        csrfRequest = null
      })
  }

  return csrfRequest
}

async function request(path, options = {}, retryOnCsrf = true) {
  const {
    method = 'GET',
    body,
    headers = {},
    csrf = false,
  } = options

  const upperMethod = method.toUpperCase()
  const shouldPrimeCsrf = csrf || !SAFE_METHODS.includes(upperMethod)

  if (shouldPrimeCsrf) {
    await ensureCsrfCookie()
  }

  const finalHeaders = {
    Accept: 'application/json',
    ...headers,
  }

  if (body !== undefined && !(body instanceof FormData)) {
    finalHeaders['Content-Type'] = 'application/json'
  }

  const xsrfToken = readCookie('XSRF-TOKEN')

  if (xsrfToken && !SAFE_METHODS.includes(upperMethod)) {
    finalHeaders['X-XSRF-TOKEN'] = xsrfToken
  }

  let response

  try {
    response = await fetch(buildUrl(path), {
      method: upperMethod,
      credentials: 'include',
      headers: finalHeaders,
      body:
        body === undefined
          ? undefined
          : body instanceof FormData
            ? body
            : JSON.stringify(body),
    })
  } catch (error) {
    throw new ApiError('Unable to reach the API. Check that Laravel is running and reachable.', 0, {
      cause: error,
    })
  }

  if (response.status === 419 && retryOnCsrf) {
    await ensureCsrfCookie(true)
    return request(path, options, false)
  }

  const payload = await parseResponse(response)

  if (!response.ok) {
    throw new ApiError(
      resolveErrorMessage(payload, response.statusText || 'The request failed.'),
      response.status,
      payload,
    )
  }

  return payload
}

export const api = {
  request,
  get(path, options = {}) {
    return request(path, { ...options, method: 'GET' })
  },
  post(path, body = {}, options = {}) {
    return request(path, { ...options, method: 'POST', body, csrf: true })
  },
  put(path, body = {}, options = {}) {
    return request(path, { ...options, method: 'PUT', body, csrf: true })
  },
  delete(path, options = {}) {
    return request(path, { ...options, method: 'DELETE', csrf: true })
  },
}

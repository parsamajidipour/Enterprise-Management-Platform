import type { FetchOptions } from 'ofetch'
import { ApiError } from '~/types/api'
import type { ApiResponse } from '~/types/api'

function buildHeaders(token?: string | null): Record<string, string> {
  const headers: Record<string, string> = {
    Accept: 'application/json',
  }
  if (token) {
    headers.Authorization = `Bearer ${token}`
  }
  return headers
}

async function doFetch<T>(
  path: string,
  baseURL: string,
  token: string | null | undefined,
  options: FetchOptions,
): Promise<ApiResponse<T>> {
  try {
    return await $fetch<ApiResponse<T>>(path, {
      baseURL,
      headers: {
        ...buildHeaders(token),
        ...(options.headers as Record<string, string> | undefined),
      },
      ...options,
    })
  }
  catch (err: unknown) {
    const raw = err as Record<string, unknown>
    const status = (raw?.response as Response | undefined)?.status ?? 500
    const body = raw?.data as Record<string, unknown> | undefined
    const message = (body?.message as string | undefined) ?? (raw?.message as string | undefined) ?? 'Request failed'
    const errors = body?.errors as Record<string, string[]> | undefined
    throw new ApiError(status, message, errors, raw)
  }
}

export function createHttp(baseURL: string, token?: string | null) {
  return {
    get: <T>(path: string, options: FetchOptions = {}) =>
      doFetch<T>(path, baseURL, token, { method: 'GET', ...options }),

    post: <T>(path: string, body?: unknown, options: FetchOptions = {}) =>
      doFetch<T>(path, baseURL, token, { method: 'POST', body, ...options }),

    put: <T>(path: string, body?: unknown, options: FetchOptions = {}) =>
      doFetch<T>(path, baseURL, token, { method: 'PUT', body, ...options }),

    patch: <T>(path: string, body?: unknown, options: FetchOptions = {}) =>
      doFetch<T>(path, baseURL, token, { method: 'PATCH', body, ...options }),

    delete: <T>(path: string, options: FetchOptions = {}) =>
      doFetch<T>(path, baseURL, token, { method: 'DELETE', ...options }),
  }
}

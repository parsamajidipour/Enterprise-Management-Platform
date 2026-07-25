import { createHttp } from './http'
import type { AuthUser, LoginResponse } from '~/types/auth'
import type { ApiResponse } from '~/types/api'

export function createAuthService(baseURL: string, token?: string | null) {
  const http = createHttp(baseURL, token)

  return {
    login(email: string, password: string): Promise<ApiResponse<LoginResponse>> {
      return http.post<LoginResponse>('/auth/login', { email, password })
    },

    logout(): Promise<ApiResponse<null>> {
      return http.post<null>('/auth/logout')
    },

    me(): Promise<ApiResponse<AuthUser>> {
      return http.get<AuthUser>('/auth/me')
    },
  }
}

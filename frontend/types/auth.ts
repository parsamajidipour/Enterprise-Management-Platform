export interface AuthUser {
  id: number
  name: string
  email: string
  phone: string | null
  is_active: boolean
  roles: string[]
  created_at: string
}

export interface LoginRequest {
  email: string
  password: string
}

export interface LoginResponse {
  token: string
  user: AuthUser
}

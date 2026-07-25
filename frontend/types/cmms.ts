import type { PaginatedData } from './api'

export interface CmmsHealth {
  system: string
  status: string
  mode: string
  authenticated: boolean
  checked_at: string
}

export interface CmmsSyncSummary {
  fetched: number
  created: number
  updated: number
  imported_ids: number[]
}

export interface CmmsSyncLog {
  id: number
  direction: string
  action: string
  external_id: string | null
  local_type: string | null
  local_id: number | null
  status: string
  request_payload: Record<string, unknown> | null
  response_payload: Record<string, unknown> | null
  error_message: string | null
  created_at: string | null
  updated_at: string | null
}

export type CmmsSyncLogPage = PaginatedData<CmmsSyncLog>

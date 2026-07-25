import { createHttp } from './http'
import type { ApiResponse } from '~/types/api'
import type { CmmsHealth, CmmsSyncLogPage, CmmsSyncSummary } from '~/types/cmms'

export function createCmmsService(baseURL: string, token?: string | null) {
  const http = createHttp(baseURL, token)

  return {
    health(): Promise<ApiResponse<CmmsHealth>> {
      return http.get<CmmsHealth>('/admin/cmms/health')
    },

    syncNow(): Promise<ApiResponse<CmmsSyncSummary>> {
      return http.post<CmmsSyncSummary>('/admin/cmms/sync-now')
    },

    logs(): Promise<ApiResponse<CmmsSyncLogPage>> {
      return http.get<CmmsSyncLogPage>('/admin/cmms/sync-logs', {
        query: { per_page: 10 },
      })
    },
  }
}

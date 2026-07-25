import { createHttp } from './http'
import type { ApiResponse, PaginatedData } from '~/types/api'
import type {
  AssignWorkOrderRequest,
  DispatchAssignment,
  DispatchRecommendation,
  Technician,
  TechnicianAvailability,
  TechnicianStatus,
} from '~/types/dispatch'

export function createDispatchService(baseURL: string, token?: string | null) {
  const http = createHttp(baseURL, token)

  return {
    getTechnicians(): Promise<ApiResponse<PaginatedData<Technician>>> {
      return http.get<PaginatedData<Technician>>('/admin/technicians')
    },

    getAvailability(): Promise<ApiResponse<TechnicianAvailability[]>> {
      return http.get<TechnicianAvailability[]>('/admin/technicians/availability')
    },

    getTechnicianStatus(): Promise<ApiResponse<TechnicianStatus[]>> {
      return http.get<TechnicianStatus[]>('/admin/technicians/status')
    },

    getRecommendations(workOrderId: number): Promise<ApiResponse<DispatchRecommendation[]>> {
      return http.get<DispatchRecommendation[]>(`/admin/work-orders/${workOrderId}/recommendations`)
    },

    getAssignments(status?: string): Promise<ApiResponse<PaginatedData<DispatchAssignment>>> {
      return http.get<PaginatedData<DispatchAssignment>>('/admin/assignments', {
        query: {
          ...(status ? { status } : {}),
          per_page: 50,
        },
      })
    },

    assignWorkOrder(
      workOrderId: number,
      technicianId: number,
      notes?: string,
    ): Promise<ApiResponse<DispatchAssignment>> {
      const payload: AssignWorkOrderRequest = { technician_id: technicianId }
      if (notes) payload.notes = notes

      return http.post<DispatchAssignment>(`/admin/work-orders/${workOrderId}/assign`, payload)
    },

    cancelAssignment(assignmentId: number, notes?: string): Promise<ApiResponse<DispatchAssignment>> {
      return http.post<DispatchAssignment>(`/admin/assignments/${assignmentId}/cancel`, { notes })
    },
  }
}

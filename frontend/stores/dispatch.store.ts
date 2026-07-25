import { defineStore } from 'pinia'
import { createDispatchService } from '~/services/dispatch.service'
import { ApiError } from '~/types/api'
import type {
  DispatchAssignment,
  DispatchRecommendation,
  Technician,
  TechnicianAvailability,
  TechnicianStatus,
} from '~/types/dispatch'

export const useDispatchStore = defineStore('dispatch', () => {
  const config = useRuntimeConfig()
  const authStore = useAuthStore()
  const baseURL = config.public.apiBase as string

  const technicians = ref<Technician[]>([])
  const availability = ref<TechnicianAvailability[]>([])
  const technicianStatus = ref<TechnicianStatus[]>([])
  const recommendations = ref<DispatchRecommendation[]>([])
  const assignments = ref<DispatchAssignment[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)
  const selectedWorkOrderId = ref<number | null>(null)

  function service() {
    return createDispatchService(baseURL, authStore.token)
  }

  function setError(err: unknown) {
    if (err instanceof ApiError) {
      const validationMessage = err.errors ? Object.values(err.errors).flat().find(Boolean) : null
      error.value = validationMessage ?? err.message
      return
    }

    error.value = 'Request failed'
  }

  async function fetchTechnicians(): Promise<void> {
    loading.value = true
    error.value = null
    try {
      const res = await service().getTechnicians()
      technicians.value = res.data.data
    }
    catch (err) {
      setError(err)
    }
    finally {
      loading.value = false
    }
  }

  async function fetchAvailability(): Promise<void> {
    loading.value = true
    error.value = null
    try {
      const res = await service().getAvailability()
      availability.value = res.data
    }
    catch (err) {
      setError(err)
    }
    finally {
      loading.value = false
    }
  }

  async function fetchTechnicianStatus(): Promise<void> {
    loading.value = true
    error.value = null
    try {
      const res = await service().getTechnicianStatus()
      technicianStatus.value = res.data
    }
    catch (err) {
      setError(err)
    }
    finally {
      loading.value = false
    }
  }

  async function fetchRecommendations(workOrderId: number): Promise<void> {
    loading.value = true
    error.value = null
    selectedWorkOrderId.value = workOrderId
    try {
      const res = await service().getRecommendations(workOrderId)
      recommendations.value = res.data
    }
    catch (err) {
      recommendations.value = []
      setError(err)
    }
    finally {
      loading.value = false
    }
  }

  async function fetchAssignments(status?: string): Promise<void> {
    loading.value = true
    error.value = null
    try {
      const res = await service().getAssignments(status)
      assignments.value = res.data.data
    }
    catch (err) {
      setError(err)
    }
    finally {
      loading.value = false
    }
  }

  async function assign(workOrderId: number, technicianId: number, notes?: string): Promise<DispatchAssignment | null> {
    if (loading.value) return null

    loading.value = true
    error.value = null
    try {
      const res = await service().assignWorkOrder(workOrderId, technicianId, notes)
      return res.data
    }
    catch (err) {
      setError(err)
      return null
    }
    finally {
      loading.value = false
    }
  }

  async function cancel(assignmentId: number, notes?: string): Promise<DispatchAssignment | null> {
    if (loading.value) return null

    loading.value = true
    error.value = null
    try {
      const res = await service().cancelAssignment(assignmentId, notes)
      return res.data
    }
    catch (err) {
      setError(err)
      return null
    }
    finally {
      loading.value = false
    }
  }

  return {
    technicians,
    availability,
    technicianStatus,
    recommendations,
    assignments,
    loading,
    error,
    selectedWorkOrderId,
    fetchTechnicians,
    fetchAvailability,
    fetchTechnicianStatus,
    fetchRecommendations,
    fetchAssignments,
    assign,
    cancel,
  }
})

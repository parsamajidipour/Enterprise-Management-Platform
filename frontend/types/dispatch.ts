import type { AuthUser } from './auth'

export interface TechnicianProfile {
  id: number
  employee_code: string | null
  skills: string[] | null
  phone: string | null
  default_area: string | null
  is_active: boolean
  created_at: string | null
  updated_at: string | null
}

export interface TechnicianLocation {
  id: number
  technician_id: number
  latitude: string | number
  longitude: string | number
  accuracy: string | number | null
  captured_at: string | null
  created_at: string | null
  updated_at: string | null
}

export interface Technician {
  id: number
  name: string
  email: string
  phone: string | null
  is_active: boolean
  profile?: TechnicianProfile | null
  latest_location?: TechnicianLocation | null
  active_jobs_count?: number
  created_at?: string | null
  updated_at?: string | null
}

export interface TechnicianAvailability {
  technician: Technician
  availability: 'available' | 'busy' | string
  active_jobs_count: number
  latest_location: TechnicianLocation | null
}

export interface DispatchRecommendation {
  technician: Technician
  availability: 'available' | 'busy' | string
  distance_km: number | null
  active_jobs_count: number
  score: number
  reason: string
}

export interface DispatchAssignment {
  id: number
  status: string
  assigned_at: string | null
  accepted_at: string | null
  arrived_at: string | null
  started_at: string | null
  completed_at: string | null
  cancelled_at: string | null
  notes: string | null
  work_order?: WorkOrderSummary | null
  technician?: AuthUser | Technician | null
  assigned_by?: AuthUser | null
  created_at: string | null
  updated_at: string | null
}

export interface AssignWorkOrderRequest {
  technician_id: number
  notes?: string
}

export interface TechnicianCurrentAssignment {
  id: number
  status: string
  assigned_at: string | null
  accepted_at: string | null
  arrived_at: string | null
  started_at: string | null
  completed_at: string | null
}

export interface TechnicianCurrentJob {
  id: number
  title: string
  priority: string
  status: string
  external_id: string | null
  outage_type: string | null
}

export interface TechnicianLastCompletedJob {
  id: number
  title: string
  completed_at: string | null
}

export interface TechnicianLastLocation {
  latitude: string | number
  longitude: string | number
  captured_at: string | null
}

export interface TechnicianStatus {
  id: number
  name: string
  email: string
  employee_code: string | null
  phone: string | null
  skills: string[]
  availability: 'available' | 'assigned' | 'accepted' | 'on_the_way' | 'arrived' | 'in_progress' | 'unavailable' | string
  human_status_label: string
  current_assignment: TechnicianCurrentAssignment | null
  current_job: TechnicianCurrentJob | null
  last_completed_job: TechnicianLastCompletedJob | null
  last_location: TechnicianLastLocation | null
  last_activity_at: string | null
}

export interface WorkOrderSummary {
  id: number
  title: string
  description: string | null
  status: string
  priority: string
  source?: string | null
  external_id?: string | null
  external_reference?: string | null
  outage_type?: string | null
  reported_at?: string | null
  cmms_status?: string | null
  latitude?: string | number | null
  longitude?: string | number | null
  scheduled_at: string | null
  completed_at: string | null
  asset?: {
    id?: number
    name?: string
    code?: string
    location?: string | null
  } | null
  assignee?: AuthUser | null
  created_at?: string | null
  updated_at?: string | null
}

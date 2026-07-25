import { tx, type TranslatableText } from '~/composables/useLanguage'

export type NavItem = {
  title: TranslatableText
  to: string
  icon: string
}

export const navItems: NavItem[] = [
  { title: tx('nav.dashboard', 'Dashboard'), to: '/', icon: 'dashboard' },
  { title: tx('nav.work_orders', 'Work Orders'), to: '/work-orders', icon: 'work-orders' },
  { title: tx('nav.dispatch', 'Dispatch'), to: '/dispatch', icon: 'dispatch' },
  { title: tx('nav.technicians', 'Technicians'), to: '/technicians', icon: 'users' },
  { title: tx('nav.integrations', 'Integrations'), to: '/integrations', icon: 'integrations' },
]

// Future API records should provide stable keys for controlled values, e.g.
// status_key: "status.in_progress" or role_key: "role.field_inspector".
// Map those keys to tx(status_key, fallback_label) at the frontend boundary.
// Free-text content such as user names, emails, IDs, descriptions, endpoints,
// coordinates, and asset codes should stay plain strings unless the backend
// explicitly stores localized variants.

export const kpis = [
  { title: 'Inspection Completion', value: '96.4%', target: '>= 95%', trend: '+3.2%', icon: '96', tone: 'green' },
  { title: 'WO Cycle Time', value: '2.6d', target: '<= 3d', trend: '-0.4d', icon: '2.6', tone: 'blue' },
  { title: 'Defect Response', value: '18.5h', target: '<= 24h', trend: '-5.5h', icon: '18', tone: 'orange' },
  { title: 'Data Quality', value: '98.8%', target: '>= 98%', trend: '+1.1%', icon: '99', tone: 'green' }
]

export const workOrders = [
  { id: 'WO-2026-1001', asset: 'TR-132-A', type: tx('work_type.monthly_transformer_inspection', 'Monthly Transformer Inspection'), priority: tx('priority.critical', 'Critical'), team: tx('team.alpha', 'Team Alpha'), assignee: 'Alex Morgan', status: tx('status.in_progress', 'In Progress'), due: '2026-05-28', location: 'North Grid Station' },
  { id: 'WO-2026-1002', asset: 'CB-220-07', type: tx('work_type.sf6_pressure_check', 'SF6 Pressure Check'), priority: tx('priority.high', 'High'), team: tx('team.beta', 'Team Beta'), assignee: 'Sara Chen', status: tx('status.assigned', 'Assigned'), due: '2026-05-29', location: 'Westfield Substation' },
  { id: 'WO-2026-1003', asset: 'OHL-400-12', type: tx('work_type.route_patrol', 'Route Patrol'), priority: tx('priority.high', 'High'), team: tx('team.line_crew_2', 'Line Crew 2'), assignee: 'Line Crew 2', status: tx('status.offline_saved', 'Offline Saved'), due: '2026-05-30', location: 'Eastgate Corridor' },
  { id: 'WO-2026-1004', asset: 'PR-88', type: tx('work_type.annual_relay_test', 'Annual Relay Test'), priority: tx('priority.critical', 'Critical'), team: tx('team.protection', 'Protection Team'), assignee: 'Protection Team', status: tx('status.completed', 'Completed'), due: '2026-05-24', location: 'Harborview Station' },
  { id: 'WO-2026-1005', asset: 'RTU-19', type: tx('work_type.communication_check', 'Communication Check'), priority: tx('priority.medium', 'Medium'), team: tx('team.scada', 'SCADA Team'), assignee: 'Priya Patel', status: tx('status.pending_sync', 'Pending Sync'), due: '2026-05-31', location: 'Riverside Station' }
]

export const assets = [
  { id: 'TR-132-A', name: 'Power Transformer', category: tx('asset_category.power_transformers', 'Power Transformers'), health: 91, barcode: 'QR-TR-132-A', gis: '40.7128, -74.0060', inspections: 24, defects: 1, status: tx('status.healthy', 'Healthy') },
  { id: 'CB-220-07', name: 'Circuit Breaker', category: tx('asset_category.circuit_breakers', 'Circuit Breakers'), health: 82, barcode: 'QR-CB-220-07', gis: '40.7484, -73.9857', inspections: 18, defects: 3, status: tx('status.review_required', 'Review Required') },
  { id: 'OHL-400-12', name: 'Overhead Line Segment', category: tx('asset_category.overhead_lines', 'Overhead Lines'), health: 76, barcode: 'QR-OHL-400-12', gis: '40.6892, -74.0445', inspections: 31, defects: 5, status: tx('status.attention', 'Attention') },
  { id: 'PR-88', name: 'Protection Relay', category: tx('asset_category.protection_control', 'Protection & Control'), health: 94, barcode: 'QR-PR-88', gis: '40.7580, -73.9855', inspections: 12, defects: 0, status: tx('status.healthy', 'Healthy') },
  { id: 'RTU-19', name: 'Remote Terminal Unit', category: tx('asset_category.protection_control', 'Protection & Control'), health: 88, barcode: 'QR-RTU-19', gis: '40.7061, -74.0087', inspections: 15, defects: 1, status: tx('status.healthy', 'Healthy') }
]

export const assetCategories = [
  { category: tx('asset_category.power_transformers', 'Power Transformers'), frequency: tx('frequency.monthly_annually', 'Monthly / Annually'), priority: tx('priority.critical', 'Critical'), forms: 4 },
  { category: tx('asset_category.circuit_breakers', 'Circuit Breakers'), frequency: tx('frequency.monthly_3_yearly', 'Monthly / 3-Yearly'), priority: tx('priority.critical', 'Critical'), forms: 3 },
  { category: tx('asset_category.overhead_lines', 'Overhead Lines'), frequency: tx('frequency.quarterly_annually', 'Quarterly / Annually'), priority: tx('priority.high', 'High'), forms: 2 },
  { category: tx('asset_category.protection_control', 'Protection & Control'), frequency: tx('frequency.annually_3_yearly', 'Annually / 3-Yearly'), priority: tx('priority.critical', 'Critical'), forms: 5 },
  { category: tx('asset_category.substation_civil', 'Substation Civil'), frequency: tx('frequency.bi_annually', 'Bi-Annually'), priority: tx('priority.medium', 'Medium'), forms: 2 }
]

export const inspections = [
  { id: 'INS-7781', form: tx('template.transformer_inspection', 'Transformer Inspection'), asset: 'TR-132-A', inspector: 'Alex Morgan', score: 94, status: tx('status.submitted', 'Submitted'), evidence: 8, sync: tx('status.synced', 'Synced') },
  { id: 'INS-7782', form: tx('template.circuit_breaker_sf6', 'Circuit Breaker SF6'), asset: 'CB-220-07', inspector: 'Sara Chen', score: 81, status: tx('status.review_required', 'Review Required'), evidence: 12, sync: tx('status.synced', 'Synced') },
  { id: 'INS-7783', form: tx('template.ohl_patrol', 'OHL Patrol'), asset: 'OHL-400-12', inspector: 'Line Crew 2', score: 77, status: tx('status.offline_draft', 'Offline Draft'), evidence: 5, sync: tx('status.queued', 'Queued') },
  { id: 'INS-7784', form: tx('template.relay_test', 'Relay Test'), asset: 'PR-88', inspector: 'Protection Team', score: 96, status: tx('status.approved', 'Approved'), evidence: 4, sync: tx('status.synced', 'Synced') }
]

export const formTemplates = [
  { name: tx('template.transformer_inspection', 'Transformer Inspection'), version: 'v1.4', fields: 42, rules: 18, status: tx('status.active', 'Active') },
  { name: tx('template.circuit_breaker_sf6', 'Circuit Breaker SF6'), version: 'v1.2', fields: 31, rules: 12, status: tx('status.active', 'Active') },
  { name: tx('template.ohl_patrol', 'OHL Patrol'), version: 'v1.1', fields: 26, rules: 9, status: tx('status.draft', 'Draft') },
  { name: tx('template.relay_test', 'Relay Test'), version: 'v1.0', fields: 37, rules: 14, status: tx('status.active', 'Active') }
]

export const integrationMatrix = [
  { system: 'CMMS', direction: tx('integration.direction.bidirectional', 'Bidirectional'), protocol: tx('integration.protocol.rest_api', 'REST API'), frequency: tx('frequency.real_time', 'Real-time'), status: tx('status.connected', 'Connected') },
  { system: 'GIS', direction: tx('integration.direction.read_write', 'Read / Write'), protocol: tx('integration.protocol.rest_api_wfs', 'REST API / WFS'), frequency: tx('frequency.on_demand', 'On-demand'), status: tx('status.connected', 'Connected') },
  { system: 'OLCM', direction: tx('integration.direction.write_push', 'Write Push'), protocol: tx('integration.protocol.rest_api', 'REST API'), frequency: tx('frequency.post_inspection', 'Post-inspection'), status: tx('status.monitoring', 'Monitoring') },
  { system: 'Barcode Master', direction: tx('integration.direction.read', 'Read'), protocol: tx('integration.protocol.local_scan_rest', 'Local Scan / REST'), frequency: tx('frequency.on_scan', 'On scan'), status: tx('status.connected', 'Connected') },
  { system: 'BI Dashboard', direction: tx('integration.direction.read_pull', 'Read Pull'), protocol: tx('integration.protocol.db_view_api', 'DB View / API'), frequency: tx('frequency.scheduled', 'Scheduled'), status: tx('status.connected', 'Connected') }
]

export const apiEndpoints = [
  { endpoint: '/api/workorders', method: 'GET', description: 'Retrieve open tasks for crew or asset', target: 'CMMS' },
  { endpoint: '/api/workorders/{id}/status', method: 'PUT', description: 'Update execution progression', target: 'CMMS' },
  { endpoint: '/api/assets/{barcode}', method: 'GET', description: 'Resolve asset data upon scan', target: 'Master DB' },
  { endpoint: '/api/inspections', method: 'POST', description: 'Submit verified inspection payload', target: 'Platform DB' },
  { endpoint: '/api/olcm/condition', method: 'POST', description: 'Push health indices and ratings', target: 'OLCM' }
]

export const users = [
  { name: 'Alex Morgan', email: 'alex@example.com', role: tx('role.field_inspector', 'Field Inspector'), team: tx('team.alpha', 'Team Alpha'), status: tx('status.active', 'Active') },
  { name: 'Sara Chen', email: 'sara@example.com', role: tx('role.field_supervisor', 'Field Supervisor'), team: tx('team.beta', 'Team Beta'), status: tx('status.active', 'Active') },
  { name: 'Priya Patel', email: 'priya@example.com', role: tx('role.system_admin', 'System Admin'), team: tx('team.it_operations', 'IT Operations'), status: tx('status.active', 'Active') },
  { name: 'Protection Team', email: 'protection@example.com', role: tx('role.specialist_group', 'Specialist Group'), team: tx('team.protection_department', 'Protection'), status: tx('status.active', 'Active') }
]

export const roles = [
  { role: tx('role.system_admin', 'System Admin'), users: 3, access: 'All modules, configuration, user provisioning' },
  { role: tx('role.field_supervisor', 'Field Supervisor'), users: 12, access: 'Dispatch, review, approve, exception handling' },
  { role: tx('role.field_inspector', 'Field Inspector'), users: 48, access: 'Mobile tasks, forms, evidence capture, offline sync' },
  { role: tx('role.asset_manager', 'Asset Manager'), users: 8, access: 'Dashboards, asset health, OLCM condition review' }
]

export const settings = [
  { key: tx('setting.key.offline_sync_interval', 'Offline sync interval'), value: tx('setting.value.15_minutes', '15 minutes'), owner: tx('setting.owner.mobile_platform', 'Mobile Platform') },
  { key: tx('setting.key.mandatory_gps_evidence', 'Mandatory GPS evidence'), value: tx('setting.value.enabled', 'Enabled'), owner: tx('setting.owner.forms_engine', 'Forms Engine') },
  { key: tx('setting.key.mfa_enforcement', 'MFA enforcement'), value: tx('setting.value.enabled', 'Enabled'), owner: tx('setting.owner.identity_access', 'Identity & Access') },
  { key: tx('setting.key.critical_defect_sla', 'Critical defect SLA'), value: tx('setting.value.24_hours', '24 hours'), owner: tx('setting.owner.workflow_engine', 'Workflow Engine') },
  { key: tx('setting.key.cmms_closure_mode', 'CMMS closure mode'), value: tx('setting.value.supervisor_approval', 'Supervisor approval'), owner: tx('setting.owner.integration_gateway', 'Integration Gateway') }
]

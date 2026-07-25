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

// TODO(phase 4): assets/assetCategories and formTemplates/inspections are still
// mock data consumed by assets.vue and inspections.vue. These are slated to be
// replaced by real backend-backed data; this file should be deleted once that
// lands, not kept around as a fallback.

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

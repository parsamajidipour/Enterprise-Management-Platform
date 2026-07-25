type Locale = 'en' | 'ar'

const locale = ref<Locale>('en')

export type TranslatableText = {
  key: string
  fallback: string
}

// Use tx() for controlled values that can come from future APIs, such as
// status_key, role_key, priority_key, and category_key. Keep free text plain.
export function tx(key: string, fallback: string): TranslatableText {
  return { key, fallback }
}

function isTranslatableText(value: unknown): value is TranslatableText {
  return Boolean(
    value &&
    typeof value === 'object' &&
    'key' in value &&
    'fallback' in value &&
    typeof (value as TranslatableText).key === 'string' &&
    typeof (value as TranslatableText).fallback === 'string'
  )
}

const translations: Record<string, string> = {
  'nav.dashboard': 'لوحة التحكم',
  'nav.work_orders': 'أوامر العمل',
  'nav.inspections': 'الفحوصات',
  'nav.assets': 'الأصول',
  'nav.integrations': 'التكاملات',
  'nav.users_roles': 'المستخدمون والصلاحيات',
  'nav.settings': 'الإعدادات',

  'status.active': 'نشط',
  'status.assigned': 'مسند',
  'status.in_progress': 'قيد التنفيذ',
  'status.completed': 'مكتمل',
  'status.submitted': 'مرسل',
  'status.approved': 'معتمد',
  'status.synced': 'متزامن',
  'status.connected': 'متصل',
  'status.healthy': 'سليم',
  'status.monitoring': 'قيد المراقبة',
  'status.draft': 'مسودة',
  'status.queued': 'في الانتظار',
  'status.attention': 'بحاجة لانتباه',
  'status.review_required': 'يتطلب مراجعة',
  'status.offline_saved': 'محفوظ دون اتصال',
  'status.offline_draft': 'مسودة دون اتصال',
  'status.pending_sync': 'بانتظار المزامنة',

  'priority.critical': 'حرج',
  'priority.high': 'مرتفع',
  'priority.medium': 'متوسط',
  'priority.low': 'منخفض',

  'role.field_inspector': 'مفتش ميداني',
  'role.field_supervisor': 'مشرف ميداني',
  'role.system_admin': 'مسؤول النظام',
  'role.specialist_group': 'مجموعة متخصصة',
  'role.asset_manager': 'مدير الأصول',

  'team.alpha': 'الفريق ألفا',
  'team.beta': 'الفريق بيتا',
  'team.line_crew_2': 'فريق الخطوط 2',
  'team.protection': 'فريق الحماية',
  'team.scada': 'فريق سكادا',
  'team.it_operations': 'عمليات تقنية المعلومات',
  'team.protection_department': 'الحماية',

  'asset_category.power_transformers': 'محولات القدرة',
  'asset_category.circuit_breakers': 'قواطع الدائرة',
  'asset_category.overhead_lines': 'الخطوط الهوائية',
  'asset_category.protection_control': 'الحماية والتحكم',
  'asset_category.substation_civil': 'الأعمال المدنية للمحطات',

  'frequency.monthly_annually': 'شهري / سنوي',
  'frequency.monthly_3_yearly': 'شهري / كل 3 سنوات',
  'frequency.quarterly_annually': 'ربع سنوي / سنوي',
  'frequency.annually_3_yearly': 'سنوي / كل 3 سنوات',
  'frequency.bi_annually': 'نصف سنوي',
  'frequency.real_time': 'فوري',
  'frequency.on_demand': 'عند الطلب',
  'frequency.post_inspection': 'بعد الفحص',
  'frequency.on_scan': 'عند المسح',
  'frequency.scheduled': 'مجدول',

  'work_type.monthly_transformer_inspection': 'فحص شهري للمحول',
  'work_type.sf6_pressure_check': 'فحص ضغط SF6',
  'work_type.route_patrol': 'دورية مسار',
  'work_type.annual_relay_test': 'اختبار سنوي للمرحل',
  'work_type.communication_check': 'فحص الاتصالات',

  'template.transformer_inspection': 'فحص المحول',
  'template.circuit_breaker_sf6': 'فحص SF6 لقاطع الدائرة',
  'template.ohl_patrol': 'دورية الخط الهوائي',
  'template.relay_test': 'اختبار المرحل',

  'integration.direction.bidirectional': 'ثنائي الاتجاه',
  'integration.direction.read_write': 'قراءة / كتابة',
  'integration.direction.write_push': 'دفع كتابي',
  'integration.direction.read': 'قراءة',
  'integration.direction.read_pull': 'سحب قراءة',
  'integration.protocol.rest_api': 'واجهة REST',
  'integration.protocol.rest_api_wfs': 'واجهة REST / WFS',
  'integration.protocol.local_scan_rest': 'مسح محلي / REST',
  'integration.protocol.db_view_api': 'عرض قاعدة بيانات / API',
  'setting.value.enabled': 'مفعل',
  'setting.value.15_minutes': '15 دقيقة',
  'setting.value.24_hours': '24 ساعة',
  'setting.value.supervisor_approval': 'اعتماد المشرف',
  'setting.key.offline_sync_interval': 'فاصل المزامنة دون اتصال',
  'setting.key.mandatory_gps_evidence': 'إلزامية دليل الموقع',
  'setting.key.mfa_enforcement': 'تطبيق المصادقة متعددة العوامل',
  'setting.key.critical_defect_sla': 'اتفاقية مستوى الخدمة للعيوب الحرجة',
  'setting.key.cmms_closure_mode': 'نمط إغلاق CMMS',
  'setting.owner.mobile_platform': 'منصة الهاتف المحمول',
  'setting.owner.forms_engine': 'محرك النماذج',
  'setting.owner.identity_access': 'الهوية والوصول',
  'setting.owner.workflow_engine': 'محرك سير العمل',
  'setting.owner.integration_gateway': 'بوابة التكامل',

  Dashboard: 'لوحة التحكم',
  'Work Orders': 'أوامر العمل',
  Inspections: 'الفحوصات',
  Assets: 'الأصول',
  Integrations: 'التكاملات',
  'Users & Roles': 'المستخدمون والصلاحيات',
  Settings: 'الإعدادات',
  'Enterprise Management Platform': 'منصة إدارة المؤسسات',
  EMP: 'المنصة',
  'Inspection Admin': 'إدارة الفحوصات',
  'Digital Field Work Management': 'إدارة العمل الميداني الرقمية',
  'Admin scope': 'نطاق الإدارة',
  'Manage work orders, forms, assets, integrations, roles, and platform settings.': 'إدارة أوامر العمل والنماذج والأصول والتكاملات والصلاحيات وإعدادات المنصة.',
  'Print / PDF': 'طباعة / PDF',
  'Admin Console': 'لوحة الإدارة',
  Arabic: 'العربية',
  English: 'English',
  Target: 'الهدف',

  'Inspection Completion': 'إنجاز الفحوصات',
  'WO Cycle Time': 'زمن دورة أمر العمل',
  'Defect Response': 'الاستجابة للعيوب',
  'Data Quality': 'جودة البيانات',
  'Daily queue': 'قائمة اليوم',
  'Active field work': 'عمل ميداني نشط',
  Low: 'منخفض',
  'Total Queue': 'إجمالي القائمة',
  'In Progress': 'قيد التنفيذ',
  'Completed Today': 'مكتمل اليوم',
  'Pending Sync': 'بانتظار المزامنة',

  'Operations Queue': 'قائمة العمليات',
  'Current CMMS work orders and field execution state': 'أوامر العمل الحالية من نظام CMMS وحالة التنفيذ الميداني',
  'Open WOs': 'أوامر مفتوحة',
  'Offline drafts': 'مسودات دون اتصال',
  'Critical defects': 'عيوب حرجة',
  'Teams active': 'فرق نشطة',
  'Asset Health': 'صحة الأصول',
  'Condition index from submitted smart forms': 'مؤشر الحالة من النماذج الذكية المرسلة',
  'Health index': 'مؤشر الصحة',
  'Health Index': 'مؤشر الصحة',
  'Sync & Integration Status': 'حالة المزامنة والتكامل',
  'Enterprise systems required by the proposal': 'أنظمة المؤسسة المطلوبة حسب المقترح',
  'Admin Priorities': 'أولويات الإدارة',
  'Configuration areas that control the field platform': 'مناطق الإعداد التي تتحكم في منصة العمل الميداني',
  'Review smart forms': 'مراجعة النماذج الذكية',
  'Maintain mandatory fields, pass/fail logic, photo evidence, and scoring rules.': 'إدارة الحقول الإلزامية ومنطق النجاح والفشل والأدلة المصورة وقواعد التقييم.',
  'Assign field work': 'إسناد العمل الميداني',
  'Dispatch CMMS work orders and monitor completion by crew, status, and priority.': 'توزيع أوامر العمل من CMMS ومتابعة الإنجاز حسب الفريق والحالة والأولوية.',
  'Validate integrations': 'التحقق من التكاملات',
  'Check CMMS, GIS, OLCM, barcode, and BI API readiness.': 'فحص جاهزية واجهات CMMS وGIS وOLCM والباركود ولوحات BI.',
  'Manage access': 'إدارة الوصول',
  'Provision users with MFA and role-based permissions.': 'إعداد المستخدمين مع المصادقة متعددة العوامل والصلاحيات حسب الدور.',

  WO: 'أمر العمل',
  'Work Order': 'أمر العمل',
  Asset: 'الأصل',
  Priority: 'الأولوية',
  Team: 'الفريق',
  Status: 'الحالة',
  Due: 'الاستحقاق',
  Type: 'النوع',
  Assignee: 'المسؤول',
  'Due Date': 'تاريخ الاستحقاق',
  Location: 'الموقع',
  System: 'النظام',
  Frequency: 'التكرار',
  Template: 'القالب',
  Version: 'الإصدار',
  Fields: 'الحقول',
  Rules: 'القواعد',
  Inspection: 'الفحص',
  Form: 'النموذج',
  Inspector: 'المفتش',
  Score: 'النتيجة',
  Sync: 'المزامنة',
  Name: 'الاسم',
  Category: 'الفئة',
  Health: 'الصحة',
  Barcode: 'الباركود',
  'GIS Location': 'موقع GIS',
  Defects: 'العيوب',
  'Asset ID': 'معرف الأصل',
  'Asset Category': 'فئة الأصل',
  'Inspection Frequency': 'تكرار الفحص',
  'Form Templates': 'قوالب النماذج',
  Direction: 'الاتجاه',
  Protocol: 'البروتوكول',
  Endpoint: 'المسار',
  Method: 'الطريقة',
  Description: 'الوصف',
  'System Target': 'النظام المستهدف',
  Email: 'البريد الإلكتروني',
  Role: 'الدور',
  Users: 'المستخدمون',
  Access: 'الصلاحيات',
  Setting: 'الإعداد',
  Value: 'القيمة',
  Owner: 'المالك',

  'Work Order Administration': 'إدارة أوامر العمل',
  'Assign, schedule, and track field tasks synchronized from CMMS': 'إسناد وجدولة ومتابعة المهام الميدانية المتزامنة من CMMS',
  'Search work orders, assets, teams...': 'البحث في أوامر العمل أو الأصول أو الفرق...',
  'All statuses': 'كل الحالات',
  'All priorities': 'كل الأولويات',

  'Smart Form Templates': 'قوالب النماذج الذكية',
  'Admin library for inspection forms, validation rules, and required evidence': 'مكتبة إدارية لنماذج الفحص وقواعد التحقق والأدلة المطلوبة',
  'Transformer Inspection Rule Set': 'مجموعة قواعد فحص المحول',
  'Asset ID / Name': 'معرف الأصل / الاسم',
  'Auto-filled from barcode scan': 'تعبئة تلقائية من مسح الباركود',
  'Inspector / Date': 'المفتش / التاريخ',
  'Auto-filled from user profile': 'تعبئة تلقائية من ملف المستخدم',
  'Oil Condition': 'حالة الزيت',
  'Dropdown rating 1-5': 'قائمة تقييم من 1 إلى 5',
  Temperature: 'درجة الحرارة',
  'Numeric range 0-120 C': 'نطاق رقمي من 0 إلى 120 م',
  'Defect Identified': 'تم تحديد عيب',
  'Require description and photo': 'يتطلب وصفا وصورة',
  'Inspector Signature': 'توقيع المفتش',
  'Digital signature required': 'يتطلب توقيعا رقميا',
  'Inspection Records': 'سجلات الفحص',
  'Submitted, queued, and review-required field inspections': 'الفحوصات الميدانية المرسلة أو المنتظرة أو التي تتطلب مراجعة',
  'Validation Rules Passed': 'قواعد التحقق المجتازة',
  'Latest submitted inspection': 'آخر فحص مرسل',
  'Evidence Completeness': 'اكتمال الأدلة',
  'Photos, GPS, signature': 'الصور والموقع والتوقيع',

  'Asset Master Registry': 'سجل الأصول الرئيسي',
  'Barcode, GIS location, inspection counts, defects, and condition index': 'الباركود وموقع GIS وعدد الفحوصات والعيوب ومؤشر الحالة',
  'Condition Index': 'مؤشر الحالة',
  'Asset Categories': 'فئات الأصول',
  'Initial scope from the proposal; update frequencies as platform policies change': 'النطاق الأولي من المقترح؛ يتم تحديث التكرار عند تغير سياسات الشركة',

  'Enterprise Integrations': 'تكاملات المؤسسة',
  'CMMS, GIS, OLCM, barcode master, and BI connections': 'اتصالات CMMS وGIS وOLCM وسجل الباركود وBI',
  'Integration Health': 'صحة التكامل',
  'API Endpoint Registry': 'سجل مسارات API',
  'Core endpoints from the proposal appendix': 'المسارات الأساسية من ملحق المقترح',
  'Payload Preview': 'معاينة الحمولة',

  'Provision field inspectors, supervisors, asset managers, and administrators': 'إعداد المفتشين والمشرفين ومديري الأصول ومسؤولي النظام',
  'Roles & Access': 'الأدوار والصلاحيات',
  'RBAC structure aligned with proposal security requirements': 'هيكل صلاحيات RBAC المتوافق مع متطلبات الأمان في المقترح',

  'Platform Settings': 'إعدادات المنصة',
  'Operational defaults administrators can adjust later': 'الإعدادات التشغيلية الافتراضية التي يمكن للمسؤولين تعديلها لاحقا',
  'Security Controls': 'ضوابط الأمان',
  'Baseline controls for critical transmission infrastructure': 'ضوابط أساسية للبنية التحتية الحرجة لشبكة النقل',
  'MFA and RBAC': 'المصادقة متعددة العوامل والصلاحيات',
  'Multi-factor authentication and role-based permissions for all modules.': 'مصادقة متعددة العوامل وصلاحيات حسب الدور لكل الوحدات.',
  Encryption: 'التشفير',
  'AES-256 at rest and TLS 1.3 for integration traffic.': 'تشفير AES-256 للبيانات المخزنة وTLS 1.3 لحركة التكامل.',
  'Mobile Device Management': 'إدارة الأجهزة المحمولة',
  'Remote wipe and device policy enforcement for field devices.': 'مسح عن بعد وتطبيق سياسات الأجهزة الميدانية.',
  'Audit Logs': 'سجلات التدقيق',
  'Immutable logs for user actions, status changes, and data updates.': 'سجلات غير قابلة للتعديل لإجراءات المستخدمين وتغييرات الحالة وتحديثات البيانات.',

  Active: 'نشط',
  Assigned: 'مسند',
  Completed: 'مكتمل',
  Submitted: 'مرسل',
  Approved: 'معتمد',
  Synced: 'متزامن',
  Connected: 'متصل',
  Healthy: 'سليم',
  Critical: 'حرج',
  High: 'مرتفع',
  Medium: 'متوسط',
  Monitoring: 'قيد المراقبة',
  Draft: 'مسودة',
  Queued: 'في الانتظار',
  Attention: 'بحاجة لانتباه',
  'Review Required': 'يتطلب مراجعة',
  'Offline Saved': 'محفوظ دون اتصال',
  'Offline Draft': 'مسودة دون اتصال',
  'Real-time': 'فوري',
  'On-demand': 'عند الطلب',
  'Post-inspection': 'بعد الفحص',
  'On scan': 'عند المسح',
  Scheduled: 'مجدول',
  Bidirectional: 'ثنائي الاتجاه',
  'Read / Write': 'قراءة / كتابة',
  'Write Push': 'دفع كتابي',
  Read: 'قراءة',
  'Read Pull': 'سحب قراءة',
  Enabled: 'مفعل',
  '15 minutes': '15 دقيقة',
  '24 hours': '24 ساعة',
  'Supervisor approval': 'اعتماد المشرف',

  'Power Transformer': 'محول قدرة',
  'Circuit Breaker': 'قاطع دائرة',
  'Overhead Line Segment': 'مقطع خط هوائي',
  'Protection Relay': 'مرحل حماية',
  'Remote Terminal Unit': 'وحدة طرفية بعيدة',
  'Power Transformers': 'محولات القدرة',
  'Circuit Breakers': 'قواطع الدائرة',
  'Overhead Lines': 'الخطوط الهوائية',
  'Protection & Control': 'الحماية والتحكم',
  'Substation Civil': 'الأعمال المدنية للمحطات',
  'Monthly / Annually': 'شهري / سنوي',
  'Monthly / 3-Yearly': 'شهري / كل 3 سنوات',
  'Quarterly / Annually': 'ربع سنوي / سنوي',
  'Annually / 3-Yearly': 'سنوي / كل 3 سنوات',
  'Bi-Annually': 'نصف سنوي',
  'Monthly Transformer Inspection': 'فحص شهري للمحول',
  'SF6 Pressure Check': 'فحص ضغط SF6',
  'Route Patrol': 'دورية مسار',
  'Annual Relay Test': 'اختبار سنوي للمرحل',
  'Communication Check': 'فحص الاتصالات',
  'Team Alpha': 'الفريق ألفا',
  'Team Beta': 'الفريق بيتا',
  'Line Crew 2': 'فريق الخطوط 2',
  'Protection Team': 'فريق الحماية',
  'SCADA Team': 'فريق سكادا',
  'IT Operations': 'عمليات تقنية المعلومات',
  Protection: 'الحماية',
  'Transformer Inspection': 'فحص المحول',
  'Circuit Breaker SF6': 'فحص SF6 لقاطع الدائرة',
  'OHL Patrol': 'دورية الخط الهوائي',
  'Relay Test': 'اختبار المرحل',
  'Field Inspector': 'مفتش ميداني',
  'Field Supervisor': 'مشرف ميداني',
  'System Admin': 'مسؤول النظام',
  'Specialist Group': 'مجموعة متخصصة',
  'Asset Manager': 'مدير الأصول',
  'All modules, configuration, user provisioning': 'كل الوحدات والإعدادات وإدارة المستخدمين',
  'Dispatch, review, approve, exception handling': 'التوزيع والمراجعة والاعتماد ومعالجة الاستثناءات',
  'Mobile tasks, forms, evidence capture, offline sync': 'المهام المحمولة والنماذج وجمع الأدلة والمزامنة دون اتصال',
  'Dashboards, asset health, OLCM condition review': 'لوحات التحكم وصحة الأصول ومراجعة حالة OLCM',
  'Offline sync interval': 'فاصل المزامنة دون اتصال',
  'Mandatory GPS evidence': 'إلزامية دليل الموقع',
  'MFA enforcement': 'تطبيق المصادقة متعددة العوامل',
  'Critical defect SLA': 'اتفاقية مستوى الخدمة للعيوب الحرجة',
  'CMMS closure mode': 'نمط إغلاق CMMS',
  'Mobile Platform': 'منصة الهاتف المحمول',
  'Forms Engine': 'محرك النماذج',
  'Identity & Access': 'الهوية والوصول',
  'Workflow Engine': 'محرك سير العمل',
  'Integration Gateway': 'بوابة التكامل',
  'REST API': 'واجهة REST',
  'REST API / WFS': 'واجهة REST / WFS',
  'Local Scan / REST': 'مسح محلي / REST',
  'DB View / API': 'عرض قاعدة بيانات / API',
  'Barcode Master': 'سجل الباركود الرئيسي',
  'BI Dashboard': 'لوحة BI',
  'Retrieve open tasks for crew or asset': 'استرجاع المهام المفتوحة للفريق أو الأصل',
  'Update execution progression': 'تحديث تقدم التنفيذ',
  'Resolve asset data upon scan': 'جلب بيانات الأصل عند المسح',
  'Submit verified inspection payload': 'إرسال بيانات الفحص المعتمدة',
  'Push health indices and ratings': 'إرسال مؤشرات الصحة والتقييمات',
  'Master DB': 'قاعدة البيانات الرئيسية',
  'Platform DB': 'قاعدة بيانات المنصة'
}

export function useLanguage() {
  const isArabic = computed(() => locale.value === 'ar')
  const dir = computed(() => isArabic.value ? 'rtl' : 'ltr')
  const lang = computed(() => isArabic.value ? 'ar' : 'en')

  function setLocale(value: Locale) {
    locale.value = value
  }

  function toggleLocale() {
    locale.value = isArabic.value ? 'en' : 'ar'
  }

  function translateKey(key: string, fallback = key) {
    if (!isArabic.value) return fallback
    return translations[key] ?? fallback
  }

  function t(value: unknown) {
    if (isTranslatableText(value)) return translateKey(value.key, value.fallback)
    if (typeof value !== 'string') return value
    if (!isArabic.value) return value
    return translations[value] ?? value
  }

  function displayText(value: unknown) {
    if (isTranslatableText(value)) return t(value)
    if (value == null) return ''
    return String(value)
  }

  function searchText(value: unknown) {
    if (isTranslatableText(value)) return value.fallback
    if (value == null) return ''
    return String(value)
  }

  function translateRow<T extends Record<string, unknown>>(row: T) {
    return Object.fromEntries(
      Object.entries(row).map(([key, value]) => [key, t(value)])
    ) as T
  }

  function translateRows<T extends Record<string, unknown>>(rows: T[]) {
    return rows.map((row) => translateRow(row))
  }

  return {
    dir,
    isArabic,
    lang,
    locale,
    setLocale,
    displayText,
    searchText,
    t,
    translateKey,
    toggleLocale,
    translateRow,
    translateRows
  }
}

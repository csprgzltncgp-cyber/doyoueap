import { createClient } from 'https://esm.sh/@supabase/supabase-js@2'

const corsHeaders = {
  'Access-Control-Allow-Origin': '*',
  'Access-Control-Allow-Headers': 'authorization, x-client-info, apikey, content-type',
}

const LARAVEL_API_URL = 'https://admindashboard.chestnutce.com/api'

// RiportValue type constants (from Laravel RiportValue model)
const RIPORT_VALUE_TYPES = {
  TYPE_IS_CRISIS: 3,
  TYPE_PLACE_OF_RECEIPT: 6,
  TYPE_PROBLEM_TYPE: 7,
  TYPE_EMPLOYEE_OR_FAMILY_MEMBER: 9,
  TYPE_GENDER: 10,
  TYPE_AGE: 11,
  TYPE_SOURCE: 12,
  TYPE_PROBLEM_DETAILS: 16,
  TYPE_TYPE_OF_PROBLEM: 24,
  TYPE_LANGUAGE: 32,
  TYPE_CONSULTATION_NUMBER: 500,
  TYPE_STATUS: 501,
  TYPE_WORKSHOP_NUMBER_OF_PARTICIPANTS: 505,
  TYPE_ORIENTATION_NUMBER_OF_PARTICIPANTS: 506,
  TYPE_PRIZEGAME_NUMBER_OF_PARTICIPANTS: 507,
  TYPE_CRISIS_NUMBER_OF_PARTICIPANTS: 508,
  TYPE_HEALTH_DAY_NUMBER_OF_PARTICIPANTS: 509,
  TYPE_EXPERT_OUTPLACEMENT_NUMBER_OF_PARTICIPANTS: 510,
  TYPE_ONSITE_CONSULTATION_STATUS: 601,
}

// Value type mapping cache
interface ValueTypeMapping {
  [type: string]: {
    [value: string]: string // value -> label
  }
}

interface RiportRequest {
  company_id: number
  country_id?: number | null
  period_type?: 'quarter' | 'month'
  quarter?: number | null
  year?: number | null
  include_customer_satisfaction?: boolean
  include_customer_satisfaction_values?: boolean
  riport_values_limit?: number
}

interface RiportValue {
  id: number
  riport_id: number
  country_id: number
  type: number
  value: string
  connection_id?: string | null
  is_ongoing?: boolean | null
}

interface ProcessedStatistics {
  // Case numbers
  caseNumbers: {
    closed: number
    interrupted: number
    clientUnreachable: number
    inProgress: number
    total: number
  }
  // Consultations
  consultations: {
    total: number
    onsite: number
    ongoing: number
  }
  // Activity participants
  activities: {
    workshop: number
    crisis: number
    orientation: number
    healthDay: number
    expertOutplacement: number
    prizegame: number
  }
  // Problem types distribution
  problemTypes: Record<string, number>
  // Type of problem (subcategories)
  typeOfProblem: Record<string, number>
  // Problem details (subcategories)
  problemDetails: Record<string, number>
  // Demographics
  gender: Record<string, number>
  age: Record<string, number>
  employeeOrFamily: Record<string, number>
  // Consultation details
  placeOfReceipt: Record<string, number>
  language: Record<string, number>
  source: Record<string, number>
  // Crisis
  crisis: Record<string, number>
  // Cross-tabulations: gender × problem type
  genderByProblemType: Record<string, Record<string, number>>
  // Cross-tabulations: age × problem type
  ageByProblemType: Record<string, Record<string, number>>
}

function processRiportValues(riportValues: RiportValue[], countryId: number | null): ProcessedStatistics {
  // Filter by country if specified
  const filteredValues = countryId 
    ? riportValues.filter(v => v.country_id === countryId)
    : riportValues

  const stats: ProcessedStatistics = {
    caseNumbers: {
      closed: 0,
      interrupted: 0,
      clientUnreachable: 0,
      inProgress: 0,
      total: 0,
    },
    consultations: {
      total: 0,
      onsite: 0,
      ongoing: 0,
    },
    activities: {
      workshop: 0,
      crisis: 0,
      orientation: 0,
      healthDay: 0,
      expertOutplacement: 0,
      prizegame: 0,
    },
    problemTypes: {},
    typeOfProblem: {},
    problemDetails: {},
    gender: {},
    age: {},
    employeeOrFamily: {},
    placeOfReceipt: {},
    language: {},
    source: {},
    crisis: {},
    genderByProblemType: {},
    ageByProblemType: {},
  }

  // Build connection maps for cross-tabulation
  const connectionToProblemType: Record<string, string> = {}
  const connectionToGender: Record<string, string> = {}
  const connectionToAge: Record<string, string> = {}
  
  // First pass: collect connection_id mappings
  for (const rv of filteredValues) {
    if (rv.connection_id) {
      if (rv.type === RIPORT_VALUE_TYPES.TYPE_PROBLEM_TYPE) {
        connectionToProblemType[rv.connection_id] = rv.value
      } else if (rv.type === RIPORT_VALUE_TYPES.TYPE_GENDER) {
        connectionToGender[rv.connection_id] = rv.value
      } else if (rv.type === RIPORT_VALUE_TYPES.TYPE_AGE) {
        connectionToAge[rv.connection_id] = rv.value
      }
    }
  }

  // Debug: collect all STATUS values for logging
  const statusValues = filteredValues
    .filter(v => v.type === RIPORT_VALUE_TYPES.TYPE_STATUS)
    .map(v => ({ id: v.id, value: v.value, country_id: v.country_id }))
  console.log('STATUS (type 501) values:', JSON.stringify(statusValues))

  // Process each value
  for (const rv of filteredValues) {
    switch (rv.type) {
      case RIPORT_VALUE_TYPES.TYPE_STATUS:
        if (rv.value === 'confirmed') {
          stats.caseNumbers.closed++
        } else if (rv.value === 'interrupted' || rv.value === 'interrupted_confirmed') {
          stats.caseNumbers.interrupted++
        } else if (rv.value === 'client_unreachable' || rv.value === 'client_unreachable_confirmed') {
          stats.caseNumbers.clientUnreachable++
        }
        break

      case RIPORT_VALUE_TYPES.TYPE_CONSULTATION_NUMBER:
        const consultNum = parseInt(rv.value) || 0
        if (rv.is_ongoing) {
          stats.consultations.ongoing += consultNum
        } else {
          stats.consultations.total += consultNum
        }
        break

      case RIPORT_VALUE_TYPES.TYPE_ONSITE_CONSULTATION_STATUS:
        if (rv.value === 'booked' && !rv.is_ongoing) {
          stats.consultations.onsite++
        }
        break

      case RIPORT_VALUE_TYPES.TYPE_WORKSHOP_NUMBER_OF_PARTICIPANTS:
        stats.activities.workshop += parseInt(rv.value) || 0
        break

      case RIPORT_VALUE_TYPES.TYPE_CRISIS_NUMBER_OF_PARTICIPANTS:
        stats.activities.crisis += parseInt(rv.value) || 0
        break

      case RIPORT_VALUE_TYPES.TYPE_ORIENTATION_NUMBER_OF_PARTICIPANTS:
        stats.activities.orientation += parseInt(rv.value) || 0
        break

      case RIPORT_VALUE_TYPES.TYPE_HEALTH_DAY_NUMBER_OF_PARTICIPANTS:
        stats.activities.healthDay += parseInt(rv.value) || 0
        break

      case RIPORT_VALUE_TYPES.TYPE_EXPERT_OUTPLACEMENT_NUMBER_OF_PARTICIPANTS:
        stats.activities.expertOutplacement += parseInt(rv.value) || 0
        break

      case RIPORT_VALUE_TYPES.TYPE_PRIZEGAME_NUMBER_OF_PARTICIPANTS:
        stats.activities.prizegame += parseInt(rv.value) || 0
        break

      case RIPORT_VALUE_TYPES.TYPE_PROBLEM_TYPE:
        stats.problemTypes[rv.value] = (stats.problemTypes[rv.value] || 0) + 1
        break

      case RIPORT_VALUE_TYPES.TYPE_TYPE_OF_PROBLEM:
        stats.typeOfProblem[rv.value] = (stats.typeOfProblem[rv.value] || 0) + 1
        break

      case RIPORT_VALUE_TYPES.TYPE_PROBLEM_DETAILS:
        stats.problemDetails[rv.value] = (stats.problemDetails[rv.value] || 0) + 1
        break

      case RIPORT_VALUE_TYPES.TYPE_GENDER:
        stats.gender[rv.value] = (stats.gender[rv.value] || 0) + 1
        break

      case RIPORT_VALUE_TYPES.TYPE_AGE:
        stats.age[rv.value] = (stats.age[rv.value] || 0) + 1
        break

      case RIPORT_VALUE_TYPES.TYPE_EMPLOYEE_OR_FAMILY_MEMBER:
        stats.employeeOrFamily[rv.value] = (stats.employeeOrFamily[rv.value] || 0) + 1
        break

      case RIPORT_VALUE_TYPES.TYPE_PLACE_OF_RECEIPT:
        stats.placeOfReceipt[rv.value] = (stats.placeOfReceipt[rv.value] || 0) + 1
        break

      case RIPORT_VALUE_TYPES.TYPE_LANGUAGE:
        stats.language[rv.value] = (stats.language[rv.value] || 0) + 1
        break

      case RIPORT_VALUE_TYPES.TYPE_SOURCE:
        stats.source[rv.value] = (stats.source[rv.value] || 0) + 1
        break

      case RIPORT_VALUE_TYPES.TYPE_IS_CRISIS:
        stats.crisis[rv.value] = (stats.crisis[rv.value] || 0) + 1
        break
    }
  }

  // Build cross-tabulations: gender × problem type
  for (const connId of Object.keys(connectionToProblemType)) {
    const problemType = connectionToProblemType[connId]
    const gender = connectionToGender[connId]
    if (problemType && gender) {
      if (!stats.genderByProblemType[problemType]) {
        stats.genderByProblemType[problemType] = {}
      }
      stats.genderByProblemType[problemType][gender] = (stats.genderByProblemType[problemType][gender] || 0) + 1
    }
  }

  // Build cross-tabulations: age × problem type
  for (const connId of Object.keys(connectionToProblemType)) {
    const problemType = connectionToProblemType[connId]
    const age = connectionToAge[connId]
    if (problemType && age) {
      if (!stats.ageByProblemType[problemType]) {
        stats.ageByProblemType[problemType] = {}
      }
      stats.ageByProblemType[problemType][age] = (stats.ageByProblemType[problemType][age] || 0) + 1
    }
  }

  // Calculate total cases (closed + interrupted + clientUnreachable)
  // Note: in_progress is NOT included because it comes from Cases table, not riport_values
  // Based on TYPE_STATUS values: confirmed, interrupted(_confirmed), client_unreachable(_confirmed)
  stats.caseNumbers.total = stats.caseNumbers.closed + stats.caseNumbers.interrupted + stats.caseNumbers.clientUnreachable

  return stats
}

// Convert distribution object to percentages
function toPercentages(distribution: Record<string, number>): Record<string, number> {
  const total = Object.values(distribution).reduce((sum, val) => sum + val, 0)
  if (total === 0) return distribution
  
  const result: Record<string, number> = {}
  for (const [key, value] of Object.entries(distribution)) {
    result[key] = Math.round((value / total) * 100)
  }
  return result
}

// Find the most common value in a distribution
function findMostCommon(distribution: Record<string, number>): { key: string; count: number; percentage: number } | null {
  const entries = Object.entries(distribution)
  if (entries.length === 0) return null
  
  const total = entries.reduce((sum, [, val]) => sum + val, 0)
  const sorted = entries.sort((a, b) => b[1] - a[1])
  const [key, count] = sorted[0]
  
  return {
    key,
    count,
    percentage: total > 0 ? Math.round((count / total) * 100) : 0,
  }
}

// Helper: delay function
function delay(ms: number): Promise<void> {
  return new Promise(resolve => setTimeout(resolve, ms))
}

// Cache TTL for Supabase-stored mappings (24 hours)
const MAPPING_CACHE_TTL_MS = 1000 * 60 * 60 * 24

// Only fetch the types we actually display in Program Reports
const REQUIRED_VALUE_TYPE_IDS = new Set<string>([
  String(RIPORT_VALUE_TYPES.TYPE_IS_CRISIS),
  String(RIPORT_VALUE_TYPES.TYPE_PLACE_OF_RECEIPT),
  String(RIPORT_VALUE_TYPES.TYPE_PROBLEM_TYPE),
  String(RIPORT_VALUE_TYPES.TYPE_EMPLOYEE_OR_FAMILY_MEMBER),
  String(RIPORT_VALUE_TYPES.TYPE_GENDER),
  String(RIPORT_VALUE_TYPES.TYPE_AGE),
  String(RIPORT_VALUE_TYPES.TYPE_SOURCE),
  String(RIPORT_VALUE_TYPES.TYPE_PROBLEM_DETAILS),
  String(RIPORT_VALUE_TYPES.TYPE_TYPE_OF_PROBLEM),
  String(RIPORT_VALUE_TYPES.TYPE_LANGUAGE),
])

// Get value type mappings from Supabase cache (or fetch & cache if expired)
// eslint-disable-next-line @typescript-eslint/no-explicit-any
async function getValueTypeMappings(
  supabaseClient: any,
  companyId: number,
  laravelApiToken: string,
  languageId: number = 2
): Promise<ValueTypeMapping> {
  // Step 1: Check Supabase cache
  const { data: cached, error: cacheError } = await supabaseClient
    .from('value_type_mappings_cache')
    .select('mappings, fetched_at')
    .eq('company_id', companyId)
    .eq('language_id', languageId)
    .single()

  if (!cacheError && cached) {
    const fetchedAt = new Date(cached.fetched_at).getTime()
    const age = Date.now() - fetchedAt

    const cachedMappings = cached.mappings as ValueTypeMapping
    const hasAllRequiredTypes = Array.from(REQUIRED_VALUE_TYPE_IDS).every((typeId) => {
      return cachedMappings?.[typeId] && Object.keys(cachedMappings[typeId]).length > 0
    })

    if (age < MAPPING_CACHE_TTL_MS && hasAllRequiredTypes) {
      console.log(`Using cached mappings for company ${companyId} (age: ${Math.round(age / 1000 / 60)} minutes)`)
      return cachedMappings
    }

    if (!hasAllRequiredTypes) {
      console.log(`Cached mappings missing required types for company ${companyId}, refreshing...`)
    } else {
      console.log(`Cache expired for company ${companyId}, refreshing...`)
    }
  } else {
    console.log(`No cache found for company ${companyId}, fetching fresh mappings...`)
  }

  // Step 2: Fetch fresh mappings from Laravel API
  const mapping = await fetchValueTypeMappingsFromLaravel(companyId, laravelApiToken, languageId)

  // Step 3: Store in Supabase cache (upsert)
  if (Object.keys(mapping).length > 0) {
    const { error: upsertError } = await supabaseClient
      .from('value_type_mappings_cache')
      .upsert({
        company_id: companyId,
        language_id: languageId,
        mappings: mapping,
        fetched_at: new Date().toISOString(),
      }, {
        onConflict: 'company_id,language_id'
      })

    if (upsertError) {
      console.error('Failed to cache mappings:', upsertError)
    } else {
      console.log(`Cached mappings for company ${companyId}`)
    }
  }

  return mapping
}

// Fetch value type mappings from Laravel API (slow, rate-limited)
async function fetchValueTypeMappingsFromLaravel(
  companyId: number,
  laravelApiToken: string,
  languageId: number
): Promise<ValueTypeMapping> {
  const mapping: ValueTypeMapping = {}

  try {
    // Step 1: Get all value types
    const typesResponse = await fetch(
      `${LARAVEL_API_URL}/riports/value-types?company_id=${companyId}&language_id=${languageId}`,
      {
        headers: {
          'Authorization': `Bearer ${laravelApiToken}`,
          'Accept': 'application/json',
        },
      }
    )

    if (!typesResponse.ok) {
      console.error('Failed to fetch value types:', typesResponse.status)
      return mapping
    }

    const typesData = await typesResponse.json()
    const types = typesData.data || []

    // Filter to only the types we actually need (source can vary by backend implementation)
    const requiredTypes = types.filter((t: { type: string | number }) => {
      return REQUIRED_VALUE_TYPE_IDS.has(String(t.type))
    })

    console.log(`Fetching ${requiredTypes.length} required value types from Laravel`)

    // Step 2: Fetch values one at a time with delay (safest for rate limiting)
    for (const typeItem of requiredTypes) {
      const type = String(typeItem.type)

      try {
        const valuesResponse = await fetch(
          `${LARAVEL_API_URL}/riports/value-types/${type}/values?company_id=${companyId}&language_id=${languageId}`,
          {
            headers: {
              'Authorization': `Bearer ${laravelApiToken}`,
              'Accept': 'application/json',
            },
          }
        )

        if (!valuesResponse.ok) {
          console.error(`Failed to fetch values for type ${type}:`, valuesResponse.status)
          continue
        }

        const valuesData = await valuesResponse.json()
        const values = valuesData.data || []

        if (values.length > 0) {
          mapping[type] = {}
          for (const item of values) {
            mapping[type][item.value] = item.label
          }
          console.log(`Fetched mapping for type ${type}: ${Object.keys(mapping[type]).length} values`)
        }
      } catch (error) {
        console.error(`Error fetching values for type ${type}:`, error)
      }

      // Delay between each request to avoid rate limiting
      await delay(800)
    }
  } catch (error) {
    console.error('Error fetching value type mappings from Laravel:', error)
  }

  return mapping
}

Deno.serve(async (req) => {
  if (req.method === 'OPTIONS') {
    return new Response(null, { headers: corsHeaders })
  }

  try {
    const supabaseUrl = Deno.env.get('SUPABASE_URL')!
    const supabaseServiceKey = Deno.env.get('SUPABASE_SERVICE_ROLE_KEY')!
    const laravelApiToken = Deno.env.get('LARAVEL_API_TOKEN')

    if (!laravelApiToken) {
      throw new Error('LARAVEL_API_TOKEN is not configured')
    }

    // Get user from auth header
    const authHeader = req.headers.get('Authorization')
    if (!authHeader) {
      throw new Error('Authorization header required')
    }

    const supabaseClient = createClient(supabaseUrl, supabaseServiceKey)
    
    // Verify the user's JWT
    const token = authHeader.replace('Bearer ', '')
    const { data: { user }, error: userError } = await supabaseClient.auth.getUser(token)
    
    if (userError || !user) {
      throw new Error('Invalid or expired token')
    }

    // Get user's laravel_company_id from profile
    const { data: profile, error: profileError } = await supabaseClient
      .from('profiles')
      .select('laravel_company_id')
      .eq('id', user.id)
      .single()

    if (profileError || !profile?.laravel_company_id) {
      throw new Error('User has no associated company')
    }

    // Parse request body for optional parameters
    let requestParams: Partial<RiportRequest> = {}
    try {
      if (req.body) {
        requestParams = await req.json()
      }
    } catch {
      // No body or invalid JSON, use defaults
    }

    // Build Laravel API request - request riport_values with a high limit
    const riportRequest = {
      company_id: profile.laravel_company_id,
      country_id: requestParams.country_id ?? null,
      period_type: requestParams.period_type ?? 'quarter',
      quarter: requestParams.quarter ?? null,
      year: requestParams.year ?? null,
      include_customer_satisfaction: requestParams.include_customer_satisfaction ?? true,
      include_customer_satisfaction_values: requestParams.include_customer_satisfaction_values ?? true,
      riport_values_limit: 1000, // Request riport values
      include_raw: true, // Request raw riport values
    }

    console.log('Fetching riport data for company:', profile.laravel_company_id, riportRequest)

    // Fetch value type mappings (from Supabase cache or Laravel API)
    // Language IDs: 1 = Hungarian (Magyar), 2 = Polish, etc.
    const valueTypeMappings = await getValueTypeMappings(
      supabaseClient,
      profile.laravel_company_id,
      laravelApiToken,
      1 // Hungarian language ID (Magyar)
    )

    console.log('Fetched value type mappings:', valueTypeMappings)

    // Call Laravel API
    const laravelResponse = await fetch(`${LARAVEL_API_URL}/riports/summary`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${laravelApiToken}`,
        'Accept': 'application/json',
      },
      body: JSON.stringify(riportRequest),
    })

    if (!laravelResponse.ok) {
      const errorText = await laravelResponse.text()
      console.error('Laravel API error:', laravelResponse.status, errorText)
      throw new Error(`Laravel API error: ${laravelResponse.status}`)
    }

    const riportData = await laravelResponse.json()

    // Process riport_values if available
    let processedStats: ProcessedStatistics | null = null
    let statsPercentages: {
      problemTypes: Record<string, number>
      typeOfProblem: Record<string, number>
      problemDetails: Record<string, number>
      gender: Record<string, number>
      age: Record<string, number>
      employeeOrFamily: Record<string, number>
      placeOfReceipt: Record<string, number>
      language: Record<string, number>
      source: Record<string, number>
      crisis: Record<string, number>
      genderByProblemType: Record<string, Record<string, number>>
      ageByProblemType: Record<string, Record<string, number>>
    } | null = null
    let highlights: {
      mostFrequentProblem: { key: string; count: number; percentage: number } | null
      dominantGender: { key: string; count: number; percentage: number } | null
      dominantAgeGroup: { key: string; count: number; percentage: number } | null
    } | null = null

    if (riportData.riport_values && Array.isArray(riportData.riport_values)) {
      processedStats = processRiportValues(
        riportData.riport_values,
        requestParams.country_id ?? null
      )

      // Calculate percentages for distributions
      statsPercentages = {
        problemTypes: toPercentages(processedStats.problemTypes),
        typeOfProblem: toPercentages(processedStats.typeOfProblem),
        problemDetails: toPercentages(processedStats.problemDetails),
        gender: toPercentages(processedStats.gender),
        age: toPercentages(processedStats.age),
        employeeOrFamily: toPercentages(processedStats.employeeOrFamily),
        placeOfReceipt: toPercentages(processedStats.placeOfReceipt),
        language: toPercentages(processedStats.language),
        source: toPercentages(processedStats.source),
        crisis: toPercentages(processedStats.crisis),
        // Cross-tabulations (raw counts)
        genderByProblemType: processedStats.genderByProblemType,
        ageByProblemType: processedStats.ageByProblemType,
      }

      // Calculate highlights
      highlights = {
        mostFrequentProblem: findMostCommon(processedStats.problemTypes),
        dominantGender: findMostCommon(processedStats.gender),
        dominantAgeGroup: findMostCommon(processedStats.age),
      }
    }

    return new Response(
      JSON.stringify({
        success: true,
        data: {
          ...riportData,
          // Add processed statistics
          processed_statistics: processedStats,
          statistics_percentages: statsPercentages,
          highlights,
          // Add value type mappings
          value_type_mappings: valueTypeMappings,
        },
      }),
      {
        headers: { ...corsHeaders, 'Content-Type': 'application/json' },
        status: 200,
      }
    )

  } catch (error: unknown) {
    const errorMessage = error instanceof Error ? error.message : 'Unknown error'
    console.error('Fetch riport data error:', error)
    return new Response(
      JSON.stringify({
        success: false,
        error: errorMessage,
      }),
      {
        headers: { ...corsHeaders, 'Content-Type': 'application/json' },
        status: 400,
      }
    )
  }
})

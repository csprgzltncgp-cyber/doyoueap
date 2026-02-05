import { createClient } from 'https://esm.sh/@supabase/supabase-js@2'

const corsHeaders = {
  'Access-Control-Allow-Origin': '*',
  'Access-Control-Allow-Headers': 'authorization, x-client-info, apikey, content-type',
}

const LARAVEL_API_URL = 'https://admindashboard.chestnutce.com/api'

interface RiportRequest {
  company_id: number
  country_id?: number | null
  period_type?: 'quarter' | 'month'
  quarter?: number | null
  year?: number | null
  include_customer_satisfaction?: boolean
  include_customer_satisfaction_values?: boolean
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

    // Build Laravel API request
    const riportRequest: RiportRequest = {
      company_id: profile.laravel_company_id,
      country_id: requestParams.country_id ?? null,
      period_type: requestParams.period_type ?? 'quarter',
      quarter: requestParams.quarter ?? null,
      year: requestParams.year ?? null,
      include_customer_satisfaction: requestParams.include_customer_satisfaction ?? true,
      include_customer_satisfaction_values: requestParams.include_customer_satisfaction_values ?? true,
    }

    console.log('Fetching riport data for company:', profile.laravel_company_id, riportRequest)

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

    return new Response(
      JSON.stringify({
        success: true,
        data: riportData,
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

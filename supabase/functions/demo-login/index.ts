import { createClient } from 'https://esm.sh/@supabase/supabase-js@2'

const corsHeaders = {
  'Access-Control-Allow-Origin': '*',
  'Access-Control-Allow-Headers': 'authorization, x-client-info, apikey, content-type',
}

const DEMO_EMAIL = 'zoltan.csepregi@cgpeu.com'

Deno.serve(async (req) => {
  if (req.method === 'OPTIONS') {
    return new Response(null, { headers: corsHeaders })
  }

  try {
    const supabaseUrl = Deno.env.get('SUPABASE_URL')!
    const supabaseServiceKey = Deno.env.get('SUPABASE_SERVICE_ROLE_KEY')!
    
    const supabaseAdmin = createClient(supabaseUrl, supabaseServiceKey, {
      auth: {
        autoRefreshToken: false,
        persistSession: false
      }
    })

    // Check if user exists
    const { data: existingUsers, error: listError } = await supabaseAdmin.auth.admin.listUsers()
    
    if (listError) {
      console.error('Error listing users:', listError)
      throw listError
    }

    const targetUser = existingUsers.users.find(u => u.email === DEMO_EMAIL)

    if (!targetUser) {
      throw new Error(`User ${DEMO_EMAIL} not found`)
    }

    // Generate a magic link for the user using admin API
    const { data: linkData, error: linkError } = await supabaseAdmin.auth.admin.generateLink({
      type: 'magiclink',
      email: DEMO_EMAIL,
    })

    if (linkError) {
      console.error('Error generating magic link:', linkError)
      throw linkError
    }

    // Extract the token from the magic link and create a session
    const token = linkData.properties?.hashed_token
    
    if (!token) {
      throw new Error('Failed to generate authentication token')
    }

    // Use the OTP to verify and create session
    const supabaseClient = createClient(supabaseUrl, Deno.env.get('SUPABASE_ANON_KEY')!)
    
    const { data: session, error: sessionError } = await supabaseClient.auth.verifyOtp({
      token_hash: token,
      type: 'magiclink'
    })

    if (sessionError) {
      console.error('Error verifying OTP:', sessionError)
      throw sessionError
    }

    return new Response(
      JSON.stringify({ 
        success: true, 
        session: session.session,
        user: session.user
      }),
      { 
        headers: { ...corsHeaders, 'Content-Type': 'application/json' },
        status: 200 
      }
    )

  } catch (error: unknown) {
    const errorMessage = error instanceof Error ? error.message : 'Unknown error'
    console.error('Demo login error:', error)
    return new Response(
      JSON.stringify({ 
        success: false, 
        error: errorMessage 
      }),
      { 
        headers: { ...corsHeaders, 'Content-Type': 'application/json' },
        status: 400 
      }
    )
  }
})
import { createClient } from 'https://esm.sh/@supabase/supabase-js@2'

const corsHeaders = {
  'Access-Control-Allow-Origin': '*',
  'Access-Control-Allow-Headers': 'authorization, x-client-info, apikey, content-type',
}

const DEMO_EMAIL = 'zoltan.csepregi@cgpeu.com'
const DEMO_PASSWORD = 'demo123456'
const DEMO_NAME = 'Csepregi Zoltán'

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

    // Check if demo user exists
    const { data: existingUsers, error: listError } = await supabaseAdmin.auth.admin.listUsers()
    
    if (listError) {
      console.error('Error listing users:', listError)
      throw listError
    }

    const demoUser = existingUsers.users.find(u => u.email === DEMO_EMAIL)

    if (!demoUser) {
      // Create demo user if it doesn't exist
      const { data: newUser, error: createError } = await supabaseAdmin.auth.admin.createUser({
        email: DEMO_EMAIL,
        password: DEMO_PASSWORD,
        email_confirm: true,
        user_metadata: {
          full_name: DEMO_NAME
        }
      })

      if (createError) {
        console.error('Error creating demo user:', createError)
        throw createError
      }

      console.log('Demo user created:', newUser.user?.id)

      // Create profile for demo user
      const { error: profileError } = await supabaseAdmin
        .from('profiles')
        .upsert({
          id: newUser.user!.id,
          email: DEMO_EMAIL,
          full_name: DEMO_NAME,
          company_name: 'Demo Cég Kft.',
          employee_count: '100-500'
        })

      if (profileError) {
        console.error('Error creating profile:', profileError)
      }

      // Add HR role to demo user
      const { error: roleError } = await supabaseAdmin
        .from('user_roles')
        .upsert({
          user_id: newUser.user!.id,
          role: 'hr'
        })

      if (roleError) {
        console.error('Error adding role:', roleError)
      }
    }

    // Sign in the demo user and return session
    const { data: signInData, error: signInError } = await supabaseAdmin.auth.admin.generateLink({
      type: 'magiclink',
      email: DEMO_EMAIL,
    })

    if (signInError) {
      console.error('Error generating link:', signInError)
      throw signInError
    }

    // Use regular client to sign in with password
    const supabaseClient = createClient(supabaseUrl, Deno.env.get('SUPABASE_ANON_KEY')!)
    
    const { data: session, error: sessionError } = await supabaseClient.auth.signInWithPassword({
      email: DEMO_EMAIL,
      password: DEMO_PASSWORD
    })

    if (sessionError) {
      console.error('Error signing in:', sessionError)
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
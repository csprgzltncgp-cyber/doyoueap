import { createClient } from 'https://esm.sh/@supabase/supabase-js@2'

const corsHeaders = {
  'Access-Control-Allow-Origin': '*',
  'Access-Control-Allow-Headers': 'authorization, x-client-info, apikey, content-type',
}

const GUEST_EMAIL = 'guest@doyoueap.com'
const GUEST_PASSWORD = 'Guest2024!'

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

    // Check if guest user already exists
    const { data: existingUsers, error: listError } = await supabaseAdmin.auth.admin.listUsers()
    
    if (listError) {
      console.error('Error listing users:', listError)
      throw listError
    }

    const guestUser = existingUsers.users.find(u => u.email === GUEST_EMAIL)

    if (guestUser) {
      return new Response(
        JSON.stringify({ 
          success: true, 
          message: 'Guest user already exists',
          userId: guestUser.id
        }),
        { 
          headers: { ...corsHeaders, 'Content-Type': 'application/json' },
          status: 200 
        }
      )
    }

    // Create guest user
    const { data: newUser, error: createError } = await supabaseAdmin.auth.admin.createUser({
      email: GUEST_EMAIL,
      password: GUEST_PASSWORD,
      email_confirm: true,
      user_metadata: {
        full_name: 'Vendég Felhasználó'
      }
    })

    if (createError) {
      console.error('Error creating guest user:', createError)
      throw createError
    }

    // Add hr role for the guest user
    const { error: roleError } = await supabaseAdmin
      .from('user_roles')
      .upsert({ 
        user_id: newUser.user.id, 
        role: 'hr' 
      }, { 
        onConflict: 'user_id,role' 
      })

    if (roleError) {
      console.error('Error adding role:', roleError)
    }

    // Update profile with company name
    const { error: profileError } = await supabaseAdmin
      .from('profiles')
      .update({ 
        full_name: 'Vendég Felhasználó',
        company_name: 'Demo Cég Kft.'
      })
      .eq('id', newUser.user.id)

    if (profileError) {
      console.error('Error updating profile:', profileError)
    }

    return new Response(
      JSON.stringify({ 
        success: true, 
        message: 'Guest user created successfully',
        userId: newUser.user.id
      }),
      { 
        headers: { ...corsHeaders, 'Content-Type': 'application/json' },
        status: 200 
      }
    )

  } catch (error: unknown) {
    const errorMessage = error instanceof Error ? error.message : 'Unknown error'
    console.error('Create guest user error:', error)
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

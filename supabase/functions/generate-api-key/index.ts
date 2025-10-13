import { serve } from "https://deno.land/std@0.190.0/http/server.ts";
import { createClient } from "https://esm.sh/@supabase/supabase-js@2.39.3";

const corsHeaders = {
  'Access-Control-Allow-Origin': '*',
  'Access-Control-Allow-Headers': 'authorization, x-client-info, apikey, content-type',
};

serve(async (req) => {
  if (req.method === 'OPTIONS') {
    return new Response(null, { headers: corsHeaders });
  }

  try {
    const supabaseUrl = Deno.env.get('SUPABASE_URL')!;
    const supabaseKey = Deno.env.get('SUPABASE_SERVICE_ROLE_KEY')!;
    const supabase = createClient(supabaseUrl, supabaseKey);

    const authHeader = req.headers.get('Authorization');
    if (!authHeader) {
      throw new Error('No authorization header');
    }

    const token = authHeader.replace('Bearer ', '');
    const { data: { user }, error: authError } = await supabase.auth.getUser(token);

    if (authError || !user) {
      throw new Error('Unauthorized');
    }

    const { name, expiresInDays } = await req.json();

    // Lekérjük a felhasználó cégnevét
    const { data: profile, error: profileError } = await supabase
      .from('profiles')
      .select('company_name')
      .eq('id', user.id)
      .single();

    if (profileError || !profile?.company_name) {
      throw new Error('Company name not found');
    }

    // Generálunk egy biztonságos API kulcsot
    const apiKey = `eap_${crypto.randomUUID().replace(/-/g, '')}`;

    // Számoljuk ki a lejárati dátumot, ha van
    let expiresAt = null;
    if (expiresInDays && expiresInDays > 0) {
      const expDate = new Date();
      expDate.setDate(expDate.getDate() + expiresInDays);
      expiresAt = expDate.toISOString();
    }

    // Mentjük az adatbázisba
    const { data: apiKeyData, error: insertError } = await supabase
      .from('api_keys')
      .insert({
        user_id: user.id,
        company_name: profile.company_name,
        api_key: apiKey,
        name: name || 'API Key',
        expires_at: expiresAt,
      })
      .select()
      .single();

    if (insertError) {
      console.error('Error creating API key:', insertError);
      throw new Error('Failed to create API key');
    }

    return new Response(
      JSON.stringify({ apiKey: apiKeyData }),
      {
        headers: { ...corsHeaders, 'Content-Type': 'application/json' },
        status: 200,
      }
    );
  } catch (error) {
    console.error('Error:', error);
    return new Response(
      JSON.stringify({ error: error.message }),
      {
        headers: { ...corsHeaders, 'Content-Type': 'application/json' },
        status: 400,
      }
    );
  }
});
import { serve } from "https://deno.land/std@0.190.0/http/server.ts";
import { createClient } from "https://esm.sh/@supabase/supabase-js@2.39.3";

const corsHeaders = {
  'Access-Control-Allow-Origin': '*',
  'Access-Control-Allow-Headers': 'authorization, x-client-info, apikey, content-type, x-api-key',
};

serve(async (req) => {
  if (req.method === 'OPTIONS') {
    return new Response(null, { headers: corsHeaders });
  }

  try {
    const supabaseUrl = Deno.env.get('SUPABASE_URL')!;
    const supabaseKey = Deno.env.get('SUPABASE_SERVICE_ROLE_KEY')!;
    const supabase = createClient(supabaseUrl, supabaseKey);

    // API kulcs validálás
    const apiKey = req.headers.get('X-API-Key');
    if (!apiKey) {
      throw new Error('API key required');
    }

    const { data: apiKeyData, error: keyError } = await supabase
      .from('api_keys')
      .select('*')
      .eq('api_key', apiKey)
      .eq('is_active', true)
      .single();

    if (keyError || !apiKeyData) {
      throw new Error('Invalid API key');
    }

    // Ellenőrizzük a lejáratot
    if (apiKeyData.expires_at && new Date(apiKeyData.expires_at) < new Date()) {
      throw new Error('API key expired');
    }

    // Frissítjük az utolsó használat időpontját
    await supabase
      .from('api_keys')
      .update({ last_used_at: new Date().toISOString() })
      .eq('id', apiKeyData.id);

    // Lekérjük az audit-okat
    const { data: audits, error: auditsError } = await supabase
      .from('audits')
      .select(`
        id,
        company_name,
        program_name,
        access_token,
        status,
        is_active,
        created_at,
        updated_at,
        start_date,
        closes_at,
        expires_at,
        target_responses,
        available_languages
      `)
      .eq('company_name', apiKeyData.company_name)
      .order('created_at', { ascending: false });

    if (auditsError) {
      throw auditsError;
    }

    // Naplózzuk a hívást
    await supabase.from('api_logs').insert({
      api_key_id: apiKeyData.id,
      endpoint: '/api-get-audits',
      method: req.method,
      status_code: 200,
      ip_address: req.headers.get('x-forwarded-for'),
      user_agent: req.headers.get('user-agent'),
    });

    return new Response(
      JSON.stringify({ audits }),
      {
        headers: { ...corsHeaders, 'Content-Type': 'application/json' },
        status: 200,
      }
    );
  } catch (error) {
    console.error('Error:', error);
    const errorMessage = error instanceof Error ? error.message : 'Unknown error occurred';
    
    return new Response(
      JSON.stringify({ error: errorMessage }),
      {
        headers: { ...corsHeaders, 'Content-Type': 'application/json' },
        status: 400,
      }
    );
  }
});
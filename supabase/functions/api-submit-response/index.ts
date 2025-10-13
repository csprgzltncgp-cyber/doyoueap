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

    const { auditId, responses, employeeMetadata, participantIdHash } = await req.json();

    if (!auditId || !responses) {
      throw new Error('Missing required fields: auditId, responses');
    }

    // Ellenőrizzük, hogy az audit létezik és aktív
    const { data: audit, error: auditError } = await supabase
      .from('audits')
      .select('*')
      .eq('id', auditId)
      .eq('company_name', apiKeyData.company_name)
      .eq('is_active', true)
      .single();

    if (auditError || !audit) {
      throw new Error('Audit not found or inactive');
    }

    // Generálunk egy draw token-t, ha szükséges
    let drawToken = null;
    if (audit.draw_mode) {
      drawToken = crypto.randomUUID();
    }

    // Mentjük a választ
    const { data: response, error: responseError } = await supabase
      .from('audit_responses')
      .insert({
        audit_id: auditId,
        responses,
        employee_metadata: employeeMetadata || {},
        participant_id_hash: participantIdHash,
        draw_token: drawToken,
      })
      .select()
      .single();

    if (responseError) {
      throw responseError;
    }

    // Frissítjük az utolsó használat időpontját
    await supabase
      .from('api_keys')
      .update({ last_used_at: new Date().toISOString() })
      .eq('id', apiKeyData.id);

    // Naplózzuk a hívást
    await supabase.from('api_logs').insert({
      api_key_id: apiKeyData.id,
      endpoint: '/api-submit-response',
      method: req.method,
      status_code: 200,
      ip_address: req.headers.get('x-forwarded-for'),
      user_agent: req.headers.get('user-agent'),
      request_body: { auditId },
    });

    return new Response(
      JSON.stringify({ 
        success: true, 
        responseId: response.id,
        drawToken: drawToken
      }),
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
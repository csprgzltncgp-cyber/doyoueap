import { serve } from "https://deno.land/std@0.190.0/http/server.ts";
import { createClient } from "https://esm.sh/@supabase/supabase-js@2.39.3";

// Input validation interface
interface ApiSubmitRequest {
  auditId: string;
  responses: Record<string, unknown>;
  employeeMetadata?: Record<string, unknown>;
  participantIdHash?: string;
}

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

    // API key validation
    const apiKey = req.headers.get('X-API-Key');
    if (!apiKey) {
      throw new Error('Authentication required');
    }

    const { data: apiKeyData, error: keyError } = await supabase
      .from('api_keys')
      .select('*')
      .eq('api_key', apiKey)
      .eq('is_active', true)
      .single();

    if (keyError || !apiKeyData || !apiKeyData.is_active || 
        (apiKeyData.expires_at && new Date(apiKeyData.expires_at) < new Date())) {
      console.error('[api-submit-response] Auth failed:', {
        keyExists: !!apiKeyData,
        isActive: apiKeyData?.is_active,
        isExpired: apiKeyData?.expires_at ? new Date(apiKeyData.expires_at) < new Date() : false
      });
      throw new Error('Authentication failed');
    }

    // Parse and validate request body
    const body = await req.json();

    // Validate required fields
    if (!body.auditId || typeof body.auditId !== 'string') {
      return new Response(
        JSON.stringify({ error: 'Invalid request: auditId is required and must be a string' }),
        { status: 400, headers: { ...corsHeaders, 'Content-Type': 'application/json' } }
      );
    }

    if (!body.responses || typeof body.responses !== 'object') {
      return new Response(
        JSON.stringify({ error: 'Invalid request: responses is required and must be an object' }),
        { status: 400, headers: { ...corsHeaders, 'Content-Type': 'application/json' } }
      );
    }

    // UUID validation
    const uuidRegex = /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i;
    if (!uuidRegex.test(body.auditId)) {
      return new Response(
        JSON.stringify({ error: 'Invalid request: auditId must be a valid UUID' }),
        { status: 400, headers: { ...corsHeaders, 'Content-Type': 'application/json' } }
      );
    }

    // Validate participantIdHash length if provided
    if (body.participantIdHash && (typeof body.participantIdHash !== 'string' || body.participantIdHash.length > 255)) {
      return new Response(
        JSON.stringify({ error: 'Invalid request: participantIdHash must be a string with max 255 characters' }),
        { status: 400, headers: { ...corsHeaders, 'Content-Type': 'application/json' } }
      );
    }

    // Validate JSON payload size (max 50KB)
    const bodySize = JSON.stringify(body).length;
    if (bodySize > 50000) {
      return new Response(
        JSON.stringify({ error: 'Request too large: maximum 50KB allowed' }),
        { status: 413, headers: { ...corsHeaders, 'Content-Type': 'application/json' } }
      );
    }

    const { auditId, responses, employeeMetadata, participantIdHash } = body as ApiSubmitRequest;

    // Check if audit exists and is active
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

    // Generate a draw token if needed
    let drawToken = null;
    if (audit.draw_mode) {
      drawToken = crypto.randomUUID();
    }

    // Save the response
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

    // Update last_used_at timestamp
    await supabase
      .from('api_keys')
      .update({ last_used_at: new Date().toISOString() })
      .eq('id', apiKeyData.id);

    // Log the API call
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
    console.error('[api-submit-response] Error:', error);
    
    const errorMessage = error instanceof Error ? error.message : 'Unknown error occurred';
    let statusCode = 400;
    
    // Generic error messages to prevent information leakage
    if (errorMessage.includes('Authentication')) {
      statusCode = 401;
    } else if (errorMessage.includes('not found')) {
      statusCode = 404;
    } else if (errorMessage.includes('Request too large')) {
      statusCode = 413;
    }
    
    return new Response(
      JSON.stringify({ 
        error: 'OPERATION_FAILED',
        message: errorMessage
      }),
      {
        headers: { ...corsHeaders, 'Content-Type': 'application/json' },
        status: statusCode,
      }
    );
  }
});

-- Drop and recreate get_audit_for_survey function with lottery fields
DROP FUNCTION IF EXISTS public.get_audit_for_survey(text);

CREATE FUNCTION public.get_audit_for_survey(_access_token text)
RETURNS TABLE(
  id uuid, 
  questionnaire_id uuid, 
  program_name text, 
  logo_url text, 
  eap_program_url text, 
  custom_colors jsonb, 
  available_languages text[], 
  communication_text text, 
  is_active boolean, 
  expires_at timestamp with time zone,
  gift_id uuid,
  draw_mode text,
  closes_at timestamp with time zone
)
LANGUAGE sql
STABLE
SECURITY DEFINER
SET search_path TO 'public'
AS $$
  SELECT 
    a.id,
    a.questionnaire_id,
    a.program_name,
    a.logo_url,
    a.eap_program_url,
    a.custom_colors,
    a.available_languages,
    a.communication_text,
    a.is_active,
    a.expires_at,
    a.gift_id,
    a.draw_mode,
    a.closes_at
  FROM public.audits a
  WHERE a.access_token = _access_token
    AND a.is_active = true
    AND (a.expires_at IS NULL OR a.expires_at > now())
  LIMIT 1;
$$;

-- Grant execute permission to anonymous and authenticated users
GRANT EXECUTE ON FUNCTION public.get_audit_for_survey(text) TO anon, authenticated;
-- Drop and recreate the get_audit_for_survey function to include gift details
DROP FUNCTION IF EXISTS public.get_audit_for_survey(text);

CREATE OR REPLACE FUNCTION public.get_audit_for_survey(_access_token text)
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
  closes_at timestamp with time zone,
  gift_name text,
  gift_value_eur numeric,
  gift_description text,
  gift_image_url text
)
LANGUAGE sql
STABLE
SECURITY DEFINER
SET search_path = public
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
    a.closes_at,
    g.name as gift_name,
    g.value_eur as gift_value_eur,
    g.description as gift_description,
    g.image_url as gift_image_url
  FROM public.audits a
  LEFT JOIN public.gifts g ON a.gift_id = g.id
  WHERE a.access_token = _access_token
    AND a.is_active = true
    AND (a.expires_at IS NULL OR a.expires_at > now())
  LIMIT 1;
$$;
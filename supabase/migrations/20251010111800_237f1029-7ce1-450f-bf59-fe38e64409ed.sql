-- Fix function search_path issues for all existing functions
-- This ensures all security definer functions have immutable search_path

-- Re-create existing functions with explicit search_path
CREATE OR REPLACE FUNCTION public.has_role(_user_id uuid, _role app_role)
RETURNS boolean
LANGUAGE sql
STABLE
SECURITY DEFINER
SET search_path TO 'public'
AS $$
  SELECT EXISTS (
    SELECT 1
    FROM public.user_roles
    WHERE user_id = _user_id
      AND role = _role
  )
$$;

CREATE OR REPLACE FUNCTION public.get_user_role(_user_id uuid)
RETURNS app_role
LANGUAGE sql
STABLE
SECURITY DEFINER
SET search_path TO 'public'
AS $$
  SELECT role
  FROM public.user_roles
  WHERE user_id = _user_id
  LIMIT 1
$$;

CREATE OR REPLACE FUNCTION public.same_company_as_user(_profile_id uuid, _user_id uuid)
RETURNS boolean
LANGUAGE sql
STABLE
SECURITY DEFINER
SET search_path TO 'public'
AS $$
  SELECT EXISTS (
    SELECT 1
    FROM public.profiles p1
    INNER JOIN public.profiles p2 ON p1.company_name = p2.company_name
    WHERE p1.id = _user_id
      AND p2.id = _profile_id
      AND p1.company_name IS NOT NULL
      AND p2.company_name IS NOT NULL
  )
$$;

CREATE OR REPLACE FUNCTION public.user_owns_audit(_user_id uuid, _audit_id uuid)
RETURNS boolean
LANGUAGE sql
STABLE
SECURITY DEFINER
SET search_path TO 'public'
AS $$
  SELECT EXISTS (
    SELECT 1
    FROM public.audits
    WHERE id = _audit_id
      AND hr_user_id = _user_id
  )
$$;

CREATE OR REPLACE FUNCTION public.verify_email_token(_token text)
RETURNS TABLE(email text, verified boolean, expired boolean)
LANGUAGE sql
STABLE
SECURITY DEFINER
SET search_path TO 'public'
AS $$
  SELECT 
    ev.email,
    ev.verified,
    (ev.expires_at < now()) as expired
  FROM public.email_verifications ev
  WHERE ev.token = _token
  LIMIT 1;
$$;

CREATE OR REPLACE FUNCTION public.get_audit_for_survey(_access_token text)
RETURNS TABLE(id uuid, questionnaire_id uuid, program_name text, logo_url text, eap_program_url text, custom_colors jsonb, available_languages text[], communication_text text, is_active boolean, expires_at timestamp with time zone)
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
    a.expires_at
  FROM public.audits a
  WHERE a.access_token = _access_token
    AND a.is_active = true
    AND (a.expires_at IS NULL OR a.expires_at > now())
  LIMIT 1;
$$;

CREATE OR REPLACE FUNCTION public.increment_article_view_count(_article_id uuid)
RETURNS void
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path TO 'public'
AS $$
BEGIN
  UPDATE public.magazine_articles
  SET view_count = view_count + 1
  WHERE id = _article_id;
END;
$$;

CREATE OR REPLACE FUNCTION public.get_company_users(company_name_param text)
RETURNS TABLE(user_id uuid, email text, full_name text, role app_role)
LANGUAGE sql
STABLE
SECURITY DEFINER
SET search_path TO 'public'
AS $$
  SELECT 
    p.id as user_id,
    p.email,
    p.full_name,
    ur.role
  FROM public.profiles p
  LEFT JOIN public.user_roles ur ON p.id = ur.user_id
  WHERE p.company_name = company_name_param
$$;

-- Fix get_user_company_name (already has search_path but ensure it uses TO syntax)
CREATE OR REPLACE FUNCTION get_user_company_name(_user_id uuid)
RETURNS text
LANGUAGE sql
STABLE
SECURITY DEFINER
SET search_path TO 'public'
AS $$
  SELECT company_name
  FROM profiles
  WHERE id = _user_id
  LIMIT 1;
$$;
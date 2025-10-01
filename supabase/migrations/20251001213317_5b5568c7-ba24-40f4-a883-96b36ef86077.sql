-- Fix the generate_access_token function with correct schema reference
CREATE OR REPLACE FUNCTION public.generate_access_token()
RETURNS text
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path TO 'public', 'extensions'
AS $function$
DECLARE
  token text;
  token_exists boolean;
BEGIN
  LOOP
    -- Generate random 32-character token using pgcrypto extension
    token := encode(extensions.gen_random_bytes(24), 'base64');
    token := replace(token, '/', '_');
    token := replace(token, '+', '-');
    token := substring(token, 1, 32);
    
    -- Check if token already exists
    SELECT EXISTS(SELECT 1 FROM public.audits WHERE access_token = token) INTO token_exists;
    
    EXIT WHEN NOT token_exists;
  END LOOP;
  
  RETURN token;
END;
$function$;
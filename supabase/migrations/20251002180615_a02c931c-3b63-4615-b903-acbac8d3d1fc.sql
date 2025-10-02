-- Update access tokens for all active audits with new generated tokens
UPDATE public.audits 
SET access_token = generate_access_token()
WHERE is_active = true;
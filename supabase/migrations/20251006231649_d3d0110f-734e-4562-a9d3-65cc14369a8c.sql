-- Drop the overly permissive public read policy
DROP POLICY IF EXISTS "Anyone can read email verifications" ON public.email_verifications;

-- Create a security definer function that allows verification lookup ONLY by token
-- This prevents email harvesting while still allowing the verification flow to work
CREATE OR REPLACE FUNCTION public.verify_email_token(_token text)
RETURNS TABLE(email text, verified boolean, expired boolean)
LANGUAGE sql
STABLE
SECURITY DEFINER
SET search_path = public
AS $$
  SELECT 
    ev.email,
    ev.verified,
    (ev.expires_at < now()) as expired
  FROM public.email_verifications ev
  WHERE ev.token = _token
  LIMIT 1;
$$;

-- Optional: Allow authenticated users to check their own email verification status
-- This is useful if users need to see if their email is verified after logging in
CREATE POLICY "Users can check verification status for their own email"
ON public.email_verifications
FOR SELECT
USING (
  auth.uid() IS NOT NULL 
  AND email IN (
    SELECT email FROM auth.users WHERE id = auth.uid()
  )
);
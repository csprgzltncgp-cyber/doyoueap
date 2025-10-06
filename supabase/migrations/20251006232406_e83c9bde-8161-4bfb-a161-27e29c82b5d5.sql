-- Drop the insecure "Anyone can verify email" policy
DROP POLICY IF EXISTS "Anyone can verify email" ON public.email_verifications;

-- Create a secure function to verify email with proper token validation
CREATE OR REPLACE FUNCTION public.verify_email_with_token(_token text)
RETURNS jsonb
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = public
AS $$
DECLARE
  verification_record RECORD;
  result jsonb;
BEGIN
  -- Find the verification record by token
  SELECT * INTO verification_record
  FROM public.email_verifications
  WHERE token = _token
  LIMIT 1;
  
  -- Check if token exists
  IF verification_record.token IS NULL THEN
    RETURN jsonb_build_object(
      'success', false,
      'error', 'Invalid verification token'
    );
  END IF;
  
  -- Check if already verified
  IF verification_record.verified = true THEN
    RETURN jsonb_build_object(
      'success', true,
      'message', 'Email already verified',
      'email', verification_record.email
    );
  END IF;
  
  -- Check if expired
  IF verification_record.expires_at < now() THEN
    RETURN jsonb_build_object(
      'success', false,
      'error', 'Verification token has expired'
    );
  END IF;
  
  -- Update verification status
  UPDATE public.email_verifications
  SET verified = true
  WHERE token = _token;
  
  RETURN jsonb_build_object(
    'success', true,
    'message', 'Email verified successfully',
    'email', verification_record.email
  );
END;
$$;

-- Grant execute permission to anonymous and authenticated users
GRANT EXECUTE ON FUNCTION public.verify_email_with_token(text) TO anon, authenticated;

-- Add comment explaining the security model
COMMENT ON FUNCTION public.verify_email_with_token(text) IS 
'Securely verifies an email address using a token. This function validates the token, checks expiration, and updates the verification status. No direct UPDATE access is allowed on email_verifications table.';
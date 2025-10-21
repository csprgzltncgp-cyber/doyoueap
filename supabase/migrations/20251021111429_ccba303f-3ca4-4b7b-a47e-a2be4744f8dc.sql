-- Fix 1: Remove public SELECT access from email_verifications
DROP POLICY IF EXISTS "Anyone can check verification status" ON public.email_verifications;

-- The verify_email_with_token function already exists and is SECURITY DEFINER,
-- so verification will work through that function without exposing the table

-- Fix 2: Remove NULL check from admin_approval_requests policy that allows unauthenticated access
DROP POLICY IF EXISTS "Users can view their own approval requests" ON public.admin_approval_requests;

CREATE POLICY "Users can view their own approval requests"
  ON public.admin_approval_requests
  FOR SELECT
  USING (auth.uid() = user_id);

-- Create a table to track rate limiting for admin verification codes
CREATE TABLE IF NOT EXISTS public.admin_verification_rate_limit (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  email text NOT NULL,
  ip_address text,
  created_at timestamp with time zone NOT NULL DEFAULT now()
);

-- Enable RLS on rate limit table (service role will bypass this)
ALTER TABLE public.admin_verification_rate_limit ENABLE ROW LEVEL SECURITY;

-- No public access to rate limit table
CREATE POLICY "No direct access to rate limit table"
  ON public.admin_verification_rate_limit
  FOR ALL
  USING (false);
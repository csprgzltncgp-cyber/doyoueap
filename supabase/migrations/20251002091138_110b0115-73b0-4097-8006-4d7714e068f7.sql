-- Clean up existing data for fresh start
DELETE FROM public.audit_responses;
DELETE FROM public.audits;
DELETE FROM public.user_roles;
DELETE FROM public.profiles;

-- Create email verifications table for pre-registration email validation
CREATE TABLE public.email_verifications (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  email TEXT NOT NULL,
  token TEXT NOT NULL UNIQUE,
  verified BOOLEAN DEFAULT FALSE,
  expires_at TIMESTAMP WITH TIME ZONE NOT NULL,
  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

ALTER TABLE public.email_verifications ENABLE ROW LEVEL SECURITY;

-- Anyone can request email verification (before registration)
CREATE POLICY "Anyone can request email verification"
  ON public.email_verifications
  FOR INSERT
  WITH CHECK (true);

-- Anyone can read verifications (to check status)
CREATE POLICY "Anyone can read email verifications"
  ON public.email_verifications
  FOR SELECT
  USING (true);

-- Anyone can update to verify their email
CREATE POLICY "Anyone can verify email"
  ON public.email_verifications
  FOR UPDATE
  USING (true);
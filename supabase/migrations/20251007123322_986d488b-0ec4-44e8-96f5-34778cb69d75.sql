-- Create admin approval requests table
CREATE TABLE IF NOT EXISTS public.admin_approval_requests (
  id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
  user_id UUID NOT NULL,
  email TEXT NOT NULL,
  full_name TEXT NOT NULL,
  approval_token TEXT NOT NULL UNIQUE,
  approved BOOLEAN DEFAULT FALSE,
  approved_at TIMESTAMP WITH TIME ZONE,
  expires_at TIMESTAMP WITH TIME ZONE NOT NULL,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT timezone('utc'::text, now())
);

-- Create admin verification codes table
CREATE TABLE IF NOT EXISTS public.admin_verification_codes (
  id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
  email TEXT NOT NULL,
  code TEXT NOT NULL,
  used BOOLEAN DEFAULT FALSE,
  used_at TIMESTAMP WITH TIME ZONE,
  expires_at TIMESTAMP WITH TIME ZONE NOT NULL,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT timezone('utc'::text, now())
);

-- Enable RLS
ALTER TABLE public.admin_approval_requests ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.admin_verification_codes ENABLE ROW LEVEL SECURITY;

-- RLS Policies for admin_approval_requests
CREATE POLICY "Admins can view all approval requests"
  ON public.admin_approval_requests
  FOR SELECT
  USING (has_role(auth.uid(), 'admin'));

-- RLS Policies for admin_verification_codes  
CREATE POLICY "No direct access to verification codes"
  ON public.admin_verification_codes
  FOR ALL
  USING (false);

-- Indexes for better performance
CREATE INDEX idx_admin_approval_token ON public.admin_approval_requests(approval_token);
CREATE INDEX idx_admin_approval_user_id ON public.admin_approval_requests(user_id);
CREATE INDEX idx_verification_email_code ON public.admin_verification_codes(email, code);

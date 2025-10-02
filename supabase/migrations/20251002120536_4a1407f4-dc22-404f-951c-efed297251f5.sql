-- Add notification emails table
CREATE TABLE public.notification_emails (
  id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
  user_id UUID NOT NULL REFERENCES auth.users(id) ON DELETE CASCADE,
  email TEXT NOT NULL,
  is_verified BOOLEAN DEFAULT false,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT timezone('utc'::text, now()),
  UNIQUE(user_id, email)
);

-- Enable RLS for notification_emails
ALTER TABLE public.notification_emails ENABLE ROW LEVEL SECURITY;

-- Policies for notification_emails
CREATE POLICY "Users can view their own notification emails"
ON public.notification_emails FOR SELECT
USING (auth.uid() = user_id);

CREATE POLICY "Users can insert their own notification emails"
ON public.notification_emails FOR INSERT
WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Users can update their own notification emails"
ON public.notification_emails FOR UPDATE
USING (auth.uid() = user_id);

CREATE POLICY "Users can delete their own notification emails"
ON public.notification_emails FOR DELETE
USING (auth.uid() = user_id);

-- Add user invitations table
CREATE TABLE public.user_invitations (
  id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
  invited_by UUID NOT NULL REFERENCES auth.users(id) ON DELETE CASCADE,
  company_id UUID NOT NULL,
  email TEXT NOT NULL,
  role app_role NOT NULL DEFAULT 'hr',
  token TEXT NOT NULL UNIQUE,
  expires_at TIMESTAMP WITH TIME ZONE NOT NULL,
  accepted BOOLEAN DEFAULT false,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT timezone('utc'::text, now())
);

-- Enable RLS for user_invitations
ALTER TABLE public.user_invitations ENABLE ROW LEVEL SECURITY;

-- Policies for user_invitations
CREATE POLICY "Users can view invitations they sent"
ON public.user_invitations FOR SELECT
USING (auth.uid() = invited_by);

CREATE POLICY "HR and Admins can create invitations"
ON public.user_invitations FOR INSERT
WITH CHECK (has_role(auth.uid(), 'hr') OR has_role(auth.uid(), 'admin'));

-- Add new columns to profiles table
ALTER TABLE public.profiles 
ADD COLUMN IF NOT EXISTS data_retention_days INTEGER DEFAULT 365,
ADD COLUMN IF NOT EXISTS preferred_languages TEXT[] DEFAULT ARRAY['HU'];

-- Create function to get company users
CREATE OR REPLACE FUNCTION public.get_company_users(company_name_param TEXT)
RETURNS TABLE (
  user_id UUID,
  email TEXT,
  full_name TEXT,
  role app_role
) 
LANGUAGE sql
STABLE
SECURITY DEFINER
SET search_path = public
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
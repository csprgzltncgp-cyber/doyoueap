-- First, update all existing 'user' roles to 'hr'
UPDATE public.user_roles SET role = 'hr' WHERE role = 'user';

-- Drop dependent functions and policies
DROP FUNCTION IF EXISTS public.has_role(uuid, app_role) CASCADE;
DROP FUNCTION IF EXISTS public.get_user_role(uuid) CASCADE;

-- Now drop and recreate enum
ALTER TYPE public.app_role RENAME TO app_role_old;
CREATE TYPE public.app_role AS ENUM ('admin', 'hr');

-- Update user_roles table to use new enum
ALTER TABLE public.user_roles ALTER COLUMN role TYPE public.app_role USING role::text::public.app_role;

-- Drop old enum with CASCADE
DROP TYPE public.app_role_old CASCADE;

-- Recreate has_role function
CREATE OR REPLACE FUNCTION public.has_role(_user_id uuid, _role app_role)
RETURNS boolean
LANGUAGE sql
STABLE
SECURITY DEFINER
SET search_path = public
AS $$
  SELECT EXISTS (
    SELECT 1
    FROM public.user_roles
    WHERE user_id = _user_id
      AND role = _role
  )
$$;

-- Recreate get_user_role function
CREATE OR REPLACE FUNCTION public.get_user_role(_user_id uuid)
RETURNS app_role
LANGUAGE sql
STABLE
SECURITY DEFINER
SET search_path = public
AS $$
  SELECT role
  FROM public.user_roles
  WHERE user_id = _user_id
  LIMIT 1
$$;

-- Update default role to 'hr' in trigger
CREATE OR REPLACE FUNCTION public.handle_new_user()
RETURNS trigger
LANGUAGE plpgsql
SECURITY DEFINER SET search_path = public
AS $$
BEGIN
  INSERT INTO public.profiles (id, email, full_name)
  VALUES (
    new.id,
    new.email,
    COALESCE(new.raw_user_meta_data->>'full_name', '')
  );
  
  -- Assign default 'hr' role (only HR can register)
  INSERT INTO public.user_roles (user_id, role)
  VALUES (new.id, 'hr');
  
  RETURN new;
END;
$$;

-- Recreate RLS Policies for profiles
DROP POLICY IF EXISTS "Admins can view all profiles" ON public.profiles;
DROP POLICY IF EXISTS "HR can view all profiles" ON public.profiles;

CREATE POLICY "Admins can view all profiles"
  ON public.profiles FOR SELECT
  TO authenticated
  USING (public.has_role(auth.uid(), 'admin'));

CREATE POLICY "HR can view all profiles"
  ON public.profiles FOR SELECT
  TO authenticated
  USING (public.has_role(auth.uid(), 'hr'));

-- Recreate RLS Policies for user_roles
DROP POLICY IF EXISTS "Admins can view all roles" ON public.user_roles;
DROP POLICY IF EXISTS "Admins can insert roles" ON public.user_roles;
DROP POLICY IF EXISTS "Admins can update roles" ON public.user_roles;
DROP POLICY IF EXISTS "Admins can delete roles" ON public.user_roles;

CREATE POLICY "Admins can view all roles"
  ON public.user_roles FOR SELECT
  TO authenticated
  USING (public.has_role(auth.uid(), 'admin'));

CREATE POLICY "Admins can insert roles"
  ON public.user_roles FOR INSERT
  TO authenticated
  WITH CHECK (public.has_role(auth.uid(), 'admin'));

CREATE POLICY "Admins can update roles"
  ON public.user_roles FOR UPDATE
  TO authenticated
  USING (public.has_role(auth.uid(), 'admin'));

CREATE POLICY "Admins can delete roles"
  ON public.user_roles FOR DELETE
  TO authenticated
  USING (public.has_role(auth.uid(), 'admin'));

-- Create questionnaires table (admin manages these)
CREATE TABLE public.questionnaires (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  title text NOT NULL,
  description text,
  questions jsonb NOT NULL DEFAULT '[]'::jsonb,
  is_active boolean DEFAULT true,
  created_at timestamp with time zone DEFAULT timezone('utc'::text, now()) NOT NULL,
  updated_at timestamp with time zone DEFAULT timezone('utc'::text, now()) NOT NULL
);

ALTER TABLE public.questionnaires ENABLE ROW LEVEL SECURITY;

-- Create audits table (HR creates audits)
CREATE TABLE public.audits (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  hr_user_id uuid REFERENCES auth.users(id) ON DELETE CASCADE NOT NULL,
  company_name text NOT NULL,
  questionnaire_id uuid REFERENCES public.questionnaires(id) ON DELETE CASCADE NOT NULL,
  access_token text UNIQUE NOT NULL,
  is_active boolean DEFAULT true,
  expires_at timestamp with time zone,
  created_at timestamp with time zone DEFAULT timezone('utc'::text, now()) NOT NULL,
  updated_at timestamp with time zone DEFAULT timezone('utc'::text, now()) NOT NULL
);

ALTER TABLE public.audits ENABLE ROW LEVEL SECURITY;

-- Create audit_responses table (employee responses via public link)
CREATE TABLE public.audit_responses (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  audit_id uuid REFERENCES public.audits(id) ON DELETE CASCADE NOT NULL,
  responses jsonb NOT NULL DEFAULT '{}'::jsonb,
  employee_metadata jsonb DEFAULT '{}'::jsonb,
  submitted_at timestamp with time zone DEFAULT timezone('utc'::text, now()) NOT NULL
);

ALTER TABLE public.audit_responses ENABLE ROW LEVEL SECURITY;

-- RLS Policies for questionnaires
CREATE POLICY "Admins can manage questionnaires"
  ON public.questionnaires FOR ALL
  TO authenticated
  USING (public.has_role(auth.uid(), 'admin'));

CREATE POLICY "HR can view questionnaires"
  ON public.questionnaires FOR SELECT
  TO authenticated
  USING (public.has_role(auth.uid(), 'hr'));

-- RLS Policies for audits
CREATE POLICY "HR can view their own audits"
  ON public.audits FOR SELECT
  TO authenticated
  USING (hr_user_id = auth.uid());

CREATE POLICY "HR can create audits"
  ON public.audits FOR INSERT
  TO authenticated
  WITH CHECK (public.has_role(auth.uid(), 'hr') AND hr_user_id = auth.uid());

CREATE POLICY "HR can update their own audits"
  ON public.audits FOR UPDATE
  TO authenticated
  USING (hr_user_id = auth.uid());

CREATE POLICY "Admins can view all audits"
  ON public.audits FOR SELECT
  TO authenticated
  USING (public.has_role(auth.uid(), 'admin'));

-- RLS Policies for audit_responses (PUBLIC access via token validation)
CREATE POLICY "Anyone can insert responses"
  ON public.audit_responses FOR INSERT
  TO anon, authenticated
  WITH CHECK (true);

CREATE POLICY "HR can view responses for their audits"
  ON public.audit_responses FOR SELECT
  TO authenticated
  USING (
    EXISTS (
      SELECT 1 FROM public.audits
      WHERE audits.id = audit_responses.audit_id
      AND audits.hr_user_id = auth.uid()
    )
  );

CREATE POLICY "Admins can view all responses"
  ON public.audit_responses FOR SELECT
  TO authenticated
  USING (public.has_role(auth.uid(), 'admin'));

-- Function to generate unique access token
CREATE OR REPLACE FUNCTION public.generate_access_token()
RETURNS text
LANGUAGE plpgsql
AS $$
DECLARE
  token text;
  token_exists boolean;
BEGIN
  LOOP
    -- Generate random 32-character token
    token := encode(gen_random_bytes(24), 'base64');
    token := replace(token, '/', '_');
    token := replace(token, '+', '-');
    token := substring(token, 1, 32);
    
    -- Check if token already exists
    SELECT EXISTS(SELECT 1 FROM public.audits WHERE access_token = token) INTO token_exists;
    
    EXIT WHEN NOT token_exists;
  END LOOP;
  
  RETURN token;
END;
$$;
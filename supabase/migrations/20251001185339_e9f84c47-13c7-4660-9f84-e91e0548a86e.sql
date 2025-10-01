-- Create questionnaires table (if not exists)
CREATE TABLE IF NOT EXISTS public.questionnaires (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  title text NOT NULL,
  description text,
  questions jsonb NOT NULL DEFAULT '[]'::jsonb,
  is_active boolean DEFAULT true,
  created_at timestamp with time zone DEFAULT timezone('utc'::text, now()) NOT NULL,
  updated_at timestamp with time zone DEFAULT timezone('utc'::text, now()) NOT NULL
);

ALTER TABLE public.questionnaires ENABLE ROW LEVEL SECURITY;

-- Create audits table (if not exists)
CREATE TABLE IF NOT EXISTS public.audits (
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

-- Create audit_responses table (if not exists)
CREATE TABLE IF NOT EXISTS public.audit_responses (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  audit_id uuid REFERENCES public.audits(id) ON DELETE CASCADE NOT NULL,
  responses jsonb NOT NULL DEFAULT '{}'::jsonb,
  employee_metadata jsonb DEFAULT '{}'::jsonb,
  submitted_at timestamp with time zone DEFAULT timezone('utc'::text, now()) NOT NULL
);

ALTER TABLE public.audit_responses ENABLE ROW LEVEL SECURITY;

-- RLS Policies for questionnaires (only if not exists)
DO $$ 
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM pg_policies WHERE tablename = 'questionnaires' AND policyname = 'Admins can manage questionnaires'
  ) THEN
    CREATE POLICY "Admins can manage questionnaires"
      ON public.questionnaires FOR ALL
      TO authenticated
      USING (public.has_role(auth.uid(), 'admin'));
  END IF;
END $$;

DO $$ 
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM pg_policies WHERE tablename = 'questionnaires' AND policyname = 'HR can view questionnaires'
  ) THEN
    CREATE POLICY "HR can view questionnaires"
      ON public.questionnaires FOR SELECT
      TO authenticated
      USING (public.has_role(auth.uid(), 'hr'));
  END IF;
END $$;

-- RLS Policies for audits
DO $$ 
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM pg_policies WHERE tablename = 'audits' AND policyname = 'HR can view their own audits'
  ) THEN
    CREATE POLICY "HR can view their own audits"
      ON public.audits FOR SELECT
      TO authenticated
      USING (hr_user_id = auth.uid());
  END IF;
END $$;

DO $$ 
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM pg_policies WHERE tablename = 'audits' AND policyname = 'HR can create audits'
  ) THEN
    CREATE POLICY "HR can create audits"
      ON public.audits FOR INSERT
      TO authenticated
      WITH CHECK (public.has_role(auth.uid(), 'hr') AND hr_user_id = auth.uid());
  END IF;
END $$;

DO $$ 
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM pg_policies WHERE tablename = 'audits' AND policyname = 'HR can update their own audits'
  ) THEN
    CREATE POLICY "HR can update their own audits"
      ON public.audits FOR UPDATE
      TO authenticated
      USING (hr_user_id = auth.uid());
  END IF;
END $$;

DO $$ 
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM pg_policies WHERE tablename = 'audits' AND policyname = 'Admins can view all audits'
  ) THEN
    CREATE POLICY "Admins can view all audits"
      ON public.audits FOR SELECT
      TO authenticated
      USING (public.has_role(auth.uid(), 'admin'));
  END IF;
END $$;

-- RLS Policies for audit_responses (PUBLIC access)
DO $$ 
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM pg_policies WHERE tablename = 'audit_responses' AND policyname = 'Anyone can insert responses'
  ) THEN
    CREATE POLICY "Anyone can insert responses"
      ON public.audit_responses FOR INSERT
      TO anon, authenticated
      WITH CHECK (true);
  END IF;
END $$;

DO $$ 
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM pg_policies WHERE tablename = 'audit_responses' AND policyname = 'HR can view responses for their audits'
  ) THEN
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
  END IF;
END $$;

DO $$ 
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM pg_policies WHERE tablename = 'audit_responses' AND policyname = 'Admins can view all responses'
  ) THEN
    CREATE POLICY "Admins can view all responses"
      ON public.audit_responses FOR SELECT
      TO authenticated
      USING (public.has_role(auth.uid(), 'admin'));
  END IF;
END $$;

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

-- Update trigger to set new users as 'hr' by default
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
-- Add 'partner' package type support (already exists in code, just documenting)
-- profiles.selected_package can now be: 'starter', 'professional', 'enterprise', 'partner'

-- Add partner_user_id to companies table
ALTER TABLE public.companies
ADD COLUMN partner_user_id uuid REFERENCES auth.users(id) ON DELETE SET NULL;

-- Create index for better performance
CREATE INDEX idx_companies_partner_user_id ON public.companies(partner_user_id);

-- Add partner_company_id to audits table
ALTER TABLE public.audits
ADD COLUMN partner_company_id uuid REFERENCES public.companies(id) ON DELETE SET NULL;

-- Create index for better performance
CREATE INDEX idx_audits_partner_company_id ON public.audits(partner_company_id);

-- Update RLS policies for companies table
-- Partners can view their managed companies
CREATE POLICY "Partners can view their managed companies"
ON public.companies
FOR SELECT
USING (
  has_role(auth.uid(), 'hr'::app_role) 
  AND partner_user_id = auth.uid()
);

-- Partners can create new companies
CREATE POLICY "Partners can create companies"
ON public.companies
FOR INSERT
WITH CHECK (
  has_role(auth.uid(), 'hr'::app_role) 
  AND partner_user_id = auth.uid()
);

-- Partners can update their managed companies
CREATE POLICY "Partners can update their managed companies"
ON public.companies
FOR UPDATE
USING (
  has_role(auth.uid(), 'hr'::app_role) 
  AND partner_user_id = auth.uid()
);

-- Partners can delete their managed companies
CREATE POLICY "Partners can delete their managed companies"
ON public.companies
FOR DELETE
USING (
  has_role(auth.uid(), 'hr'::app_role) 
  AND partner_user_id = auth.uid()
);

-- Update RLS policies for audits table
-- Partners can view audits for their managed companies
CREATE POLICY "Partners can view audits for managed companies"
ON public.audits
FOR SELECT
USING (
  has_role(auth.uid(), 'hr'::app_role) 
  AND partner_company_id IN (
    SELECT id FROM public.companies WHERE partner_user_id = auth.uid()
  )
);

-- Partners can create audits for their managed companies
CREATE POLICY "Partners can create audits for managed companies"
ON public.audits
FOR INSERT
WITH CHECK (
  has_role(auth.uid(), 'hr'::app_role) 
  AND hr_user_id = auth.uid()
  AND (
    partner_company_id IS NULL 
    OR partner_company_id IN (
      SELECT id FROM public.companies WHERE partner_user_id = auth.uid()
    )
  )
);

-- Partners can update audits for their managed companies
CREATE POLICY "Partners can update audits for managed companies"
ON public.audits
FOR UPDATE
USING (
  has_role(auth.uid(), 'hr'::app_role) 
  AND hr_user_id = auth.uid()
  AND (
    partner_company_id IS NULL 
    OR partner_company_id IN (
      SELECT id FROM public.companies WHERE partner_user_id = auth.uid()
    )
  )
);
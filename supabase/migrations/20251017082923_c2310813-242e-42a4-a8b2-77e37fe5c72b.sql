-- Remove global unique constraint on company_name
ALTER TABLE public.companies DROP CONSTRAINT IF EXISTS companies_company_name_key;

-- Add composite unique constraint for partner_user_id + company_name
-- This allows different partners to have clients with the same company name
-- but prevents one partner from having duplicate company names
ALTER TABLE public.companies 
ADD CONSTRAINT companies_partner_company_unique 
UNIQUE (partner_user_id, company_name);
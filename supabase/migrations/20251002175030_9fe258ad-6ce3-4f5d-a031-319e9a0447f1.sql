-- Make company_name nullable in audits table
ALTER TABLE public.audits ALTER COLUMN company_name DROP NOT NULL;
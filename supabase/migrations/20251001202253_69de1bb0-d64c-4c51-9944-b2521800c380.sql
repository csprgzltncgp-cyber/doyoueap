-- Add logo_url and eap_program_url columns to audits table
ALTER TABLE public.audits 
ADD COLUMN logo_url TEXT,
ADD COLUMN eap_program_url TEXT DEFAULT 'https://doyoueap.hu';
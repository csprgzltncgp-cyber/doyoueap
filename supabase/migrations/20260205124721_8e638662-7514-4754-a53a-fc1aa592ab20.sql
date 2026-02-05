-- Add laravel_company_id column to profiles for linking to external Laravel system
ALTER TABLE public.profiles ADD COLUMN IF NOT EXISTS laravel_company_id integer;

-- Update the guest user's profile to link to company ID 24
UPDATE public.profiles 
SET laravel_company_id = 24 
WHERE email = 'guest@doyoueap.com';
-- Add company and billing information columns to profiles table
ALTER TABLE public.profiles
ADD COLUMN IF NOT EXISTS country TEXT,
ADD COLUMN IF NOT EXISTS vat_id TEXT,
ADD COLUMN IF NOT EXISTS address TEXT,
ADD COLUMN IF NOT EXISTS city TEXT,
ADD COLUMN IF NOT EXISTS postal_code TEXT,
ADD COLUMN IF NOT EXISTS industry TEXT,
ADD COLUMN IF NOT EXISTS employee_count TEXT,
ADD COLUMN IF NOT EXISTS contact_phone TEXT,
ADD COLUMN IF NOT EXISTS company_domain TEXT,
ADD COLUMN IF NOT EXISTS billing_address_same_as_company BOOLEAN DEFAULT true,
ADD COLUMN IF NOT EXISTS billing_address TEXT,
ADD COLUMN IF NOT EXISTS billing_city TEXT,
ADD COLUMN IF NOT EXISTS billing_postal_code TEXT,
ADD COLUMN IF NOT EXISTS selected_package TEXT,
ADD COLUMN IF NOT EXISTS billing_cycle TEXT DEFAULT 'yearly';
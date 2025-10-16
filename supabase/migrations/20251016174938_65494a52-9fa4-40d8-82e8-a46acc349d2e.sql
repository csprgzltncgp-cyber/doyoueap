-- Create industries table
CREATE TABLE public.industries (
  id uuid NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
  name text NOT NULL UNIQUE,
  is_active boolean NOT NULL DEFAULT true,
  created_at timestamp with time zone NOT NULL DEFAULT timezone('utc'::text, now()),
  updated_at timestamp with time zone NOT NULL DEFAULT timezone('utc'::text, now())
);

-- Enable RLS
ALTER TABLE public.industries ENABLE ROW LEVEL SECURITY;

-- RLS policies for industries
CREATE POLICY "Admins can manage industries"
ON public.industries
FOR ALL
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

CREATE POLICY "HR and Partners can view active industries"
ON public.industries
FOR SELECT
USING (has_role(auth.uid(), 'hr'::app_role) AND is_active = true);

-- Add industry_id column to companies table
ALTER TABLE public.companies 
ADD COLUMN industry_id uuid REFERENCES public.industries(id);

-- Migrate existing industry data (optional - copy text to a default industry)
-- This helps preserve existing data
INSERT INTO public.industries (name, is_active)
SELECT DISTINCT industry, true
FROM public.companies
WHERE industry IS NOT NULL AND industry != ''
ON CONFLICT (name) DO NOTHING;

-- Update companies to reference the new industry_id
UPDATE public.companies c
SET industry_id = i.id
FROM public.industries i
WHERE c.industry = i.name;

-- Remove old industry text column
ALTER TABLE public.companies DROP COLUMN industry;

-- Add some default industries
INSERT INTO public.industries (name, is_active) VALUES
  ('IT / Technológia', true),
  ('Pénzügy / Banki szektor', true),
  ('Egészségügy', true),
  ('Oktatás', true),
  ('Kereskedelem / Retail', true),
  ('Gyártás / Termelés', true),
  ('Szolgáltatások', true),
  ('Telekommunikáció', true),
  ('Energetika', true),
  ('Ingatlan', true),
  ('Média / Marketing', true),
  ('Turizmus / Vendéglátás', true),
  ('Közigazgatás', true),
  ('Nonprofit szektor', true),
  ('Egyéb', true)
ON CONFLICT (name) DO NOTHING;

-- Update trigger for industries
CREATE TRIGGER update_industries_updated_at
BEFORE UPDATE ON public.industries
FOR EACH ROW
EXECUTE FUNCTION public.update_updated_at_column();
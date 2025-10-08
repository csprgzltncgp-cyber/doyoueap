-- Create newsletter templates table for customizable email templates
CREATE TABLE IF NOT EXISTS public.newsletter_templates (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  name text NOT NULL,
  header_title text NOT NULL DEFAULT 'DoYouEAP Hírlevél',
  header_subtitle text,
  footer_text text NOT NULL DEFAULT 'Ez egy automatikus üzenet. Kérjük, ne válaszoljon erre az emailre.',
  footer_company text NOT NULL DEFAULT 'DoYouEAP',
  footer_address text,
  button_text text NOT NULL DEFAULT 'Tovább a cikkhez',
  button_color text NOT NULL DEFAULT '#0ea5e9',
  primary_color text NOT NULL DEFAULT '#0ea5e9',
  background_color text NOT NULL DEFAULT '#f8fafc',
  is_default boolean DEFAULT false,
  created_at timestamp with time zone NOT NULL DEFAULT now(),
  updated_at timestamp with time zone NOT NULL DEFAULT now(),
  created_by uuid REFERENCES auth.users(id) ON DELETE SET NULL
);

-- Enable RLS
ALTER TABLE public.newsletter_templates ENABLE ROW LEVEL SECURITY;

-- RLS policies
CREATE POLICY "Admins can manage templates"
ON public.newsletter_templates
FOR ALL
TO authenticated
USING (has_role(auth.uid(), 'admin'::app_role));

CREATE POLICY "Admins can view templates"
ON public.newsletter_templates
FOR SELECT
TO authenticated
USING (has_role(auth.uid(), 'admin'::app_role));

-- Add template_id to newsletter_campaigns
ALTER TABLE public.newsletter_campaigns
ADD COLUMN IF NOT EXISTS template_id uuid REFERENCES public.newsletter_templates(id) ON DELETE SET NULL;

-- Create default template
INSERT INTO public.newsletter_templates (
  name,
  header_title,
  header_subtitle,
  footer_text,
  footer_company,
  button_text,
  button_color,
  primary_color,
  background_color,
  is_default
) VALUES (
  'Alapértelmezett sablon',
  'DoYouEAP Hírlevél',
  'Hírek és információk az EAP világából',
  'Ez egy automatikus üzenet. Kérjük, ne válaszoljon erre az emailre.',
  'DoYouEAP',
  'Tovább a cikkhez',
  '#0ea5e9',
  '#0ea5e9',
  '#f8fafc',
  true
);
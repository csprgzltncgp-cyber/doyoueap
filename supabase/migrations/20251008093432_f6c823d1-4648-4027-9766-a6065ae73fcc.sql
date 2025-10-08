-- Add missing fields to newsletter_templates table
ALTER TABLE public.newsletter_templates
ADD COLUMN IF NOT EXISTS greeting_text text NOT NULL DEFAULT 'Kedves Feliratkozónk!',
ADD COLUMN IF NOT EXISTS footer_links jsonb DEFAULT '[]'::jsonb,
ADD COLUMN IF NOT EXISTS header_color text NOT NULL DEFAULT '#0ea5e9',
ADD COLUMN IF NOT EXISTS footer_color text NOT NULL DEFAULT '#1a1a1a',
ADD COLUMN IF NOT EXISTS header_gradient text,
ADD COLUMN IF NOT EXISTS button_gradient text,
ADD COLUMN IF NOT EXISTS footer_gradient text,
ADD COLUMN IF NOT EXISTS cta_button_url text,
ADD COLUMN IF NOT EXISTS show_cta_button boolean DEFAULT true,
ADD COLUMN IF NOT EXISTS extra_content text;

-- Remove the old primary_color column as we now have header_color, button_color, footer_color
-- We'll keep it for backwards compatibility but won't use it in the new system

COMMENT ON COLUMN public.newsletter_templates.greeting_text IS 'Megszólítás szövege a hírlevélben';
COMMENT ON COLUMN public.newsletter_templates.footer_links IS 'Lábléc linkek tömbje: [{"text": "Link szöveg", "url": "https://..."}]';
COMMENT ON COLUMN public.newsletter_templates.header_color IS 'Fejléc színe';
COMMENT ON COLUMN public.newsletter_templates.footer_color IS 'Lábléc színe';
COMMENT ON COLUMN public.newsletter_templates.header_gradient IS 'Fejléc színátmenet (opcionális)';
COMMENT ON COLUMN public.newsletter_templates.button_gradient IS 'Gomb színátmenet (opcionális)';
COMMENT ON COLUMN public.newsletter_templates.footer_gradient IS 'Lábléc színátmenet (opcionális)';
COMMENT ON COLUMN public.newsletter_templates.extra_content IS 'Extra tartalom (pl. EAP Pulse szöveg)';
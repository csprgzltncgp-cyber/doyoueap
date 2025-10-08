-- Add button text color and shadow color fields to newsletter_templates
ALTER TABLE public.newsletter_templates
ADD COLUMN button_text_color text NOT NULL DEFAULT '#ffffff',
ADD COLUMN button_shadow_color text NOT NULL DEFAULT '#0ea5e9';
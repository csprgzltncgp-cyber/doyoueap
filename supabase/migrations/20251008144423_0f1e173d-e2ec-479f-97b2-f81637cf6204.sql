-- Add greeting text color and background color fields to newsletter_templates
ALTER TABLE public.newsletter_templates
ADD COLUMN IF NOT EXISTS greeting_text_color text DEFAULT '#ffffff',
ADD COLUMN IF NOT EXISTS greeting_bg_color text DEFAULT '#0ea5e9';
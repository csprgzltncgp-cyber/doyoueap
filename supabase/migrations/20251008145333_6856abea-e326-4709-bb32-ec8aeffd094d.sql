-- Add content text styling fields to newsletter_templates
ALTER TABLE public.newsletter_templates
ADD COLUMN IF NOT EXISTS content_text_color text DEFAULT '#4a5568',
ADD COLUMN IF NOT EXISTS content_text_size text DEFAULT '16px',
ADD COLUMN IF NOT EXISTS extra_content_text_color text DEFAULT '#4a5568',
ADD COLUMN IF NOT EXISTS extra_content_text_size text DEFAULT '16px';
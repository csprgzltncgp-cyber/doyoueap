-- Add image fields to newsletter_templates table
ALTER TABLE public.newsletter_templates
ADD COLUMN logo_url text,
ADD COLUMN featured_image_url text,
ADD COLUMN footer_logo_url text;
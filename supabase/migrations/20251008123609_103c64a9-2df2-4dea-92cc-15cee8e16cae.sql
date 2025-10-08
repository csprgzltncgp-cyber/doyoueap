-- Add new fields for footer link color and extra content colors
ALTER TABLE newsletter_templates
ADD COLUMN IF NOT EXISTS footer_link_color text NOT NULL DEFAULT '#ffffff',
ADD COLUMN IF NOT EXISTS extra_content_border_color text NOT NULL DEFAULT '#0ea5e9',
ADD COLUMN IF NOT EXISTS extra_content_bg_color text NOT NULL DEFAULT '#0ea5e915';
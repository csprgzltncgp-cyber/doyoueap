-- Add separate gradient fields for header sections
ALTER TABLE newsletter_templates 
ADD COLUMN IF NOT EXISTS header_gradient_1 text,
ADD COLUMN IF NOT EXISTS header_gradient_2 text;

-- Remove the old combined header_gradient field
ALTER TABLE newsletter_templates 
DROP COLUMN IF EXISTS header_gradient;
-- Add header_color_1 and header_color_2 columns to newsletter_templates table
ALTER TABLE public.newsletter_templates 
ADD COLUMN IF NOT EXISTS header_color_1 TEXT NOT NULL DEFAULT '#0ea5e9',
ADD COLUMN IF NOT EXISTS header_color_2 TEXT NOT NULL DEFAULT '#0ea5e9';

-- Update existing records to use header_color as default for both new columns
UPDATE public.newsletter_templates
SET header_color_1 = COALESCE(header_color, '#0ea5e9'),
    header_color_2 = COALESCE(header_color, '#0ea5e9')
WHERE header_color_1 IS NULL OR header_color_2 IS NULL;
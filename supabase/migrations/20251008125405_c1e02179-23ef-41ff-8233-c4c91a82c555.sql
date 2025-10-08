-- Remove all button-related fields from newsletter_templates
ALTER TABLE newsletter_templates
DROP COLUMN IF EXISTS button_text,
DROP COLUMN IF EXISTS button_text_color,
DROP COLUMN IF EXISTS button_color,
DROP COLUMN IF EXISTS button_shadow_color,
DROP COLUMN IF EXISTS button_gradient,
DROP COLUMN IF EXISTS cta_button_url,
DROP COLUMN IF EXISTS show_cta_button;
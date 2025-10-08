-- Add button fields for content button
ALTER TABLE public.newsletter_templates
ADD COLUMN content_button_text text DEFAULT 'Olvass tovább',
ADD COLUMN content_button_text_color text DEFAULT '#ffffff',
ADD COLUMN content_button_color text DEFAULT '#0ea5e9',
ADD COLUMN content_button_shadow_color text DEFAULT '#0ea5e933',
ADD COLUMN content_button_url text,
ADD COLUMN show_content_button boolean DEFAULT false;

-- Add button fields for extra content button
ALTER TABLE public.newsletter_templates
ADD COLUMN extra_button_text text DEFAULT 'További információ',
ADD COLUMN extra_button_text_color text DEFAULT '#ffffff',
ADD COLUMN extra_button_color text DEFAULT '#0ea5e9',
ADD COLUMN extra_button_shadow_color text DEFAULT '#0ea5e933',
ADD COLUMN extra_button_url text,
ADD COLUMN show_extra_button boolean DEFAULT false;